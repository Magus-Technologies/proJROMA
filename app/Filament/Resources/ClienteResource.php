<?php

namespace App\Filament\Resources;

use App\Models\Cliente;
use App\Models\TmsMercado;
use App\Filament\Resources\ClienteResource\Pages;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;

class ClienteResource extends Resource
{
    use \App\Filament\Concerns\HasClienteBuscador;
    use \App\Filament\Concerns\VerificaPermisoDeAcceso;

    public const PERMISO_ACCESO = 'clientes.ver';

    protected static ?string $model = Cliente::class;

    protected static string|BackedEnum|null $navigationIcon  = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Clientes';
    protected static string|\UnitEnum|null $navigationGroup = 'Maestros';
    protected static ?int    $navigationSort  = 1;
    protected static ?string $label           = 'Cliente';
    protected static ?string $pluralLabel     = 'Clientes';

    public static function form(Schema $schema): Schema
    {
        // Mismo formulario que usa el botón "+" del buscador de cliente
        // (venta / cotización) — fuente única en el trait HasClienteBuscador.
        return $schema->components(static::clienteFormFields());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('documento')->label('RUC/DNI')->searchable()->sortable(),
                TextColumn::make('datos')->label('Nombre / Razón Social')->searchable()->sortable()->wrap(),
                TextColumn::make('distrito')->label('Distrito')->toggleable(),
                TextColumn::make('mercadoTms.nombre')->label('Mercado / Zona')->placeholder('—')->toggleable(),
                TextColumn::make('telefono')->label('Teléfono')->toggleable(),
                TextColumn::make('email')->label('Email')->toggleable(),
                TextColumn::make('ultima_venta')->label('Última venta')->date('d/m/Y')->sortable()->toggleable(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (Action $action, Cliente $record) {
                        if ($record->ventas()->exists()) {
                            Notification::make()
                                ->danger()
                                ->title('No se puede eliminar')
                                ->body('El cliente tiene ventas registradas.')
                                ->send();
                            $action->cancel();
                        }
                    }),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('datos');
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with('mercadoTms')
            ->where('id_empresa', (int) session('id_empresa'));
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientes::route('/'),
        ];
    }
}
