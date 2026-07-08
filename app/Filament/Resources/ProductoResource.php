<?php

namespace App\Filament\Resources;

use App\Models\Almacen;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Presentacion;
use App\Models\Producto;
use App\Models\Subcategoria;
use App\Models\Submarca;
use App\Models\UnidadMedida;
use App\Filament\Clusters\Productos;
use App\Filament\Resources\ProductoResource\Pages;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ProductoResource extends Resource
{
    use \App\Filament\Concerns\VerificaPermisoDeAcceso;

    public const PERMISO_ACCESO = 'productos.ver';

    protected static ?string $model = Producto::class;
    protected static ?string $cluster = Productos::class;

    protected static string|BackedEnum|null $navigationIcon  = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Productos';
    protected static ?int    $navigationSort  = 0;
    protected static ?string $label           = 'Producto';
    protected static ?string $pluralLabel     = 'Productos';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('cod_barra')->label('Código de Barra')->maxLength(50),
            TextInput::make('codigo')->label('Código Interno')->maxLength(50),
            TextInput::make('descripcion')->label('Descripción')->required()->maxLength(200)->columnSpanFull(),
            TextInput::make('precio')->label('Precio')->numeric()->prefix('S/'),
            TextInput::make('costo')->label('Costo')->numeric()->prefix('S/'),
            TextInput::make('peso_bruto')->label('Peso')->numeric()->suffix('kg')
                ->required()->minValue(0.01)
                ->helperText('Peso por unidad. Se usa para asignar vehículo en despachos según su capacidad de carga.'),
            Select::make('medida')->label('Unidad de medida')
                ->options(fn () => UnidadMedida::where('id_empresa', (int) session('id_empresa'))
                    ->where('estado', 1)->orderBy('nombre')->pluck('nombre', 'nombre'))
                ->searchable()->nullable()
                ->createOptionForm([
                    TextInput::make('nombre')->label('Nombre')->required()->maxLength(60),
                    TextInput::make('abreviatura')->label('Abreviatura')->maxLength(15),
                ])
                ->createOptionUsing(fn (array $data): string => UnidadMedida::firstOrCreate(
                    ['id_empresa' => (int) session('id_empresa'), 'nombre' => trim($data['nombre'])],
                    ['abreviatura' => $data['abreviatura'] ?? null, 'estado' => 1]
                )->nombre),

            Select::make('presentaciones')->label('Presentación (cómo compra)')
                ->options(fn () => Presentacion::where('id_empresa', (int) session('id_empresa'))
                    ->where('estado', 1)->orderBy('nombre')->pluck('nombre', 'nombre'))
                ->searchable()->nullable()
                ->createOptionForm([
                    TextInput::make('nombre')->label('Nombre')->required()->maxLength(60),
                ])
                ->createOptionUsing(fn (array $data): string => Presentacion::firstOrCreate(
                    ['id_empresa' => (int) session('id_empresa'), 'nombre' => trim($data['nombre'])],
                    ['estado' => 1]
                )->nombre),

            TextInput::make('cnt_presenta')->label('Unidades por presentación')
                ->numeric()->minValue(0)->placeholder('Ej: 20 (unidades por caja)'),

            Select::make('id_categoria')
                ->label('Categoría')
                ->options(fn () => Categoria::where('id_empresa', (int) session('id_empresa'))
                    ->pluck('nombre', 'id_categoria'))
                ->searchable()
                ->nullable()
                ->live()
                ->afterStateUpdated(fn (Set $set) => $set('id_subcategoria', null))
                ->createOptionForm([
                    TextInput::make('nombre')->label('Nombre')->required()->maxLength(100),
                ])
                ->createOptionUsing(function (array $data): int {
                    return Categoria::create([
                        'nombre'      => $data['nombre'],
                        'id_empresa'  => (int) session('id_empresa'),
                        'estado'      => '1',
                    ])->id_categoria;
                }),
            Select::make('id_subcategoria')
                ->label('Subcategoría')
                ->options(fn (Get $get) => Subcategoria::where('id_empresa', (int) session('id_empresa'))
                    ->where('id_categoria', $get('id_categoria'))
                    ->pluck('nombre', 'id_subcategoria'))
                ->searchable()
                ->nullable()
                ->disabled(fn (Get $get) => ! $get('id_categoria'))
                ->createOptionForm([
                    TextInput::make('nombre')->label('Nombre')->required()->maxLength(100),
                ])
                ->createOptionUsing(function (array $data, Get $get): int {
                    return Subcategoria::create([
                        'nombre'       => $data['nombre'],
                        'id_categoria' => $get('id_categoria'),
                        'id_empresa'   => (int) session('id_empresa'),
                        'estado'       => '1',
                    ])->id_subcategoria;
                }),
            Select::make('id_marca')
                ->label('Marca')
                ->options(fn () => Marca::where('id_empresa', (int) session('id_empresa'))
                    ->pluck('nombre', 'id_marca'))
                ->searchable()
                ->nullable()
                ->live()
                ->afterStateUpdated(fn (Set $set) => $set('id_submarca', null))
                ->createOptionForm([
                    TextInput::make('nombre')->label('Nombre')->required()->maxLength(100),
                ])
                ->createOptionUsing(function (array $data): int {
                    return Marca::create([
                        'nombre'     => $data['nombre'],
                        'id_empresa' => (int) session('id_empresa'),
                        'estado'     => '1',
                    ])->id_marca;
                }),
            Select::make('id_submarca')
                ->label('Submarca')
                ->options(fn (Get $get) => Submarca::where('id_empresa', (int) session('id_empresa'))
                    ->where('id_marca', $get('id_marca'))
                    ->pluck('nombre', 'id_submarca'))
                ->searchable()
                ->nullable()
                ->disabled(fn (Get $get) => ! $get('id_marca'))
                ->createOptionForm([
                    TextInput::make('nombre')->label('Nombre')->required()->maxLength(100),
                ])
                ->createOptionUsing(function (array $data, Get $get): int {
                    return Submarca::create([
                        'nombre'     => $data['nombre'],
                        'id_marca'   => $get('id_marca'),
                        'id_empresa' => (int) session('id_empresa'),
                        'estado'     => '1',
                    ])->id_submarca;
                }),
            Toggle::make('activo')->label('Activo')->default(true),
            FileUpload::make('imagen')
                ->label('Imagen')
                ->image()
                ->disk('public')
                ->directory('productos')
                ->imagePreviewHeight('100')
                ->maxSize(2048)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        $sortUsing = fn (string $column) => fn (Builder $query, string $direction) => $query->orderBy($column, $direction);

        return $table
            ->columns([
                ImageColumn::make('imagen')
                    ->label('IMG')
                    ->disk('public')
                    ->size(40)
                    ->defaultImageUrl(fn () => null)
                    ->toggleable(),
                TextColumn::make('cod_barra')->label('Cód. Barra')->searchable()->sortable(query: $sortUsing('cod_barra'))->toggleable(),
                TextColumn::make('codigo')->label('Código')->searchable()->sortable(query: $sortUsing('codigo')),
                TextColumn::make('descripcion')->label('Descripción')->searchable()->sortable(query: $sortUsing('descripcion'))->wrap(),
                TextColumn::make('medida')->label('Medida')->toggleable(),
                TextColumn::make('peso_bruto')->label('Peso')->toggleable()
                    ->formatStateUsing(fn ($state) => $state > 0 ? number_format((float) $state, 2) . ' kg' : '—'),
                TextColumn::make('categoria.nombre')->label('Categoría')->sortable()->toggleable(),
                TextColumn::make('marca.nombre')->label('Marca')->sortable()->toggleable(),
                TextColumn::make('precio')->label('Precio')->money('PEN')->sortable(query: $sortUsing('precio')),
                TextColumn::make('costo')->label('Costo')->money('PEN')->sortable(query: $sortUsing('costo'))->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('cantidad')->label('Stock')->sortable(query: $sortUsing('cantidad'))
                    ->color(fn ($state) => $state <= 0 ? 'danger' : ($state <= 5 ? 'warning' : 'success'))
                    ->icon('heroicon-o-building-storefront')
                    ->iconPosition(IconPosition::After)
                    ->tooltip('Ver stock en todos los almacenes')
                    ->action(
                        Action::make('verStockAlmacenes')
                            ->modalHeading(fn (Producto $record) => "Stock por almacén — {$record->descripcion}")
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Cerrar')
                            ->schema(fn (Producto $record) => [
                                TextEntry::make('total')->label('Total en todos los almacenes')
                                    ->state($record->cantidad ?? 0)
                                    ->badge()->color('primary')->size('lg'),
                                RepeatableEntry::make('almacenes')
                                    ->label('')
                                    ->state(function () use ($record) {
                                        $nombres = Almacen::where('id_empresa', (int) session('id_empresa'))
                                            ->pluck('nombre', 'codigo');

                                        $query = Producto::where('id_empresa', (int) session('id_empresa'));
                                        $query = filled($record->codigo)
                                            ? $query->where('codigo', $record->codigo)
                                            : $query->where('id_producto', $record->id_producto);

                                        return $query->orderBy('almacen')->get(['almacen', 'cantidad'])
                                            ->map(fn ($p) => [
                                                'nombre'   => $nombres[$p->almacen] ?? ($p->almacen ?: 'Sin almacén'),
                                                'cantidad' => $p->cantidad ?? 0,
                                            ])->all();
                                    })
                                    ->schema([
                                        TextEntry::make('nombre')->label('Almacén'),
                                        TextEntry::make('cantidad')->label('Cantidad')
                                            ->badge()
                                            ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),
                                    ])
                                    ->columns(2),
                            ])
                    ),
                IconColumn::make('activo')->label('Estado')->boolean()->toggleable(),
            ])
            ->filters([
                TernaryFilter::make('activo')->label('Estado'),
                SelectFilter::make('id_categoria')
                    ->label('Categoría')
                    ->options(fn () => Categoria::where('id_empresa', (int) session('id_empresa'))
                        ->pluck('nombre', 'id_categoria')),
                SelectFilter::make('id_marca')
                    ->label('Marca')
                    ->options(fn () => Marca::where('id_empresa', (int) session('id_empresa'))
                        ->pluck('nombre', 'id_marca')),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('descripcion')
            ->defaultKeySort(false);
    }

    public static function getEloquentQuery(): Builder
    {
        // Un mismo producto (mismo "codigo") tiene una fila por almacén.
        // Agrupamos para listar el catálogo una sola vez, sumando el stock de todos los almacenes.
        $groupKey = "IF(codigo IS NULL OR codigo = '', CONCAT('_pid_', id_producto), codigo)";

        return parent::getEloquentQuery()
            ->select([
                DB::raw('MIN(id_producto) as id_producto'),
                DB::raw('MIN(codigo) as codigo'),
                DB::raw('MIN(cod_barra) as cod_barra'),
                DB::raw('MIN(descripcion) as descripcion'),
                DB::raw('MIN(medida) as medida'),
                DB::raw('MIN(presentaciones) as presentaciones'),
                DB::raw('MIN(cnt_presenta) as cnt_presenta'),
                DB::raw('MIN(peso_bruto) as peso_bruto'),
                DB::raw('MIN(id_categoria) as id_categoria'),
                DB::raw('MIN(id_subcategoria) as id_subcategoria'),
                DB::raw('MIN(id_marca) as id_marca'),
                DB::raw('MIN(id_submarca) as id_submarca'),
                DB::raw('MIN(precio) as precio'),
                DB::raw('MIN(costo) as costo'),
                DB::raw('SUM(cantidad) as cantidad'),
                DB::raw('MIN(activo) as activo'),
                DB::raw('MIN(imagen) as imagen'),
                'id_empresa',
            ])
            ->where('id_empresa', (int) session('id_empresa'))
            ->groupBy('id_empresa')
            ->groupByRaw($groupKey)
            ->with(['categoria', 'marca']);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductos::route('/'),
        ];
    }
}
