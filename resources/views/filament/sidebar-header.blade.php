@php
    $empresa = session('nombre_empresa');
@endphp
<div class="fi-sidebar-custom-header">
    <button
        type="button"
        x-data="{}"
        x-on:click="$store.sidebar.isOpen ? $store.sidebar.close() : $store.sidebar.open()"
        class="fi-sidebar-custom-header-toggle"
        x-tooltip="{
            content: $store.sidebar.isOpen ? 'Colapsar' : 'Expandir',
            placement: document.dir === 'rtl' ? 'left' : 'right',
            theme: $store.theme,
        }"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 5l7 7-7 7"/>
        </svg>
    </button>
    <div class="fi-sidebar-custom-header-brand" x-show="$store.sidebar.isOpen" x-cloak>
        <div class="fi-sidebar-custom-header-brand-name">ProjRoma</div>
        @if($empresa)
            <div class="fi-sidebar-custom-header-brand-empresa">{{ $empresa }}</div>
        @endif
    </div>
</div>
