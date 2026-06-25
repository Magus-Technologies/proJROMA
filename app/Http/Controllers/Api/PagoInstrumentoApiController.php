<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PagoInstrumentoApiController extends Controller
{
    private function empresa(): int { return (int) session('id_empresa'); }

    // ── Selects (solo activos) ──────────────────────────────────────────────
    public function bancos(): JsonResponse
    {
        return response()->json(
            DB::table('bancos')->where('id_empresa', $this->empresa())->where('estado', '1')
                ->orderBy('nombre')->get(['id_banco', 'nombre'])
        );
    }

    public function cuentasBancarias(): JsonResponse
    {
        return response()->json(
            DB::table('cuentas_bancarias as cb')->join('bancos as b', 'b.id_banco', '=', 'cb.id_banco')
                ->where('cb.id_empresa', $this->empresa())->where('cb.estado', '1')
                ->orderBy('b.nombre')
                ->select('cb.id_cuenta', 'b.nombre as banco', 'cb.tipo_cuenta', 'cb.numero_cuenta', 'cb.moneda', 'cb.titular')
                ->get()
        );
    }

    public function tarjetas(): JsonResponse
    {
        return response()->json(
            DB::table('tarjetas as t')->join('bancos as b', 'b.id_banco', '=', 't.id_banco')
                ->where('t.id_empresa', $this->empresa())->where('t.estado', '1')
                ->orderBy('b.nombre')
                ->select('t.id_tarjeta', 'b.nombre as banco', 't.tipo', 't.marca', 't.ultimos_4', 't.titular', 't.id_cuenta_bancaria')
                ->get()
        );
    }

    public function billeteras(): JsonResponse
    {
        return response()->json(
            DB::table('billeteras_digitales as bd')
                ->join('billetera_tipos as bt', 'bt.id', '=', 'bd.id_billetera_tipo')
                ->leftJoin('cuentas_bancarias as cb', 'cb.id_cuenta', '=', 'bd.id_cuenta_bancaria')
                ->leftJoin('bancos as b', 'b.id_banco', '=', 'cb.id_banco')
                ->where('bd.id_empresa', $this->empresa())->where('bd.estado', '1')
                ->orderBy('bt.nombre')
                ->select('bd.id_billetera', 'bt.nombre as tipo', 'bd.telefono', 'bd.titular',
                         'bd.id_cuenta_bancaria',
                         DB::raw('COALESCE(CONCAT(b.nombre, " - ", cb.tipo_cuenta, " ", cb.numero_cuenta), "-") as cuenta_vinculada'))
                ->get()
        );
    }

    // ── Billetera Tipos (select) ──────────────────────────────────────────
    public function billeteraTipos(): JsonResponse
    {
        $empresa = $this->empresa();
        $existe = DB::table('billetera_tipos')->where('id_empresa', $empresa)->exists();
        if (!$existe) {
            $defaults = ['Yape', 'Plin', 'Tunki', 'Agora', 'BIM', 'Otro'];
            foreach ($defaults as $name) {
                DB::table('billetera_tipos')->insert([
                    'id_empresa' => $empresa, 'nombre' => $name, 'estado' => '1',
                ]);
            }
        }
        return response()->json(
            DB::table('billetera_tipos')->where('id_empresa', $empresa)->where('estado', '1')
                ->orderBy('nombre')->get(['id', 'nombre'])
        );
    }

    // ── DataTables server-side ──────────────────────────────────────────────
    public function bancosDt(): mixed
    {
        return DataTables::of(
            DB::table('bancos')->where('id_empresa', $this->empresa())
        )->make(true);
    }

    public function cuentasDt(): mixed
    {
        $query = DB::table('cuentas_bancarias as cb')
            ->leftJoin('bancos as b', 'b.id_banco', '=', 'cb.id_banco')
            ->where('cb.id_empresa', $this->empresa())
            ->select('cb.*', DB::raw('COALESCE(b.nombre, "-") as banco'),
                     DB::raw('COALESCE(cb.numero_cuenta, cb.cci, "-") as numero'));
        return DataTables::of($query)->make(true);
    }

    public function tarjetasDt(): mixed
    {
        $query = DB::table('tarjetas as t')
            ->leftJoin('bancos as b', 'b.id_banco', '=', 't.id_banco')
            ->leftJoin('cuentas_bancarias as cb', 'cb.id_cuenta', '=', 't.id_cuenta_bancaria')
            ->where('t.id_empresa', $this->empresa())
            ->select('t.*', DB::raw('COALESCE(b.nombre, "-") as banco'),
                     DB::raw('COALESCE(CONCAT(cb.tipo_cuenta, " ", cb.numero_cuenta), "-") as cuenta_vinculada'));
        return DataTables::of($query)->make(true);
    }

    public function billeterasDt(): mixed
    {
        $query = DB::table('billeteras_digitales as bd')
            ->join('billetera_tipos as bt', 'bt.id', '=', 'bd.id_billetera_tipo')
            ->leftJoin('cuentas_bancarias as cb', 'cb.id_cuenta', '=', 'bd.id_cuenta_bancaria')
            ->leftJoin('bancos as b', 'b.id_banco', '=', 'cb.id_banco')
            ->where('bd.id_empresa', $this->empresa())
            ->select('bd.*', 'bt.nombre as tipo',
                     DB::raw('COALESCE(CONCAT(b.nombre, " - ", cb.tipo_cuenta, " ", cb.numero_cuenta), "-") as cuenta_vinculada'));
        return DataTables::of($query)->make(true);
    }

    // ── Billetera Tipos CRUD (DataTable) ───────────────────────────────────
    public function billeteraTiposDt(): mixed
    {
        return DataTables::of(
            DB::table('billetera_tipos')->where('id_empresa', $this->empresa())
        )->make(true);
    }

    public function guardarBilleteraTipo(Request $r): JsonResponse
    {
        $r->validate(['nombre' => 'required|string|max:30']);
        $id = DB::table('billetera_tipos')->insertGetId([
            'id_empresa' => $this->empresa(),
            'nombre'     => $r->nombre,
            'estado'     => '1',
        ]);
        return response()->json(['res' => true, 'id' => $id]);
    }

    public function editarBilleteraTipo(Request $r): JsonResponse
    {
        $r->validate(['id' => 'required|integer', 'nombre' => 'required|string|max:30']);
        DB::table('billetera_tipos')->where('id_empresa', $this->empresa())->where('id', $r->id)
            ->update(['nombre' => $r->nombre, 'estado' => $r->estado ?? '1']);
        return response()->json(['res' => true]);
    }

    public function toggleBilleteraTipo(Request $r): JsonResponse
    {
        $r->validate(['id' => 'required|integer']);
        $row = DB::table('billetera_tipos')->where('id_empresa', $this->empresa())->where('id', $r->id)->first();
        if (!$row) return response()->json(['res' => false, 'msg' => 'No encontrado.'], 404);
        $new = $row->estado === '1' ? '0' : '1';
        DB::table('billetera_tipos')->where('id', $r->id)->update(['estado' => $new]);
        return response()->json(['res' => true, 'estado' => $new]);
    }

    // ── CRUD: Bancos ────────────────────────────────────────────────────────
    public function guardarBanco(Request $r): JsonResponse
    {
        $r->validate(['nombre' => 'required|string|max:100']);
        DB::table('bancos')->insert([
            'id_empresa'  => $this->empresa(),
            'nombre'      => $r->nombre,
            'codigo_sunat'=> $r->codigo_sunat ?? '',
            'estado'      => $r->estado ?? '1',
        ]);
        return response()->json(['res' => true]);
    }

    public function editarBanco(Request $r): JsonResponse
    {
        $r->validate(['id' => 'required|integer', 'nombre' => 'required|string|max:100']);
        DB::table('bancos')->where('id_empresa', $this->empresa())->where('id_banco', $r->id)
            ->update(['nombre' => $r->nombre, 'codigo_sunat' => $r->codigo_sunat ?? '', 'estado' => $r->estado ?? '1']);
        return response()->json(['res' => true]);
    }

    public function toggleBanco(Request $r): JsonResponse
    {
        $r->validate(['id' => 'required|integer']);
        $row = DB::table('bancos')->where('id_empresa', $this->empresa())->where('id_banco', $r->id)->first();
        if (!$row) return response()->json(['res' => false, 'msg' => 'No encontrado.'], 404);
        $new = $row->estado === '1' ? '0' : '1';
        DB::table('bancos')->where('id_banco', $r->id)->update(['estado' => $new]);
        return response()->json(['res' => true, 'estado' => $new]);
    }

    // ── CRUD: Cuentas Bancarias ─────────────────────────────────────────────
    public function guardarCuenta(Request $r): JsonResponse
    {
        $r->validate([
            'id_banco' => 'required|integer', 'tipo_cuenta' => 'required',
            'titular'  => 'required|string|max:200',
        ]);
        DB::table('cuentas_bancarias')->insert([
            'id_empresa'    => $this->empresa(),
            'id_banco'      => $r->id_banco,
            'tipo_cuenta'   => $r->tipo_cuenta ?? 'CC',
            'numero_cuenta' => $r->numero_cuenta ?? '',
            'cci'           => $r->cci ?? '',
            'moneda'        => $r->moneda ?? 'PEN',
            'titular'       => $r->titular,
            'estado'        => $r->estado ?? '1',
        ]);
        return response()->json(['res' => true]);
    }

    public function editarCuenta(Request $r): JsonResponse
    {
        $r->validate(['id' => 'required|integer', 'id_banco' => 'required|integer', 'titular' => 'required|string|max:200']);
        DB::table('cuentas_bancarias')->where('id_empresa', $this->empresa())->where('id_cuenta', $r->id)
            ->update([
                'id_banco' => $r->id_banco, 'tipo_cuenta' => $r->tipo_cuenta ?? 'CC',
                'numero_cuenta' => $r->numero_cuenta ?? '', 'cci' => $r->cci ?? '',
                'moneda' => $r->moneda ?? 'PEN', 'titular' => $r->titular, 'estado' => $r->estado ?? '1',
            ]);
        return response()->json(['res' => true]);
    }

    public function toggleCuenta(Request $r): JsonResponse
    {
        $r->validate(['id' => 'required|integer']);
        $row = DB::table('cuentas_bancarias')->where('id_empresa', $this->empresa())->where('id_cuenta', $r->id)->first();
        if (!$row) return response()->json(['res' => false, 'msg' => 'No encontrada.'], 404);
        $new = $row->estado === '1' ? '0' : '1';
        DB::table('cuentas_bancarias')->where('id_cuenta', $r->id)->update(['estado' => $new]);
        return response()->json(['res' => true, 'estado' => $new]);
    }

    // ── CRUD: Tarjetas ──────────────────────────────────────────────────────
    public function guardarTarjeta(Request $r): JsonResponse
    {
        $r->validate([
            'id_banco' => 'required|integer', 'ultimos_4' => 'required|string|size:4',
            'titular'  => 'required|string|max:200',
        ]);
        DB::table('tarjetas')->insert([
            'id_empresa'        => $this->empresa(),
            'id_banco'          => $r->id_banco,
            'id_cuenta_bancaria'=> $r->id_cuenta_bancaria ?? null,
            'tipo'              => $r->tipo ?? 'DEBITO',
            'marca'             => $r->marca ?? 'VISA',
            'ultimos_4'         => $r->ultimos_4,
            'titular'           => $r->titular,
            'fecha_vencimiento' => $r->fecha_vencimiento ?? null,
            'estado'            => $r->estado ?? '1',
        ]);
        return response()->json(['res' => true]);
    }

    public function editarTarjeta(Request $r): JsonResponse
    {
        $r->validate(['id' => 'required|integer', 'id_banco' => 'required|integer', 'ultimos_4' => 'required|string|size:4', 'titular' => 'required|string|max:200']);
        DB::table('tarjetas')->where('id_empresa', $this->empresa())->where('id_tarjeta', $r->id)
            ->update([
                'id_banco' => $r->id_banco, 'id_cuenta_bancaria' => $r->id_cuenta_bancaria ?? null,
                'tipo' => $r->tipo ?? 'DEBITO', 'marca' => $r->marca ?? 'VISA',
                'ultimos_4' => $r->ultimos_4, 'titular' => $r->titular,
                'fecha_vencimiento' => $r->fecha_vencimiento ?? null, 'estado' => $r->estado ?? '1',
            ]);
        return response()->json(['res' => true]);
    }

    public function toggleTarjeta(Request $r): JsonResponse
    {
        $r->validate(['id' => 'required|integer']);
        $row = DB::table('tarjetas')->where('id_empresa', $this->empresa())->where('id_tarjeta', $r->id)->first();
        if (!$row) return response()->json(['res' => false, 'msg' => 'No encontrada.'], 404);
        $new = $row->estado === '1' ? '0' : '1';
        DB::table('tarjetas')->where('id_tarjeta', $r->id)->update(['estado' => $new]);
        return response()->json(['res' => true, 'estado' => $new]);
    }

    // ── CRUD: Billeteras Digitales ──────────────────────────────────────────
    public function guardarBilletera(Request $r): JsonResponse
    {
        $r->validate([
            'id_billetera_tipo' => 'required|integer',
            'id_cuenta_bancaria'=> 'required|integer',
            'telefono'          => 'required|string|max:15',
            'titular'           => 'required|string|max:200',
        ]);
        DB::table('billeteras_digitales')->insert([
            'id_empresa'         => $this->empresa(),
            'id_billetera_tipo'  => $r->id_billetera_tipo,
            'id_cuenta_bancaria' => $r->id_cuenta_bancaria,
            'telefono'           => $r->telefono,
            'titular'            => $r->titular,
            'estado'             => $r->estado ?? '1',
        ]);
        return response()->json(['res' => true]);
    }

    public function editarBilletera(Request $r): JsonResponse
    {
        $r->validate([
            'id'                => 'required|integer',
            'id_billetera_tipo' => 'required|integer',
            'id_cuenta_bancaria'=> 'required|integer',
            'telefono'          => 'required|string|max:15',
            'titular'           => 'required|string|max:200',
        ]);
        DB::table('billeteras_digitales')->where('id_empresa', $this->empresa())->where('id_billetera', $r->id)
            ->update([
                'id_billetera_tipo'  => $r->id_billetera_tipo,
                'id_cuenta_bancaria' => $r->id_cuenta_bancaria,
                'telefono'           => $r->telefono,
                'titular'            => $r->titular,
                'estado'             => $r->estado ?? '1',
            ]);
        return response()->json(['res' => true]);
    }

    public function toggleBilletera(Request $r): JsonResponse
    {
        $r->validate(['id' => 'required|integer']);
        $row = DB::table('billeteras_digitales')->where('id_empresa', $this->empresa())->where('id_billetera', $r->id)->first();
        if (!$row) return response()->json(['res' => false, 'msg' => 'No encontrada.'], 404);
        $new = $row->estado === '1' ? '0' : '1';
        DB::table('billeteras_digitales')->where('id_billetera', $r->id)->update(['estado' => $new]);
        return response()->json(['res' => true, 'estado' => $new]);
    }
}
