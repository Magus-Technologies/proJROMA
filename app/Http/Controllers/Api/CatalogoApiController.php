<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Subcategoria;
use App\Models\Marca;
use App\Models\Submarca;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CatalogoApiController extends Controller
{
    /** Configuración por tipo: modelo, tabla, PK, padre y "guards" de integridad */
    private const MAP = [
        'categorias'    => ['model' => Categoria::class,    'table' => 'categorias',    'pk' => 'id_categoria',    'parent' => null,           'guards' => [['subcategorias', 'id_categoria', 'subcategorías'], ['productos', 'id_categoria', 'productos']]],
        'subcategorias' => ['model' => Subcategoria::class, 'table' => 'subcategorias', 'pk' => 'id_subcategoria', 'parent' => 'id_categoria', 'parentTable' => 'categorias', 'parentPk' => 'id_categoria', 'guards' => [['productos', 'id_subcategoria', 'productos']]],
        'marcas'        => ['model' => Marca::class,        'table' => 'marcas',        'pk' => 'id_marca',        'parent' => null,           'guards' => [['submarcas', 'id_marca', 'submarcas'], ['productos', 'id_marca', 'productos']]],
        'submarcas'     => ['model' => Submarca::class,     'table' => 'submarcas',     'pk' => 'id_submarca',     'parent' => 'id_marca', 'parentTable' => 'marcas', 'parentPk' => 'id_marca', 'guards' => [['productos', 'id_submarca', 'productos']]],
    ];

    private function empresa(): int { return (int) session('id_empresa'); }

    private function cfg(string $tipo): array { return self::MAP[$tipo]; }

    /**
     * Tablas/etiquetas que referencian a este item y lo dejan "en uso".
     * $soloActivos: true para desactivar (sólo refs activas), false para eliminar (cualquier ref).
     */
    private function enUso(array $cfg, int $id, bool $soloActivos = true): array
    {
        $usos = [];
        foreach ($cfg['guards'] ?? [] as [$tabla, $col, $lbl]) {
            $q = DB::table($tabla)->where($col, $id)->where('id_empresa', $this->empresa());
            if ($soloActivos) $q->where('estado', '1');
            if ($q->exists()) $usos[] = $lbl;
        }
        return $usos;
    }

    /**
     * Lista los items. Por defecto trae todos (activos e inactivos) con su estado.
     * Con ?activos=1 sólo los activos (para los selects de producto).
     * Con ?parent=ID filtra por su padre.
     */
    public function listar(string $tipo, Request $request): JsonResponse
    {
        $cfg = $this->cfg($tipo);

        // Tipos con padre: incluir el nombre del padre
        if (!empty($cfg['parentTable'])) {
            $rows = DB::table($cfg['table'] . ' as a')
                ->leftJoin($cfg['parentTable'] . ' as p', 'p.' . $cfg['parentPk'], '=', 'a.' . $cfg['parent'])
                ->where('a.id_empresa', $this->empresa())
                ->when($request->boolean('activos'), fn ($q) => $q->where('a.estado', '1'))
                ->when($request->filled('parent'), fn ($q) => $q->where('a.' . $cfg['parent'], (int) $request->parent))
                ->select('a.*', 'p.nombre as parent_nombre')
                ->orderBy('a.nombre')
                ->get();
            return response()->json($rows);
        }

        $model = $cfg['model'];
        return response()->json(
            $model::where('id_empresa', $this->empresa())
                ->when($request->boolean('activos'), fn ($q) => $q->where('estado', '1'))
                ->orderBy('nombre')
                ->get()
        );
    }

    public function guardar(string $tipo, Request $request): JsonResponse
    {
        $cfg = $this->cfg($tipo);

        $rules = ['nombre' => 'required|string|max:150', 'descripcion' => 'nullable|string|max:255', 'estado' => 'nullable|in:0,1'];
        if ($cfg['parent']) {
            $rules[$cfg['parent']] = 'required|integer';
        }
        $data = $request->validate($rules);

        $data['id_empresa'] = $this->empresa();
        $data['estado']     = $request->input('estado', '1');   // permite registrar desactivado

        $item = $cfg['model']::create($data);

        return response()->json(['res' => true, 'id' => $item->{$cfg['pk']}]);
    }

    public function editar(string $tipo, Request $request): JsonResponse
    {
        $cfg = $this->cfg($tipo);

        $rules = ['id' => 'required|integer', 'nombre' => 'required|string|max:150', 'descripcion' => 'nullable|string|max:255', 'estado' => 'nullable|in:0,1'];
        if ($cfg['parent']) {
            $rules[$cfg['parent']] = 'nullable|integer';
        }
        $request->validate($rules);

        $payload = ['nombre' => $request->nombre, 'descripcion' => $request->descripcion, 'estado' => $request->input('estado', '1')];
        if ($cfg['parent'] && $request->filled($cfg['parent'])) {
            $payload[$cfg['parent']] = (int) $request->{$cfg['parent']};
        }

        $cfg['model']::where('id_empresa', $this->empresa())
            ->where($cfg['pk'], $request->id)
            ->update($payload);

        return response()->json(['res' => true]);
    }

    /** Activa / desactiva. Al desactivar valida que no esté en uso. */
    public function toggle(string $tipo, Request $request): JsonResponse
    {
        $cfg = $this->cfg($tipo);
        $request->validate(['id' => 'required|integer']);

        $item = $cfg['model']::where('id_empresa', $this->empresa())
            ->where($cfg['pk'], $request->id)
            ->firstOrFail();

        $nuevo = $item->estado === '1' ? '0' : '1';

        if ($nuevo === '0') {
            $usos = $this->enUso($cfg, (int) $request->id);
            if ($usos) {
                return response()->json([
                    'res' => false,
                    'msg' => 'No se puede desactivar: está en uso por ' . implode(', ', $usos) . '.',
                ], 409);
            }
        }

        $item->update(['estado' => $nuevo]);

        return response()->json(['res' => true, 'estado' => $nuevo]);
    }

    public function borrar(string $tipo, Request $request): JsonResponse
    {
        $cfg = $this->cfg($tipo);
        $request->validate(['id' => 'required|integer']);

        $usos = $this->enUso($cfg, (int) $request->id, false);
        if ($usos) {
            return response()->json([
                'res' => false,
                'msg' => 'No se puede eliminar: está en uso por ' . implode(', ', $usos) . '.',
            ], 409);
        }

        $cfg['model']::where('id_empresa', $this->empresa())
            ->where($cfg['pk'], $request->id)
            ->delete();

        return response()->json(['res' => true]);
    }
}
