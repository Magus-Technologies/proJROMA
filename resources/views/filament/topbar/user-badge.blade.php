@auth
    @php
        $u        = auth()->user();
        $nombre   = trim(($u->nombres ?? '') . ' ' . ($u->apellidos ?? '')) ?: ($u->usuario ?? 'Usuario');
        $rol      = $u->rol?->nombre ?? 'Usuario';
        $sucursal = session('sucursal');
    @endphp

    <div class="fi-user-badge">
        <span class="fi-user-badge-name">{{ $nombre }}</span>
        <span class="fi-user-badge-meta">
            {{ $rol }}@if($sucursal) · Suc. {{ $sucursal }}@endif
        </span>
    </div>
@endauth
