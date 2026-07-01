<div style="
    border-radius: 1.25rem;
    padding: 2rem;
    background: rgba(255,255,255,.07);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255,255,255,.13);
    box-shadow: 0 25px 50px -12px rgba(0,0,0,.4), inset 0 1px 0 rgba(255,255,255,.1);
    animation: fadeUp .5s ease-out .06s forwards;
    opacity: 0;
">
    <h2 style="font-size:1.5rem;font-weight:700;color:white;margin:0 0 .25rem;">Bienvenido</h2>
    <p style="color:#93c5fd;font-size:.75rem;margin:0 0 1.75rem;">Ingresa tus credenciales para acceder al sistema</p>

    <form wire:submit="authenticate">
        <div style="display:flex;flex-direction:column;gap:1rem;">
            {{ $this->form }}
        </div>

        <div style="margin-top:1.25rem;">
            <button type="submit" style="
                width:100%;border:none;cursor:pointer;border-radius:.75rem;
                background:linear-gradient(to right,#1d4ed8,#3b82f6);
                color:white;padding:.875rem 1rem;font-size:.875rem;font-weight:700;
                box-shadow:0 4px 15px rgba(29,78,216,.45);
                transition:all .15s ease;letter-spacing:.02em;
            ">
                <span wire:loading.remove wire:target="authenticate">Entrar al sistema</span>
                <span wire:loading wire:target="authenticate" style="align-items:center;justify-content:center;gap:.5rem;">
                    <svg style="height:1rem;width:1rem;animation:spin 1s linear infinite;" fill="none" viewBox="0 0 24 24">
                        <circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Verificando...
                </span>
            </button>
        </div>
    </form>
</div>

<style>
    @keyframes fadeUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
    @keyframes spin   { to{transform:rotate(360deg)} }

    /* Filament field labels */
    .fi-fo-field-wrp label,
    .fi-fo-field-label,
    .fi-fo-field-label label,
    .fi-fo-field-label > span:first-child {
        color: rgba(147,197,253,1) !important;
        font-size: 0.68rem !important;
        font-weight: 700 !important;
        letter-spacing: 0.08em !important;
        text-transform: uppercase !important;
    }
    /* Input wrapper — quita el border que Filament pone en el wrp */
    .fi-input-wrp {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
    }
    .fi-input-wrp-content-ctn {
        background: rgba(255,255,255,.14) !important;
        border: 1.5px solid rgba(255,255,255,.28) !important;
        border-radius: 0.625rem !important;
        transition: border-color .15s, box-shadow .15s !important;
    }
    .fi-input-wrp-content-ctn:focus-within {
        border-color: #60a5fa !important;
        box-shadow: 0 0 0 3px rgba(96,165,250,.3) !important;
    }
    .fi-input {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        color: white !important;
        padding: 0.625rem 0.75rem !important;
    }
    .fi-input::placeholder { color: rgba(147,197,253,.45) !important; }
    .fi-input:focus { outline: none !important; box-shadow: none !important; }
    .fi-fo-checkbox label,
    .fi-fo-checkbox span { color: rgba(255,255,255,.75) !important; font-size: 0.8rem !important; }
</style>
