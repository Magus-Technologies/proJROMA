<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;

class DemoComponentes extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Demo Componentes';

    protected static ?string $title = 'Demo de Componentes Filament';

    protected string $view = 'filament.pages.demo-componentes';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('Ej: Juan Pérez'),

                Select::make('tipo_documento')
                    ->label('Tipo de Documento')
                    ->options([
                        'boleta' => 'Boleta',
                        'factura' => 'Factura',
                        'nota_venta' => 'Nota de Venta',
                    ])
                    ->searchable()
                    ->native(false),

                Select::make('cliente_id')
                    ->label('Cliente')
                    ->relationship('cliente', 'datos')
                    ->searchable()
                    ->preload(),

                DatePicker::make('fecha')
                    ->label('Fecha')
                    ->native(false)
                    ->displayFormat('d/m/Y'),

                TimePicker::make('hora')
                    ->label('Hora')
                    ->seconds(false),

                Toggle::make('activo')
                    ->label('¿Activo?')
                    ->onColor('success')
                    ->offColor('danger'),

                ColorPicker::make('color_etiqueta')
                    ->label('Color de Etiqueta'),

                Textarea::make('observaciones')
                    ->label('Observaciones')
                    ->rows(3)
                    ->maxLength(500),

                RichEditor::make('descripcion')
                    ->label('Descripción')
                    ->toolbarButtons([
                        'bold', 'italic', 'underline', 'bulletList', 'orderedList',
                    ]),

                TagsInput::make('etiquetas')
                    ->label('Etiquetas')
                    ->separator(',')
                    ->suggestions(['urgente', 'pendiente', 'revisado']),

                FileUpload::make('archivo')
                    ->label('Archivo')
                    ->directory('demo')
                    ->maxSize(2048),

                KeyValue::make('configuracion')
                    ->label('Configuración')
                    ->keyLabel('Clave')
                    ->valueLabel('Valor'),

                Repeater::make('items')
                    ->label('Productos')
                    ->schema([
                        TextInput::make('producto')->required(),
                        TextInput::make('cantidad')->numeric()->required(),
                        TextInput::make('precio')->numeric()->prefix('S/'),
                    ])
                    ->columns(3)
                    ->addActionLabel('Agregar producto'),
            ])
            ->statePath('data')
            ->columns(2);
    }

    // ── Acción: Modal de confirmación ─────────────────────────────
    public function abrirModalConfirmacionAction(): Action
    {
        return Action::make('abrirModalConfirmacion')
            ->label('Modal de Confirmación')
            ->color('warning')
            ->icon('heroicon-o-question-mark-circle')
            ->requiresConfirmation()
            ->modalHeading('¿Estás seguro?')
            ->modalDescription('Esta acción no se puede deshacer.')
            ->modalSubmitActionLabel('Sí, confirmar')
            ->action(function () {
                Notification::make()
                    ->title('Acción confirmada')
                    ->success()
                    ->send();
            });
    }

    // ── Acción: Modal con formulario ──────────────────────────────
    public function abrirModalFormularioAction(): Action
    {
        return Action::make('abrirModalFormulario')
            ->label('Modal con Formulario')
            ->color('primary')
            ->icon('heroicon-o-pencil-square')
            ->modalWidth(MaxWidth::Xl)
            ->modalHeading('Nuevo Cliente')
            ->form([
                TextInput::make('nombre_cliente')
                    ->label('Nombre')
                    ->required(),
                TextInput::make('documento')
                    ->label('DNI / RUC')
                    ->required()
                    ->maxLength(11),
                Select::make('tipo')
                    ->options([
                        'natural' => 'Persona Natural',
                        'juridica' => 'Persona Jurídica',
                    ]),
                Textarea::make('direccion')
                    ->label('Dirección'),
            ])
            ->action(function (array $data) {
                Notification::make()
                    ->title('Cliente creado')
                    ->body("Cliente: {$data['nombre_cliente']} - {$data['documento']}")
                    ->success()
                    ->send();
            });
    }

    // ── Acción: Modal con vista de detalle ────────────────────────
    public function abrirModalSlideOverAction(): Action
    {
        return Action::make('abrirModalSlideOver')
            ->label('Slide Over')
            ->color('success')
            ->icon('heroicon-o-eye')
            ->slideOver()
            ->modalHeading('Detalle del Documento')
            ->modalContent(function () {
                return view('filament.components.demo-modal-content', [
                    'documento' => 'F001-00001234',
                    'cliente' => 'Cliente Demo S.A.C.',
                    'fecha' => now()->format('d/m/Y'),
                    'total' => 'S/ 1,250.00',
                    'estado' => 'Activa',
                ]);
            });
    }

    // ── Notificaciones directas ───────────────────────────────────
    public function notificarSuccessAction(): Action
    {
        return Action::make('notificarSuccess')
            ->label('Success')
            ->color('success')
            ->action(function () {
                Notification::make()
                    ->title('¡Éxito!')
                    ->body('Operación completada correctamente.')
                    ->success()
                    ->send();
            });
    }

    public function notificarErrorAction(): Action
    {
        return Action::make('notificarError')
            ->label('Error')
            ->color('danger')
            ->action(function () {
                Notification::make()
                    ->title('Error')
                    ->body('Ocurrió un problema al procesar.')
                    ->danger()
                    ->send();
            });
    }

    public function notificarInfoAction(): Action
    {
        return Action::make('notificarInfo')
            ->label('Info')
            ->color('info')
            ->action(function () {
                Notification::make()
                    ->title('Información')
                    ->body('Datos actualizados en segundo plano.')
                    ->info()
                    ->send();
            });
    }

    public function notificarWarningAction(): Action
    {
        return Action::make('notificarWarning')
            ->label('Warning')
            ->color('warning')
            ->action(function () {
                Notification::make()
                    ->title('Advertencia')
                    ->body('Revisa los campos obligatorios.')
                    ->warning()
                    ->send();
            });
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->abrirModalConfirmacionAction(),
            $this->abrirModalFormularioAction(),
            $this->abrirModalSlideOverAction(),
        ];
    }
}
