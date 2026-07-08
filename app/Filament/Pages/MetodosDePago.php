<?php

namespace App\Filament\Pages;

use App\Models\Banco;
use App\Models\BilleteraDigital;
use App\Models\BilleteraTipo;
use App\Models\CuentaBancaria;
use App\Models\Tarjeta;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MetodosDePago extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Métodos de Pago';
    protected static string|\UnitEnum|null $navigationGroup = 'Caja';
    protected static ?int $navigationSort = 6;
    protected string $view = 'filament.pages.metodos-de-pago';

    public string $tab = 'bancos';

    public function updatedTab(): void
    {
        $this->resetTable();
    }

    public function table(Table $table): Table
    {
        return match ($this->tab) {
            'cuentas' => $this->cuentasTable($table),
            'tarjetas' => $this->tarjetasTable($table),
            'billeteras' => $this->billeterasTable($table),
            default => $this->bancosTable($table),
        };
    }

    protected function bancosTable(Table $table): Table
    {
        return $table
            ->query(Banco::where('id_empresa', (int) session('id_empresa')))
            ->columns([
                TextColumn::make('nombre')->label('Nombre')->searchable(),
                TextColumn::make('codigo_sunat')->label('Código SUNAT')->placeholder('—'),
                TextColumn::make('estado')->label('Estado')
                    ->badge()
                    ->getStateUsing(fn (Banco $record): string => $record->estado === '1' ? 'Activo' : 'Inactivo')
                    ->color(fn (Banco $record): string => $record->estado === '1' ? 'success' : 'gray'),
            ])
            ->actions([
                EditAction::make('editar')
                    ->icon('heroicon-o-pencil')
                    ->color('primary')
                    ->form([
                        TextInput::make('nombre')->label('Nombre')->required()->maxLength(100),
                        TextInput::make('codigo_sunat')->label('Código SUNAT')->maxLength(10),
                        Toggle::make('estado')->label('Activo')->default(true),
                    ])
                    ->fillForm(fn (Banco $record): array => [
                        'nombre' => $record->nombre,
                        'codigo_sunat' => $record->codigo_sunat,
                        'estado' => $record->estado === '1',
                    ])
                    ->action(function (Banco $record, array $data): void {
                        $data['estado'] = $data['estado'] ? '1' : '0';
                        $record->update($data);
                        Notification::make()->success()->title('Banco actualizado')->send();
                    }),
                \Filament\Actions\Action::make('toggle')
                    ->icon('heroicon-o-arrows-right-left')
                    ->color('warning')
                    ->action(function (Banco $record): void {
                        $nuevo = $record->estado === '1' ? '0' : '1';
                        $record->update(['estado' => $nuevo]);
                        Notification::make()->success()->title($nuevo === '1' ? 'Banco activado' : 'Banco desactivado')->send();
                    }),
            ]);
    }

    protected function cuentasTable(Table $table): Table
    {
        return $table
            ->query(CuentaBancaria::where('cuentas_bancarias.id_empresa', (int) session('id_empresa'))
                ->with('banco'))
            ->columns([
                TextColumn::make('banco.nombre')->label('Banco'),
                TextColumn::make('tipo_cuenta')->label('Tipo'),
                TextColumn::make('numero_cuenta')->label('Número')->placeholder('—'),
                TextColumn::make('moneda')->label('Moneda'),
                TextColumn::make('titular')->label('Titular'),
                TextColumn::make('estado')->label('Estado')
                    ->badge()
                    ->getStateUsing(fn (CuentaBancaria $record): string => $record->estado === '1' ? 'Activo' : 'Inactivo')
                    ->color(fn (CuentaBancaria $record): string => $record->estado === '1' ? 'success' : 'gray'),
            ])
            ->actions([
                EditAction::make('editar')
                    ->icon('heroicon-o-pencil')->color('primary')
                    ->form([
                        Select::make('id_banco')->label('Banco')->required()
                            ->options(fn () => Banco::where('id_empresa', (int) session('id_empresa'))
                                ->pluck('nombre', 'id_banco')),
                        Select::make('tipo_cuenta')->label('Tipo')->required()
                            ->options(['CC' => 'Cuenta Corriente', 'CA' => 'Cuenta de Ahorros', 'CTS' => 'CTS', 'AHORRO' => 'Ahorro']),
                        Select::make('moneda')->label('Moneda')->required()
                            ->options(['PEN' => 'Soles (PEN)', 'USD' => 'Dólares (USD)']),
                        TextInput::make('numero_cuenta')->label('Número')->maxLength(30),
                        TextInput::make('cci')->label('CCI')->maxLength(30),
                        TextInput::make('titular')->label('Titular')->required()->maxLength(200),
                        Toggle::make('estado')->label('Activo')->default(true),
                    ])
                    ->fillForm(fn (CuentaBancaria $record): array => [
                        'id_banco' => $record->id_banco,
                        'tipo_cuenta' => $record->tipo_cuenta,
                        'moneda' => $record->moneda,
                        'numero_cuenta' => $record->numero_cuenta,
                        'cci' => $record->cci,
                        'titular' => $record->titular,
                        'estado' => $record->estado === '1',
                    ])
                    ->action(function (CuentaBancaria $record, array $data): void {
                        $data['estado'] = $data['estado'] ? '1' : '0';
                        $record->update($data);
                        Notification::make()->success()->title('Cuenta actualizada')->send();
                    }),
                \Filament\Actions\Action::make('toggle')
                    ->icon('heroicon-o-arrows-right-left')->color('warning')
                    ->action(function (CuentaBancaria $record): void {
                        $nuevo = $record->estado === '1' ? '0' : '1';
                        $record->update(['estado' => $nuevo]);
                        Notification::make()->success()->title($nuevo === '1' ? 'Activada' : 'Desactivada')->send();
                    }),
            ]);
    }

    protected function tarjetasTable(Table $table): Table
    {
        return $table
            ->query(Tarjeta::where('tarjetas.id_empresa', (int) session('id_empresa'))
                ->with(['banco', 'cuentaBancaria']))
            ->columns([
                TextColumn::make('banco.nombre')->label('Banco'),
                TextColumn::make('tipo')->label('Tipo'),
                TextColumn::make('marca')->label('Marca'),
                TextColumn::make('ultimos_4')->label('Últ. 4 díg.')->formatStateUsing(fn ($state): string => "*$state"),
                TextColumn::make('titular')->label('Titular'),
                TextColumn::make('fecha_vencimiento')->label('Vencimiento')->placeholder('—'),
                TextColumn::make('cuentaBancaria.numero_cuenta')->label('Cuenta vinculada')->placeholder('—'),
                TextColumn::make('estado')->label('Estado')
                    ->badge()
                    ->getStateUsing(fn (Tarjeta $record): string => $record->estado === '1' ? 'Activo' : 'Inactivo')
                    ->color(fn (Tarjeta $record): string => $record->estado === '1' ? 'success' : 'gray'),
            ])
            ->actions([
                EditAction::make('editar')
                    ->icon('heroicon-o-pencil')->color('primary')
                    ->form([
                        Select::make('id_banco')->label('Banco')->required()
                            ->options(fn () => Banco::where('id_empresa', (int) session('id_empresa'))->pluck('nombre', 'id_banco')),
                        Select::make('tipo')->label('Tipo')->required()
                            ->options(['DEBITO' => 'Débito', 'CREDITO' => 'Crédito']),
                        Select::make('marca')->label('Marca')->required()
                            ->options(['VISA' => 'Visa', 'MASTERCARD' => 'Mastercard', 'AMEX' => 'American Express', 'DINERS' => 'Diners']),
                        TextInput::make('ultimos_4')->label('Últimos 4 dígitos')->required()->maxLength(4),
                        DatePicker::make('fecha_vencimiento')->label('Vencimiento'),
                        TextInput::make('titular')->label('Titular')->required()->maxLength(200),
                        Select::make('id_cuenta_bancaria')->label('Cuenta vinculada')
                            ->options(fn () => CuentaBancaria::where('id_empresa', (int) session('id_empresa'))
                                ->selectRaw("id_cuenta, CONCAT((SELECT nombre FROM bancos WHERE id_banco = cuentas_bancarias.id_banco), ' ', COALESCE(numero_cuenta,'')) as label")
                                ->pluck('label', 'id_cuenta'))
                            ->nullable(),
                        Toggle::make('estado')->label('Activo')->default(true),
                    ])
                    ->fillForm(fn (Tarjeta $record): array => [
                        'id_banco' => $record->id_banco,
                        'tipo' => $record->tipo,
                        'marca' => $record->marca,
                        'ultimos_4' => $record->ultimos_4,
                        'fecha_vencimiento' => $record->fecha_vencimiento,
                        'titular' => $record->titular,
                        'id_cuenta_bancaria' => $record->id_cuenta_bancaria,
                        'estado' => $record->estado === '1',
                    ])
                    ->action(function (Tarjeta $record, array $data): void {
                        $data['estado'] = $data['estado'] ? '1' : '0';
                        $record->update($data);
                        Notification::make()->success()->title('Tarjeta actualizada')->send();
                    }),
                \Filament\Actions\Action::make('toggle')
                    ->icon('heroicon-o-arrows-right-left')->color('warning')
                    ->action(function (Tarjeta $record): void {
                        $nuevo = $record->estado === '1' ? '0' : '1';
                        $record->update(['estado' => $nuevo]);
                        Notification::make()->success()->title($nuevo === '1' ? 'Activada' : 'Desactivada')->send();
                    }),
            ]);
    }

    protected function billeterasTable(Table $table): Table
    {
        return $table
            ->query(BilleteraDigital::where('billeteras_digitales.id_empresa', (int) session('id_empresa'))
                ->with(['billeteraTipo', 'cuentaBancaria.banco']))
            ->columns([
                TextColumn::make('billeteraTipo.nombre')->label('Tipo'),
                TextColumn::make('cuentaBancaria.numero_cuenta')->label('Cuenta vinculada')->placeholder('—'),
                TextColumn::make('telefono')->label('Teléfono')->placeholder('—'),
                TextColumn::make('titular')->label('Titular'),
                TextColumn::make('estado')->label('Estado')
                    ->badge()
                    ->getStateUsing(fn (BilleteraDigital $record): string => $record->estado === '1' ? 'Activo' : 'Inactivo')
                    ->color(fn (BilleteraDigital $record): string => $record->estado === '1' ? 'success' : 'gray'),
            ])
            ->actions([
                EditAction::make('editar')
                    ->icon('heroicon-o-pencil')->color('primary')
                    ->form([
                        Select::make('id_billetera_tipo')->label('Tipo de billetera')->required()
                            ->options(fn () => BilleteraTipo::where('estado', '1')
                                ->where('id_empresa', (int) session('id_empresa'))
                                ->pluck('nombre', 'id')),
                        Select::make('id_cuenta_bancaria')->label('Cuenta vinculada')->required()
                            ->options(fn () => CuentaBancaria::where('id_empresa', (int) session('id_empresa'))
                                ->selectRaw("id_cuenta, CONCAT((SELECT nombre FROM bancos WHERE id_banco = cuentas_bancarias.id_banco), ' ', COALESCE(numero_cuenta,'')) as label")
                                ->pluck('label', 'id_cuenta')),
                        TextInput::make('telefono')->label('Teléfono')->required()->maxLength(15),
                        TextInput::make('titular')->label('Titular')->required()->maxLength(200),
                        Toggle::make('estado')->label('Activo')->default(true),
                    ])
                    ->fillForm(fn (BilleteraDigital $record): array => [
                        'id_billetera_tipo' => $record->id_billetera_tipo,
                        'id_cuenta_bancaria' => $record->id_cuenta_bancaria,
                        'telefono' => $record->telefono,
                        'titular' => $record->titular,
                        'estado' => $record->estado === '1',
                    ])
                    ->action(function (BilleteraDigital $record, array $data): void {
                        $data['estado'] = $data['estado'] ? '1' : '0';
                        $record->update($data);
                        Notification::make()->success()->title('Billetera actualizada')->send();
                    }),
                \Filament\Actions\Action::make('toggle')
                    ->icon('heroicon-o-arrows-right-left')->color('warning')
                    ->action(function (BilleteraDigital $record): void {
                        $nuevo = $record->estado === '1' ? '0' : '1';
                        $record->update(['estado' => $nuevo]);
                        Notification::make()->success()->title($nuevo === '1' ? 'Activada' : 'Desactivada')->send();
                    }),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('crear')
                ->label(fn (): string => match ($this->tab) {
                    'cuentas' => 'Nueva Cuenta',
                    'tarjetas' => 'Nueva Tarjeta',
                    'billeteras' => 'Nueva Billetera',
                    default => 'Nuevo Banco',
                })
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->form(fn (): array => match ($this->tab) {
                    'cuentas' => [
                        Select::make('id_banco')->label('Banco')->required()
                            ->options(fn () => Banco::where('id_empresa', (int) session('id_empresa'))->pluck('nombre', 'id_banco')),
                        Select::make('tipo_cuenta')->label('Tipo')->required()
                            ->options(['CC' => 'Cuenta Corriente', 'CA' => 'Cuenta de Ahorros', 'CTS' => 'CTS', 'AHORRO' => 'Ahorro']),
                        Select::make('moneda')->label('Moneda')->required()
                            ->options(['PEN' => 'Soles (PEN)', 'USD' => 'Dólares (USD)']),
                        TextInput::make('numero_cuenta')->label('Número')->maxLength(30),
                        TextInput::make('cci')->label('CCI')->maxLength(30),
                        TextInput::make('titular')->label('Titular')->required()->maxLength(200),
                        Toggle::make('estado')->label('Activo')->default(true),
                    ],
                    'tarjetas' => [
                        Select::make('id_banco')->label('Banco')->required()
                            ->options(fn () => Banco::where('id_empresa', (int) session('id_empresa'))->pluck('nombre', 'id_banco')),
                        Select::make('tipo')->label('Tipo')->required()
                            ->options(['DEBITO' => 'Débito', 'CREDITO' => 'Crédito']),
                        Select::make('marca')->label('Marca')->required()
                            ->options(['VISA' => 'Visa', 'MASTERCARD' => 'Mastercard', 'AMEX' => 'American Express', 'DINERS' => 'Diners']),
                        TextInput::make('ultimos_4')->label('Últimos 4 dígitos')->required()->maxLength(4),
                        DatePicker::make('fecha_vencimiento')->label('Vencimiento'),
                        TextInput::make('titular')->label('Titular')->required()->maxLength(200),
                        Select::make('id_cuenta_bancaria')->label('Cuenta vinculada')
                            ->options(fn () => CuentaBancaria::where('id_empresa', (int) session('id_empresa'))
                                ->selectRaw("id_cuenta, CONCAT((SELECT nombre FROM bancos WHERE id_banco = cuentas_bancarias.id_banco), ' ', COALESCE(numero_cuenta,'')) as label")
                                ->pluck('label', 'id_cuenta'))
                            ->nullable(),
                        Toggle::make('estado')->label('Activo')->default(true),
                    ],
                    'billeteras' => [
                        Select::make('id_billetera_tipo')->label('Tipo de billetera')->required()
                            ->options(fn () => BilleteraTipo::where('estado', '1')
                                ->where('id_empresa', (int) session('id_empresa'))
                                ->pluck('nombre', 'id')),
                        Select::make('id_cuenta_bancaria')->label('Cuenta vinculada')->required()
                            ->options(fn () => CuentaBancaria::where('id_empresa', (int) session('id_empresa'))
                                ->selectRaw("id_cuenta, CONCAT((SELECT nombre FROM bancos WHERE id_banco = cuentas_bancarias.id_banco), ' ', COALESCE(numero_cuenta,'')) as label")
                                ->pluck('label', 'id_cuenta')),
                        TextInput::make('telefono')->label('Teléfono')->required()->maxLength(15),
                        TextInput::make('titular')->label('Titular')->required()->maxLength(200),
                        Toggle::make('estado')->label('Activo')->default(true),
                    ],
                    default => [
                        TextInput::make('nombre')->label('Nombre')->required()->maxLength(100),
                        TextInput::make('codigo_sunat')->label('Código SUNAT')->maxLength(10),
                        Toggle::make('estado')->label('Activo')->default(true),
                    ],
                })
                ->action(function (array $data): void {
                    $empresaId = (int) session('id_empresa');
                    $data['estado'] = $data['estado'] ? '1' : '0';

                    match ($this->tab) {
                        'cuentas' => CuentaBancaria::create(array_merge($data, ['id_empresa' => $empresaId])),
                        'tarjetas' => Tarjeta::create(array_merge($data, ['id_empresa' => $empresaId])),
                        'billeteras' => BilleteraDigital::create(array_merge($data, ['id_empresa' => $empresaId])),
                        default => Banco::create(array_merge($data, ['id_empresa' => $empresaId])),
                    };

                    Notification::make()->success()->title('Creado correctamente')->send();
                    $this->resetTable();
                }),
        ];
    }
}
