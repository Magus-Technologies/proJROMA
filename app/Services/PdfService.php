<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

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
        $pdf = Pdf::loadView($view, $data + ['logoBase64' => $this->logoBase64()])
            ->setPaper($this->paper, $this->orientation)
            ->setOptions($this->options);

        return $pdf->stream($filename);
    }

    public function descargar(string $view, array $data, string $filename): Response
    {
        $pdf = Pdf::loadView($view, $data + ['logoBase64' => $this->logoBase64()])
            ->setPaper($this->paper, $this->orientation)
            ->setOptions($this->options);

        return $pdf->download($filename);
    }

    private function logoBase64(): string
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
        return (new static())
            ->setPaper($width, $height)
            ->setMargins(5, 5, 5, 5);
    }
}
