<?php

namespace App\Filament\Resources;

use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use App\Filament\Resources\ProductoResource\Pages;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static string|BackedEnum|null $navigationIcon  = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Productos';
    protected static string|\UnitEnum|null $navigationGroup  = 'Almacén';
    protected static ?int    $navigationSort  = 1;
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
            TextInput::make('cantidad')->label('Stock')->numeric(),
            TextInput::make('medida')->label('Medida')->maxLength(50)
                ->placeholder('Ej: Unidad, Caja, Kg'),
            Select::make('id_categoria')
                ->label('Categoría')
                ->options(fn () => Categoria::where('id_empresa', (int) session('id_empresa'))
                    ->pluck('nombre', 'id_categoria'))
                ->searchable()
                ->nullable()
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
            Select::make('id_marca')
                ->label('Marca')
                ->options(fn () => Marca::where('id_empresa', (int) session('id_empresa'))
                    ->pluck('nombre', 'id_marca'))
                ->searchable()
                ->nullable()
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
        return $table
            ->columns([
                ImageColumn::make('imagen')
                    ->label('IMG')
                    ->disk('public')
                    ->size(40)
                    ->defaultImageUrl(fn () => null)
                    ->toggleable(),
                TextColumn::make('cod_barra')->label('Cód. Barra')->searchable()->sortable()->toggleable(),
                TextColumn::make('codigo')->label('Código')->searchable()->sortable(),
                TextColumn::make('descripcion')->label('Descripción')->searchable()->sortable()->wrap(),
                TextColumn::make('medida')->label('Medida')->toggleable(),
                TextColumn::make('categoria.nombre')->label('Categoría')->sortable()->toggleable(),
                TextColumn::make('marca.nombre')->label('Marca')->sortable()->toggleable(),
                TextColumn::make('precio')->label('Precio')->money('PEN')->sortable(),
                TextColumn::make('costo')->label('Costo')->money('PEN')->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('cantidad')->label('Stock')->sortable()
                    ->color(fn ($state) => $state <= 0 ? 'danger' : ($state <= 5 ? 'warning' : 'success')),
                IconColumn::make('activo')->label('Activo')->boolean()->toggleable(),
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
            ->defaultSort('descripcion');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['categoria', 'marca'])
            ->where('id_empresa', (int) session('id_empresa'));
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductos::route('/'),
        ];
    }
}
