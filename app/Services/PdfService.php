<?php

namespace App\Services;

use App\Models\Empresa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    private array $paper = [0, 0, 595.28, 841.89];
    private string $orientation = 'portrait';
    private array $options = [
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled'      => false,
        'defaultFont'          => 'sans-serif',
        'dpi'                  => 96,
        'margin_top'           => 10,
        'margin_bottom'        => 10,
        'margin_left'          => 10,
        'margin_right'         => 10,
    ];

    public function setPaper(float $w, float $h, string $orientation = 'portrait'): static
    {
        $this->paper = [0, 0, $w, $h];
        $this->orientation = $orientation;
        return $this;
    }

    public function setMargins(int $top, int $bottom, int $left, int $right): static
    {
        $this->options['margin_top'] = $top;
        $this->options['margin_bottom'] = $bottom;
        $this->options['margin_left'] = $left;
        $this->options['margin_right'] = $right;
        return $this;
    }

    public function setOption(string $key, mixed $value): static
    {
        $this->options[$key] = $value;
        return $this;
    }

    public function headerHtml(object $empresa, string $tituloDoc, string $numeroDoc): string
    {
        return view('pdf.partials.header-pdf', compact('empresa', 'tituloDoc', 'numeroDoc'))->render();
    }

    public function footerHtml(?string $usuario = null): string
    {
        return view('pdf.partials.footer-pdf', compact('usuario'))->render();
    }

    public function generar(string $view, array $data, string $filename): Response
    {
        $pdf = Pdf::loadView($view, $data + ['logoBase64' => $this->logoBase64($data)])
            ->setPaper($this->paper, $this->orientation)
            ->setOptions($this->options);

        return $pdf->stream($filename);
    }

    public function descargar(string $view, array $data, string $filename): Response
    {
        $pdf = Pdf::loadView($view, $data + ['logoBase64' => $this->logoBase64($data)])
            ->setPaper($this->paper, $this->orientation)
            ->setOptions($this->options);

        return $pdf->download($filename);
    }

    /**
     * El logo que va en la cabecera del PDF.
     *
     * Manda el que la empresa cargó en su ficha; el del sistema es solo el
     * respaldo para cuando no cargó ninguno. Antes se usaba siempre el del
     * sistema, así que los reportes que no pasaban el logo a mano salían con
     * la marca de ProjRoma en lugar de la del cliente.
     *
     * @param  array<string, mixed>  $data  datos de la vista; si trae la
     *                                      empresa, se usa esa en vez de la
     *                                      de la sesión.
     */
    private function logoBase64(array $data = []): string
    {
        $empresa = $data['empresa'] ?? null;

        if (! $empresa instanceof Empresa) {
            $empresa = Empresa::find((int) session('id_empresa'));
        }

        return static::logoDeEmpresa($empresa) ?: $this->logoDelSistema();
    }

    /** El logo cargado en Empresa, listo para incrustar en el PDF. */
    public static function logoDeEmpresa(?Empresa $empresa): string
    {
        if (! $empresa?->logo) {
            return '';
        }

        // Subidas de Filament (disco public) y, si no, la ruta legada.
        foreach ([
            Storage::disk('public')->exists($empresa->logo)
                ? Storage::disk('public')->path($empresa->logo)
                : null,
            public_path('storage/' . $empresa->logo),
        ] as $ruta) {
            if ($ruta && file_exists($ruta)) {
                return 'data:' . mime_content_type($ruta) . ';base64,' . base64_encode(file_get_contents($ruta));
            }
        }

        return '';
    }

    private function logoDelSistema(): string
    {
        $path = public_path('logos/logo.svg');
        if (!file_exists($path)) return '';
        return 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($path));
    }

    public static function a4(): static
    {
        return new static();
    }

    public static function ticket(float $width = 226.77, float $height = 900): static
    {
        // Los márgenes del ticket se controlan con @page en la vista
        // (dompdf ignora margin_* como opción).
        return (new static())
            ->setPaper($width, $height);
    }
}
