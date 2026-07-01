<div class="fi-sidebar-custom-footer">
    <button
        type="button"
        x-data="{}"
        x-on:click="$store.sidebar.isOpen ? $store.sidebar.close() : $store.sidebar.open()"
        class="fi-sidebar-custom-footer-btn"
        x-tooltip="{
            content: $store.sidebar.isOpen ? 'Colapsar menú' : 'Expandir menú',
            placement: document.dir === 'rtl' ? 'left' : 'right',
            theme: $store.theme,
        }"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
             :class="$store.sidebar.isOpen ? '' : 'rotate-180'">
            <path d="M9 18l6-6-6-6"/>
        </svg>
        <span x-show="$store.sidebar.isOpen" x-cloak>Colapsar</span>
    </button>
</div>
