<table class="header-table" style="width:100%;border-collapse:collapse;margin-bottom:16px">
    <tr>
        <td style="vertical-align:top;width:60%">
            @if(!empty($logoBase64))
            <img src="{{ $logoBase64 }}" style="max-height:60px;max-width:190px;margin-bottom:5px;display:block;">
            @endif
            <div style="font-size:15pt;font-weight:bold;color:#dc2626;text-transform:uppercase;line-height:1.2;margin-bottom:3px">
                {{ $empresa->razon_social ?? 'EMPRESA' }}
            </div>
            <div style="font-size:7.5pt;color:#555;line-height:1.6">
                {{ $empresa->direccion ?? '' }}<br>
                @if($empresa->telefono)Central Telefónica: {{ $empresa->telefono }}<br>@endif
                @if($empresa->email)Email: {{ $empresa->email }}<br>@endif
                RUC: {{ $empresa->ruc ?? '-' }}
            </div>
        </td>
        <td style="vertical-align:top;width:40%;text-align:right">
            <div style="display:inline-block;border:2px solid #bfc4cc;text-align:center;min-width:175px">
                <div style="background:#bfc4cc;font-size:7.5pt;font-weight:bold;padding:5px 12px;color:#000">
                    R.U.C. {{ $empresa->ruc ?? '-' }}
                </div>
                <div style="font-size:8pt;padding:7px 12px 9px;font-weight:bold;color:#000">
                    <div style="font-size:13pt;text-transform:uppercase;letter-spacing:0.5px;margin:3px 0">{{ $tituloDoc }}</div>
                    <div style="font-size:11pt;border-top:1px solid #999;padding-top:5px;margin-top:5px">{{ $numeroDoc }}</div>
                </div>
            </div>
        </td>
    </tr>
</table>
