{{-- UI de permisos por módulo: buscador global + cards que abren modal.
     Las cards son las secciones .permisos-card renderizadas por Filament;
     aquí va el buscador, los estilos del modal y el JS de apertura/cierre. --}}
<div
    x-data="{
        q: '',
        filtrar() {
            const term = this.q.trim().toLowerCase();

            document.querySelectorAll('[data-card-modulo]').forEach((card) => {
                let visiblesCard = 0;

                card.querySelectorAll('[data-grupo-permisos]').forEach((grupo) => {
                    let visiblesGrupo = 0;

                    grupo.querySelectorAll('.fi-fo-checkbox-list-option-ctn').forEach((opcion) => {
                        const coincide = ! term || opcion.innerText.toLowerCase().includes(term);
                        opcion.style.display = coincide ? '' : 'none';
                        if (coincide) visiblesGrupo++;
                    });

                    grupo.style.display = visiblesGrupo ? '' : 'none';
                    visiblesCard += visiblesGrupo;

                    // Desplegar la sub-card si la búsqueda tiene coincidencias;
                    // volver a plegarla cuando se limpia el buscador
                    const seccion = grupo.querySelector('.fi-section');
                    if (seccion) {
                        if (term && visiblesGrupo) {
                            seccion.dispatchEvent(new CustomEvent('expand'));
                        } else if (! term) {
                            try { Alpine.$data(seccion).isCollapsed = true; } catch (e) {}
                        }
                    }
                });

                card.style.display = visiblesCard ? '' : 'none';
            });
        },
    }"
>
    <div class="fi-input-wrp" style="max-width: 28rem;">
        <div class="fi-input-wrp-prefix fi-input-wrp-prefix-has-content fi-inline">
            <svg class="fi-icon fi-size-md" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="fi-input-wrp-content-ctn">
            <input
                type="search"
                class="fi-input fi-input-has-inline-prefix"
                placeholder="Buscar permiso… (ej. anular, kardex, crear)"
                autocomplete="off"
                x-model="q"
                x-on:input.debounce.250ms="filtrar()"
                x-on:search="filtrar()"
            />
        </div>
    </div>
</div>

{{-- Botón de cierre del modal (visible solo con un módulo abierto) --}}
<button
    type="button"
    class="permisos-modal-cerrar"
    onclick="window.cerrarCardPermisos()"
    aria-label="Cerrar"
>
    ✕ Cerrar
</button>

<style>
    /* La estructura real es: div.permisos-card (wrapper) > section.fi-section
       > (header.fi-section-header + div.fi-section-content-ctn) */

    /* Card cerrada: solo la cabecera, con aspecto clickeable */
    .permisos-card { cursor: pointer; }
    .permisos-card .fi-section { transition: box-shadow .15s ease, transform .15s ease; }
    .permisos-card:not(.abierta):hover .fi-section {
        box-shadow: 0 6px 20px rgba(0, 0, 0, .10);
        transform: translateY(-2px);
    }
    .permisos-card:not(.abierta) .fi-section-content-ctn { display: none; }

    /* Card abierta = modal: el wrapper pasa a ser el backdrop y la
       sección interna (solo la hija directa) es el diálogo centrado */
    .permisos-card.abierta {
        position: fixed; inset: 0; z-index: 60;
        background: rgba(15, 23, 42, .60);
        display: flex; align-items: center; justify-content: center;
        padding: 2rem 1rem;
        cursor: default;
    }
    .permisos-card.abierta > .fi-section {
        width: min(60rem, 94vw);
        max-height: 85vh;
        display: flex; flex-direction: column;
        overflow: hidden;
        border-radius: .75rem;
        box-shadow: 0 25px 60px rgba(0, 0, 0, .35);
    }
    .permisos-card.abierta > .fi-section > .fi-section-header {
        flex-shrink: 0;
        border-bottom: 1px solid rgba(0, 0, 0, .08);
    }
    .dark .permisos-card.abierta > .fi-section > .fi-section-header {
        border-bottom-color: rgba(255, 255, 255, .10);
    }
    .permisos-card.abierta > .fi-section > .fi-section-content-ctn {
        display: block;
        overflow-y: auto;
        padding-bottom: .5rem;
    }

    /* Sub-cards de submódulos dentro del modal (plegables) */
    .permisos-subcard .fi-section-header { cursor: pointer; }

    .permisos-modal-cerrar {
        display: none;
        position: fixed; top: 1rem; right: 1.25rem; z-index: 70;
        padding: .5rem 1rem;
        border-radius: 9999px;
        background: #fff; color: #111;
        font-size: .875rem; font-weight: 600;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .25);
        cursor: pointer;
    }
    body.permisos-modal-activa .permisos-modal-cerrar { display: block; }
    body.permisos-modal-activa { overflow: hidden; }
</style>

<script>
    (function () {
        if (window.__permisosCardsInit) return;
        window.__permisosCardsInit = true;

        window.cerrarCardPermisos = function () {
            document.querySelectorAll('.permisos-card.abierta')
                .forEach((card) => card.classList.remove('abierta'));
            document.body.classList.remove('permisos-modal-activa');
        };

        document.addEventListener('click', function (e) {
            const abierta = document.querySelector('.permisos-card.abierta');

            if (abierta) {
                // Clic directo sobre el backdrop (la sección misma) cierra el modal
                if (e.target === abierta) window.cerrarCardPermisos();
                return;
            }

            const card = e.target.closest('.permisos-card');
            if (card) {
                card.classList.add('abierta');
                document.body.classList.add('permisos-modal-activa');
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') window.cerrarCardPermisos();
        });
    })();
</script>
