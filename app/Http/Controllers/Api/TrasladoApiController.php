<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Traslado;
use App\Models\MotivoMovimiento;
use App\Models\InventarioMovimiento;
use App\Models\Almacen;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TrasladoApiController extends Controller
{
    private function empresa(): int { return (int) session('id_empresa'); }

    /** Cabeceras de traslados, con filtros ?desde, ?hasta, ?almacen (origen o destino). */
    public function listar(Request $request): JsonResponse
    {
        $emp = $this->empresa();
        $traslados = DB::table('traslados as t')
            ->leftJoin('almacenes as ao', function ($j) { $j->on('ao.codigo', '=', 't.almacen_origen')->on('ao.id_empresa', '=', 't.id_empresa'); })
            ->leftJoin('almacenes as ad', function ($j) { $j->on('ad.codigo', '=', 't.almacen_destino')->on('ad.id_empresa', '=', 't.id_empresa'); })
            ->leftJoin('usuarios as u', 'u.usuario_id', '=', 't.id_usuario')
            ->where('t.id_empresa', $emp)
            ->when($request->filled('desde'),   fn ($q) => $q->whereDate('t.fecha', '>=', $request->desde))
            ->when($request->filled('hasta'),   fn ($q) => $q->whereDate('t.fecha', '<=', $request->hasta))
            ->when($request->filled('almacen'), fn ($q) => $q->where(fn ($w) => $w->where('t.almacen_origen', $request->almacen)->orWhere('t.almacen_destino', $request->almacen)))
            ->orderByDesc('t.id_traslado')
            ->select('t.*',
                DB::raw('COALESCE(ao.nombre, t.almacen_origen) as origen_nombre'),
                DB::raw('COALESCE(ad.nombre, t.almacen_destino) as destino_nombre'),
                DB::raw("TRIM(CONCAT(COALESCE(u.nombres,''),' ',COALESCE(u.apellidos,''))) as usuario"),
                DB::raw('(SELECT COUNT(*) FROM traslado_detalle d WHERE d.id_traslado = t.id_traslado) as items'))
            ->get();

        return response()->json($traslados);
    }

    /** Detalle (líneas) de un traslado. */
    public function detalle(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|integer']);
        $rows = DB::table('traslado_detalle as d')
            ->join('productos as p', 'p.id_producto', '=', 'd.id_producto')
            ->where('d.id_traslado', $request->id)
            ->select('p.codigo', 'p.descripcion as producto', 'p.medida as unidad', 'd.cantidad',
                'd.stock_ant_origen', 'd.stock_nuevo_origen', 'd.stock_ant_destino', 'd.stock_nuevo_destino')
            ->get();

        return response()->json($rows);
    }

    /** Registra un traslado (cabecera + detalle): salida del origen + ingreso al destino por cada línea. */
    public function guardar(Request $request): JsonResponse
    {
        $data = $request->validate([
            'almacen_origen'        => 'required',
            'almacen_destino'       => 'required|different:almacen_origen',
            'observacion'           => 'nullable|string|max:200',
            'detalles'              => 'required|array|min:1',
            'detalles.*.id_producto' => 'required|integer',
            'detalles.*.cantidad'   => 'required|integer|min:1',
        ]);

        $emp = $this->empresa();
        $uid = (int) (auth()->user()->usuario_id ?? 0);

        DB::beginTransaction();
        try {
            $motSal  = MotivoMovimiento::where('id_empresa', $emp)->where('tipo', 'S')->where('nombre', 'Traslado salida')->value('id_motivo');
            $motIng  = MotivoMovimiento::where('id_empresa', $emp)->where('tipo', 'I')->where('nombre', 'Traslado entrada')->value('id_motivo');
            $nomOrig = Almacen::where('id_empresa', $emp)->where('codigo', $data['almacen_origen'])->value('nombre') ?? $data['almacen_origen'];
            $nomDest = Almacen::where('id_empresa', $emp)->where('codigo', $data['almacen_destino'])->value('nombre') ?? $data['almacen_destino'];

            $traslado = Traslado::create([
                'id_empresa' => $emp, 'almacen_origen' => $data['almacen_origen'], 'almacen_destino' => $data['almacen_destino'],
                'fecha' => now(), 'observacion' => $data['observacion'] ?? null, 'id_usuario' => $uid, 'estado' => '1',
            ]);

            foreach ($data['detalles'] as $linea) {
                $cant = (int) $linea['cantidad'];

                $origen = Producto::where('id_empresa', $emp)->where('id_producto', $linea['id_producto'])
                    ->where('almacen', $data['almacen_origen'])->lockForUpdate()->firstOrFail();

                if ($cant > (int) $origen->cantidad) {
                    DB::rollBack();
                    return response()->json(['res' => false, 'msg' => "Stock insuficiente de \"{$origen->descripcion}\" en el origen (disponible: {$origen->cantidad})."], 409);
                }

                // Salida del origen
                $antO = (int) $origen->cantidad;
                $origen->update(['cantidad' => $antO - $cant]);
                InventarioMovimiento::create([
                    'id_empresa' => $emp, 'almacen' => $data['almacen_origen'], 'id_producto' => $origen->id_producto,
                    'tipo' => 'S', 'id_motivo' => $motSal, 'cantidad' => $cant, 'stock_anterior' => $antO, 'stock_nuevo' => $antO - $cant,
                    'costo' => $origen->costo, 'observacion' => "Traslado #{$traslado->id_traslado} a {$nomDest}", 'id_usuario' => $uid, 'fecha' => now(),
                ]);

                // Ingreso al destino (por código; si no existe, clona el producto)
                $dest = null;
                if (!empty($origen->codigo)) {
                    $dest = Producto::where('id_empresa', $emp)->where('almacen', $data['almacen_destino'])
                        ->where('codigo', $origen->codigo)->lockForUpdate()->first();
                }
                if ($dest) {
                    $antD = (int) $dest->cantidad;
                    $dest->update(['cantidad' => $antD + $cant]);
                } else {
                    $dest = $origen->replicate();
                    $dest->almacen  = $data['almacen_destino'];
                    $dest->cantidad = $cant;
                    $dest->save();
                    $antD = 0;
                }
                InventarioMovimiento::create([
                    'id_empresa' => $emp, 'almacen' => $data['almacen_destino'], 'id_producto' => $dest->id_producto,
                    'tipo' => 'I', 'id_motivo' => $motIng, 'cantidad' => $cant, 'stock_anterior' => $antD, 'stock_nuevo' => $antD + $cant,
                    'costo' => $dest->costo, 'observacion' => "Traslado #{$traslado->id_traslado} desde {$nomOrig}", 'id_usuario' => $uid, 'fecha' => now(),
                ]);

                DB::table('traslado_detalle')->insert([
                    'id_traslado'         => $traslado->id_traslado,
                    'id_producto'         => $origen->id_producto,
                    'cantidad'            => $cant,
                    'stock_ant_origen'    => $antO,
                    'stock_nuevo_origen'  => $antO - $cant,
                    'stock_ant_destino'   => $antD,
                    'stock_nuevo_destino' => $antD + $cant,
                ]);
            }

            DB::commit();
            return response()->json(['res' => true, 'id' => $traslado->id_traslado]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error traslado: ' . $e->getMessage());
            return response()->json(['res' => false, 'msg' => 'Error al realizar el traslado.'], 500);
        }
    }
}
