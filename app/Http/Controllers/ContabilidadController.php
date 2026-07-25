<?php

namespace App\Http\Controllers;

use App\Models\AsientoContable;
use App\Models\AsientoDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContabilidadController extends Controller
{
    public function storeAsiento(Request $request)
    {
        $data = $request->validate([
            'fecha' => 'required|date',
            'glosa' => 'required|string|max:500',
            'tipo' => 'required|in:apertura,operaciones,ajuste,cierre',
            'detalle' => 'required|array|min:2',
            'detalle.*.cuenta_id' => 'required|exists:plan_cuentas,id',
            'detalle.*.debe' => 'numeric|min:0',
            'detalle.*.haber' => 'numeric|min:0',
            'detalle.*.glosa' => 'nullable|string|max:300',
        ]);

        $totalDebe = collect($data['detalle'])->sum('debe');
        $totalHaber = collect($data['detalle'])->sum('haber');

        if (abs($totalDebe - $totalHaber) > 0.01) {
            return back()->withErrors(['error' => 'El total del Debe debe ser igual al total del Haber.']);
        }

        DB::transaction(function () use ($data, $totalDebe, $totalHaber) {
            $asiento = AsientoContable::create([
                'numero' => AsientoContable::nextNumber(),
                'fecha' => $data['fecha'],
                'glosa' => $data['glosa'],
                'tipo' => $data['tipo'],
                'estado' => 'provisional',
                'total_debe' => $totalDebe,
                'total_haber' => $totalHaber,
                'user_id' => session('usuario_id'),
            ]);

            foreach ($data['detalle'] as $item) {
                if ((float) $item['debe'] > 0 || (float) $item['haber'] > 0) {
                    AsientoDetalle::create([
                        'asiento_id' => $asiento->id,
                        'plan_cuenta_id' => $item['cuenta_id'],
                        'debe' => $item['debe'],
                        'haber' => $item['haber'],
                        'glosa' => $item['glosa'] ?? null,
                    ]);
                }
            }
        });

        return redirect()->route('filament.admin.pages.libro-diario', [
            'desde' => $request->desde,
            'hasta' => $request->hasta,
        ]);
    }

    public function anularAsiento(int $id)
    {
        AsientoContable::where('id', $id)->update(['estado' => 'anulado']);
        return back();
    }
}
