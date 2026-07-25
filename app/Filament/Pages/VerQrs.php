<?php

namespace App\Filament\Pages;

use App\Models\BilleteraDigital;
use Filament\Pages\Page;

class VerQrs extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationLabel = 'Códigos QR';
    protected static string|\UnitEnum|null $navigationGroup = 'Caja';
    protected static ?int $navigationSort = 7;

    protected static ?string $title = 'Códigos QR';
    protected string $view = 'filament.pages.ver-qrs';

    public function getQrs(): array
    {
        $empresaId = (int) session('id_empresa');

        return BilleteraDigital::where('id_empresa', $empresaId)
            ->where('estado', '1')
            ->whereNotNull('qr')
            ->with('billeteraTipo')
            ->get()
            ->toArray();
    }
}
