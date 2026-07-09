<?php

namespace App\Filament\Resources\NotaElectronicaResource\Pages;

use App\Filament\Resources\NotaElectronicaResource;
use App\Models\NotaElectronica;
use App\Models\Venta;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class CreateNotaElectronica extends CreateRecord
{
    protected static string $resource = NotaElectronicaResource::class;

    protected static ?string $title = 'Nueva Nota Electrónica';

    /** Catálogo 09 de SUNAT — motivos de Nota de Crédito. */
    public const MOTIVOS_CREDITO = [
        '01' => 'Anulación de la operación',
        '02' => 'Anulación por error en el RUC',
        '03' => 'Corrección por error en la descripción',
        '04' => 'Descuento global',
        '05' => 'Descuento por ítem',
        '06' => 'Devolución total',
        '07' => 'Devolución por ítem',
        '08' => 'Bonificación',
        '09' => 'Disminución en el valor',
        '10' => 'Otros Conceptos',
    ];

    /** Catálogo 10 de SUNAT — motivos de Nota de Débito. */
    public const MOTIVOS_DEBITO = [
        '01' => 'Intereses por mora',
        '02' => 'Aumento en el valor',
        '03' => 'Penalidades / Otros conceptos',
    ];

    public function mount(): void
    {
        parent::mount();

        // Prefill desde la lista de ventas: /notas-electronicas/create?venta=123&tipo=credito
        $idVenta = (int) request()->query('venta');
        if (! $idVenta) {
            return;
        }

        $venta = Venta::where('id_empresa', (int) session('id_empresa'))->find($idVenta);
        if (! $venta) {
            return;
        }

        $tipo = request()->query('tipo') === 'debito' ? 'debito' : 'credito';

        $this->form->fill(array_merge($this->data ?? [], [
            'id_venta'   => $venta->id_venta,
            'tipo'       => $tipo,
            'cod_motivo' => '01',
            'motivo'     => $tipo === 'credito'
                ? self::MOTIVOS_CREDITO['01']
                : self::MOTIVOS_DEBITO['01'],
            'total'      => (float) $venta->total,
        ]));
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Comprobante afectado')
                ->compact()
                ->columns(2)
                ->schema([
                    Select::make('id_venta')
                        ->label('Venta (factura o boleta)')
                        ->placeholder('Buscar por serie o número…')
                        ->searchable()
                        ->required()
                        ->columnSpanFull()
                        ->getSearchResultsUsing(fn (string $search): array => Venta::query()
                            ->where('id_empresa', (int) session('id_empresa'))
                            ->whereIn('id_tido', [1, 2])
                            ->where('estado', '!=', '0')
                            ->where(fn ($q) => $q
                                ->where('serie', 'like', "%{$search}%")
                                ->orWhere('numero', 'like', "%{$search}%"))
                            ->with('cliente')
                            ->orderByDesc('id_venta')
                            ->limit(20)
                            ->get()
                            ->mapWithKeys(fn (Venta $v) => [
                                $v->id_venta => $v->documento_completo . ' — ' . ($v->cliente?->datos ?? 'Cliente')
                                    . ' — S/ ' . number_format((float) $v->total, 2),
                            ])
                            ->toArray())
                        ->getOptionLabelUsing(function ($value): ?string {
                            $v = Venta::with('cliente')->find($value);

                            return $v ? $v->documento_completo . ' — ' . ($v->cliente?->datos ?? 'Cliente') : null;
                        })
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set): void {
                            if ($v = Venta::find($state)) {
                                $set('total', (float) $v->total);
                            }
                        }),

                    Placeholder::make('detalle_venta')
                        ->hiddenLabel()
                        ->columnSpanFull()
                        ->visible(fn (callable $get): bool => filled($get('id_venta')))
                        ->content(function (callable $get): HtmlString {
                            $v = Venta::with('cliente')->find($get('id_venta'));
                            if (! $v) {
                                return new HtmlString('');
                            }

                            return new HtmlString(
                                '<div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;'
                                . 'padding:11px 14px;border-radius:12px;border:1px solid rgba(59,130,246,.35);background:rgba(59,130,246,.07)">'
                                . '<span><strong>' . e($v->documento_completo) . '</strong> · ' . e($v->cliente?->datos ?? 'Cliente') . '</span>'
                                . '<span>Emitida: ' . optional($v->fecha_emision)->format('d/m/Y')
                                . ' · Total: <strong>S/ ' . number_format((float) $v->total, 2) . '</strong></span>'
                                . '</div>'
                            );
                        }),
                ]),

            Section::make('Datos de la nota')
                ->compact()
                ->columns(2)
                ->schema([
                    Select::make('tipo')
                        ->label('Tipo de nota')
                        ->options([
                            'credito' => 'Nota de Crédito (EC01)',
                            'debito'  => 'Nota de Débito (ED01)',
                        ])
                        ->default('credito')
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set): void {
                            $set('cod_motivo', '01');
                            $set('motivo', $state === 'debito'
                                ? self::MOTIVOS_DEBITO['01']
                                : self::MOTIVOS_CREDITO['01']);
                        }),

                    Select::make('cod_motivo')
                        ->label('Motivo (código SUNAT)')
                        ->options(fn (callable $get): array => $get('tipo') === 'debito'
                            ? self::MOTIVOS_DEBITO
                            : self::MOTIVOS_CREDITO)
                        ->default('01')
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $get, callable $set): void {
                            $catalogo = $get('tipo') === 'debito' ? self::MOTIVOS_DEBITO : self::MOTIVOS_CREDITO;
                            $set('motivo', $catalogo[$state] ?? '');
                        }),

                    TextInput::make('motivo')
                        ->label('Descripción del motivo')
                        ->helperText('Se completa según el código; podés ajustarlo.')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    TextInput::make('total')
                        ->label('Monto de la nota (S/)')
                        ->numeric()
                        ->minValue(0.01)
                        ->prefix('S/')
                        ->required()
                        ->helperText('No puede superar el total del comprobante afectado.')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    protected function fallo(string $mensaje): never
    {
        Notification::make()->danger()->title($mensaje)->persistent()->send();

        throw new Halt();
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): NotaElectronica {
            $empresa  = (int) session('id_empresa');
            $sucursal = (int) session('sucursal');

            $venta = Venta::where('id_empresa', $empresa)->find($data['id_venta']);
            if (! $venta) {
                $this->fallo('No se encontró la venta seleccionada.');
            }

            if ((float) $data['total'] > (float) $venta->total) {
                $this->fallo('El monto de la nota (S/ ' . number_format((float) $data['total'], 2)
                    . ') no puede superar el total del comprobante (S/ ' . number_format((float) $venta->total, 2) . ').');
            }

            $serie = $data['tipo'] === 'credito' ? 'EC01' : 'ED01';

            $numero = (int) DB::table('notas_electronicas')
                ->where('id_empresa', $empresa)
                ->where('sucursal', $sucursal)
                ->where('serie', $serie)
                ->lockForUpdate()
                ->max('numero') + 1;

            $nota = NotaElectronica::create([
                'id_venta'      => $venta->id_venta,
                'tipo'          => $data['tipo'],
                'cod_motivo'    => $data['cod_motivo'],
                // `motivo` es INT en el esquema legacy; la descripción va en motivo_desc.
                'motivo'        => (int) $data['cod_motivo'],
                'motivo_desc'   => $data['motivo'],
                'id_empresa'    => $empresa,
                'sucursal'      => $sucursal,
                'serie'         => $serie,
                'numero'        => $numero,
                'total'         => $data['total'],
                'fecha_emision' => now()->toDateString(),
                'estado'        => '1',
                'enviado_sunat' => '0',
            ]);

            $doc = $serie . '-' . str_pad((string) $numero, 8, '0', STR_PAD_LEFT);

            Notification::make()->success()
                ->title("Nota {$doc} registrada")
                ->body('Ya podés enviarla a SUNAT desde la lista.')
                ->send();

            return $nota;
        });
    }

    protected function getRedirectUrl(): string
    {
        return NotaElectronicaResource::getUrl('index');
    }
}
