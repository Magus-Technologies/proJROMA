<div style="margin-top:20px;text-align:center;font-size:7.5px;color:#999;border-top:1px solid #ddd;padding-top:6px">
    Generado por ProjRoma — projroma.com | {{ now()->format('d/m/Y H:i:s') }}
    @if($usuario ?? null) | Usuario: {{ $usuario }}@endif
    | Pág. <span class="page"></span> de <span class="topage"></span>
</div>
