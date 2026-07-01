<?php

namespace App\Filament\Resources;

use App\Models\Empresa;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Filament\Resources\EmpresaResource\Pages;

use UnitEnum;

class EmpresaResource extends Resource
{
    protected static ?string $model = Empresa::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Empresas';
    protected static ?int    $navigationSort  = 3;
    protected static string|UnitEnum|null $navigationGroup = 'Administración';

    protected static ?string $pluralLabel = 'Empresas';

    protected static ?string $label = 'Empresa';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ruc')
                    ->label('RUC')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('razon_social')
                    ->label('Razón Social')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('comercial')
                    ->label('Nombre Comercial')
                    ->searchable()
                    ->limit(25),

                TextColumn::make('distrito')
                    ->label('Distrito')
                    ->sortable(),

                IconColumn::make('estado')
                    ->label('Activo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('modo')
                    ->label('Modo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'beta' => 'warning',
                        'produccion' => 'success',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        '1' => 'Activo',
                        '0' => 'Inactivo',
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('toggle')
                        ->label('Activar/Desactivar')
                        ->icon('heroicon-o-power')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading(fn (Empresa $record): string => $record->estado === '1' ? '¿Desactivar empresa?' : '¿Activar empresa?')
                        ->action(function (Empresa $record) {
                            $record->update(['estado' => $record->estado === '1' ? '0' : '1']);
                            Notification::make()
                                ->title($record->estado === '1' ? 'Empresa activada' : 'Empresa desactivada')
                                ->success()
                                ->send();
                        }),
                    DeleteAction::make()
                        ->before(function (Action $action, Empresa $record) {
                            if ($record->usuarios()->exists()) {
                                Notification::make()
                                    ->title('No se puede eliminar')
                                    ->body('Hay usuarios asignados a esta empresa.')
                                    ->danger()
                                    ->send();
                                $action->cancel();
                            }
                        }),
                ])->tooltip('Acciones'),
            ])
            ->defaultSort('razon_social', 'asc');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Logo')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        FileUpload::make('logo')
                            ->label('Logo de la empresa')
                            ->image()
                            ->disk('public')
                            ->imagePreviewHeight('120')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml'])
                            ->directory('logos')
                            ->maxSize(2048)
                            ->columnSpanFull(),
                    ]),

                Section::make('Datos Principales')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('ruc')
                                    ->label('RUC')
                                    ->required()
                                    ->maxLength(11)
                                    ->minLength(11)
                                    ->unique(ignoreRecord: true),

                                TextInput::make('razon_social')
                                    ->label('Razón Social')
                                    ->required()
                                    ->maxLength(245),

                                TextInput::make('comercial')
                                    ->label('Nombre Comercial')
                                    ->maxLength(245),
                            ]),
                    ]),

                Section::make('Ubicación')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('direccion')
                                    ->label('Dirección')
                                    ->maxLength(245)
                                    ->columnSpan(2),

                                TextInput::make('cod_sucursal')
                                    ->label('Código Sucursal')
                                    ->maxLength(4),
                            ]),
                        Grid::make(4)
                            ->schema([
                                TextInput::make('distrito')
                                    ->label('Distrito')
                                    ->maxLength(45),
                                TextInput::make('provincia')
                                    ->label('Provincia')
                                    ->maxLength(45),
                                TextInput::make('departamento')
                                    ->label('Departamento')
                                    ->maxLength(45),
                                TextInput::make('ubigeo')
                                    ->label('Ubigeo')
                                    ->maxLength(6),
                            ]),
                    ]),

                Section::make('Contacto')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->maxLength(145),
                                TextInput::make('telefono')
                                    ->label('Teléfono 1')
                                    ->maxLength(30),
                                TextInput::make('telefono2')
                                    ->label('Teléfono 2')
                                    ->maxLength(30),
                                TextInput::make('telefono3')
                                    ->label('Teléfono 3')
                                    ->maxLength(30),
                            ]),
                    ]),

                Section::make('Credenciales SUNAT')
                    ->icon('heroicon-o-key')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('user_sol')
                                    ->label('Usuario SOL')
                                    ->maxLength(45),
                                TextInput::make('clave_sol')
                                    ->label('Clave SOL')
                                    ->maxLength(45),
                            ]),
                        FileUpload::make('certificado')
                            ->label('Certificado Digital (.pem)')
                            ->acceptedFileTypes(['.pem', '.pfx', '.p12'])
                            ->directory('certificados')
                            ->maxSize(512)
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record !== null),
                    ]),

                Section::make('Configuración')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextInput::make('igv')
                                    ->label('IGV')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(1)
                                    ->default(0.18)
                                    ->step(0.01),

                                Select::make('tipo_impresion')
                                    ->label('Tipo Impresión')
                                    ->options([
                                        '1' => 'A4',
                                        '2' => '8cm (Voucher)',
                                    ])
                                    ->default('1'),

                                Select::make('modo')
                                    ->label('Modo')
                                    ->options([
                                        'produccion' => 'Producción',
                                        'beta' => 'Beta',
                                    ])
                                    ->default('produccion'),

                                Toggle::make('estado')
                                    ->label('Activo')
                                    ->default(true),
                            ]),
                        TextInput::make('propaganda')
                            ->label('Propaganda / Lema')
                            ->maxLength(250)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmpresas::route('/'),
            'create' => Pages\CreateEmpresa::route('/create'),
            'edit' => Pages\EditEmpresa::route('/{record}/edit'),
        ];
    }
}
