<?php

namespace App\Filament\Actions;

use Closure;
use Filament\Actions\Action;

/**
 * Reusable PDF preview modal with share shortcuts.
 *
 * Renders the given PDF inside a modal iframe, with Print / WhatsApp / Email
 * buttons in the footer. Extra footer actions (e.g. "Convertir a venta") can
 * be appended per resource. The only bottom action is "Cerrar".
 */
class PdfPreviewAction
{
    public static function make(
        Closure $pdfUrl,
        Closure $titulo,
        ?Closure $whatsappUrl = null,
        ?Closure $emailUrl = null,
        array $accionesExtra = [],
        string $name = 'vista_previa',
    ): Action {
        return Action::make($name)
            ->label('Vista previa')
            ->icon('heroicon-o-eye')
            ->color('info')
            ->modalHeading(fn ($record, $livewire): string => $titulo($record, $livewire))
            ->modalContent(fn ($record) => view('filament.modals.pdf-preview', [
                'url' => $pdfUrl($record),
            ]))
            ->modalWidth('5xl')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Cerrar')
            ->extraModalFooterActions(array_merge(
                array_values(array_filter([
                    Action::make("{$name}_imprimir")
                        ->label('Imprimir')
                        ->icon('heroicon-m-printer')
                        ->color('danger')
                        ->url(fn ($record): string => $pdfUrl($record))
                        ->openUrlInNewTab(),

                    $whatsappUrl ? Action::make("{$name}_whatsapp")
                        ->label('WhatsApp')
                        ->icon('custom-whatsapp')
                        ->color('success')
                        ->url(fn ($record): string => $whatsappUrl($record))
                        ->openUrlInNewTab() : null,

                    $emailUrl ? Action::make("{$name}_email")
                        ->label('Enviar por Email')
                        ->icon('heroicon-m-envelope')
                        ->color('gray')
                        ->url(fn ($record): string => $emailUrl($record)) : null,
                ])),
                $accionesExtra,
            ));
    }
}
