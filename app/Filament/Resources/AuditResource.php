<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditResource\Pages;
use App\Models\Audit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditResource extends Resource
{
    protected static ?string $model = Audit::class;

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Auditoría';
    protected static string|\UnitEnum|null $navigationGroup  = 'Administración';
    protected static ?int    $navigationSort  = 5;
    protected static ?string $label           = 'Auditoría';
    protected static ?string $pluralLabel     = 'Auditoría';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user_name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('event')
                    ->label('Evento')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default   => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'created' => 'CREADO',
                        'updated' => 'EDITADO',
                        'deleted' => 'ELIMINADO',
                        default   => strtoupper($state),
                    }),
                TextColumn::make('model_type')
                    ->label('Modelo')
                    ->formatStateUsing(fn (string $state) => class_basename($state))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('model_id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('url')
                    ->label('URL')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                ViewAction::make()
                    ->modalHeading('Detalle de auditoría')
                    ->mutateRecordDataUsing(function (Audit $record): array {
                        $data = $record->toArray();
                        $out = [
                            'Fecha'     => $record->created_at->format('d/m/Y H:i:s'),
                            'Usuario'   => "{$record->user_name} (ID: {$record->user_id})",
                            'Rol'       => $record->user_rol,
                            'Evento'    => $record->event,
                            'Modelo'    => class_basename($record->model_type),
                            'ID'        => $record->model_id,
                            'Empresa'   => $record->empresa_id,
                            'IP'        => $record->ip_address,
                            'URL'       => $record->url,
                            'Método'    => $record->method,
                        ];

                        if ($record->old_values) {
                            $out['Valores anteriores'] = json_encode($record->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                        }
                        if ($record->new_values) {
                            $out['Valores nuevos'] = json_encode($record->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                        }

                        return $out;
                    })
                    ->modalContent(fn (array $data) => view('filament.audit-detail', ['data' => $data])),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAudits::route('/'),
        ];
    }
}
