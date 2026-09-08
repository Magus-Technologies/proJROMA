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
    /* Barra de marcar / desmarcar */
    .permisos-barra-toggle {
        display: flex; align-items: center; gap: .5rem; flex-wrap: wrap;
        padding: .5rem .25rem;
    }
    .permisos-barra-ambito {
        font-size: .75rem; color: #6b7280; margin-right: .25rem;
    }
    .dark .permisos-barra-ambito { color: #9ca3af; }

    .permisos-btn {
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .3rem .7rem;
        border-radius: .5rem;
        border: 1px solid transparent;
        font-size: .75rem; font-weight: 600;
        cursor: pointer;
        transition: background-color .12s ease, border-color .12s ease;
    }
    .permisos-btn-marcar {
        color: #047857; background: #ecfdf5; border-color: #a7f3d0;
    }
    .permisos-btn-marcar:hover { background: #d1fae5; }
    .permisos-btn-desmarcar {
        color: #b91c1c; background: #fef2f2; border-color: #fecaca;
    }
    .permisos-btn-desmarcar:hover { background: #fee2e2; }

    .dark .permisos-btn-marcar {
        color: #6ee7b7; background: rgba(6, 78, 59, .35); border-color: rgba(16, 185, 129, .35);
    }
    .dark .permisos-btn-desmarcar {
        color: #fca5a5; background: rgba(127, 29, 29, .35); border-color: rgba(239, 68, 68, .35);
    }

    body.permisos-modal-activa .permisos-modal-cerrar { display: block; }
    body.permisos-modal-activa { overflow: hidden; }
</style>

<script>
    (function () {
        if (window.__permisosCardsInit) return;
        window.__permisosCardsInit = true;

        /**
         * Marca o desmarca los checkboxes del contenedor donde vive el botón.
         *
         * Se hace en el navegador a propósito. Una acción de Filament haría una
         * petición a Livewire, el schema se re-renderizaría y la card perdería
         * la clase .abierta, cerrando el modal a media edición. Emitiendo el
         * evento change, wire:model se entera igual sin re-render.
         */
        window.marcarPermisos = function (boton, valor) {
            // El ámbito es el grupo si el botón está dentro de uno; si no, la card entera.
            const ambito = boton.closest('[data-grupo-permisos]')
                ?? boton.closest('[data-card-modulo]');
            if (! ambito) return;

            let cambiados = 0;

            ambito.querySelectorAll('input[type="checkbox"]').forEach((cb) => {
                // Solo permisos visibles: respeta el filtro del buscador.
                const opcion = cb.closest('.fi-fo-checkbox-list-option-ctn');
                if (opcion && opcion.style.display === 'none') return;
                if (cb.disabled || cb.checked === valor) return;

                cb.checked = valor;
                cb.dispatchEvent(new Event('input', { bubbles: true }));
                cb.dispatchEvent(new Event('change', { bubbles: true }));
                cambiados++;
            });

            // Feedback: sin esto no se nota que pasó algo si todo ya estaba igual.
            const original = boton.textContent;
            boton.textContent = cambiados ? `${cambiados} cambiados` : 'Ya estaban así';
            setTimeout(() => { boton.textContent = original; }, 1100);
        };

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

            // Un clic en los botones de marcar/desmarcar no debe abrir ni cerrar nada.
            if (e.target.closest('.permisos-barra-toggle')) return;

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
