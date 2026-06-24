<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ProjRoma — Iniciar Sesión</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Plus Jakarta Sans','sans-serif']}}}}</script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { background: linear-gradient(135deg,#0a1628 0%,#0f1f3d 45%,#1e3a8a 75%,#1d4ed8 100%); }
        .glass { background:rgba(255,255,255,.06); backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,.12); }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
        .floating { animation:float 6s ease-in-out infinite }
        @keyframes fadeUp { from{opacity:0;transform:translateY(18px)} to{opacity:1;transform:translateY(0)} }
        .fu { animation:fadeUp .55s ease-out forwards }
        .fu1 { animation-delay:.08s;opacity:0 }
        .fu2 { animation-delay:.18s;opacity:0 }
        .fu3 { animation-delay:.28s;opacity:0 }
        @keyframes spin { to{transform:rotate(360deg)} }
        .spin { animation:spin 1s linear infinite }
        .orb { position:absolute;border-radius:50%;pointer-events:none;filter:blur(80px) }
    </style>
</head>

<body class="min-h-screen font-sans" x-data="loginApp()">

    {{-- Orbs decorativos --}}
    <div class="orb" style="width:500px;height:500px;background:rgba(59,130,246,.18);top:-180px;right:-120px"></div>
    <div class="orb" style="width:320px;height:320px;background:rgba(29,78,216,.22);bottom:-80px;left:-80px"></div>

    <div class="relative min-h-screen flex">

        {{-- Panel izquierdo (desktop) --}}
        <div class="hidden lg:flex lg:w-1/2 flex-col items-center justify-center px-16">
            <div class="floating mb-10">
                <div class="flex h-28 w-28 items-center justify-center rounded-3xl bg-white/10 shadow-2xl ring-1 ring-white/20">
                    <i class="ti ti-ship text-6xl text-white"></i>
                </div>
            </div>
            <h1 class="text-5xl font-extrabold text-white text-center leading-tight mb-3">
                Titanic<span class="text-blue-300">SAC</span>
            </h1>
            <p class="text-blue-200 text-sm text-center max-w-xs mb-10 leading-relaxed">
                Sistema integrado de ventas, facturación electrónica SUNAT y gestión de almacén.
            </p>
            <div class="space-y-2.5 w-full max-w-xs">
                @foreach([
                    ['ti-receipt-2','Facturación electrónica SUNAT'],
                    ['ti-chart-bar', 'Dashboard con KPIs en tiempo real'],
                    ['ti-users',     'Multi-empresa · Multi-sucursal'],
                    ['ti-shield-lock','Roles y acceso por perfiles'],
                ] as [$ico,$txt])
                <div class="glass flex items-center gap-3 rounded-xl px-4 py-2.5">
                    <i class="{{ $ico }} text-blue-300 text-sm"></i>
                    <span class="text-xs text-blue-100">{{ $txt }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Panel derecho - Formulario --}}
        <div class="flex flex-1 items-center justify-center p-6 lg:p-12">
            <div class="w-full max-w-md">

                {{-- Logo móvil --}}
                <div class="flex justify-center mb-8 lg:hidden">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10">
                        <i class="ti ti-ship text-3xl text-white"></i>
                    </div>
                </div>

                <div class="glass rounded-2xl p-8 shadow-2xl fu fu1">
                    <h2 class="text-2xl font-bold text-white mb-0.5">Bienvenido</h2>
                    <p class="text-blue-300 text-xs mb-7">Ingresa tus credenciales para acceder al sistema</p>

                    {{-- Error --}}
                    <div x-show="error" x-cloak
                         class="mb-5 flex items-start gap-2 rounded-xl border border-red-500/30 bg-red-500/15 px-4 py-3 text-xs text-red-200">
                        <i class="ti ti-alert-circle text-red-300 mt-0.5 shrink-0"></i>
                        <span x-text="error"></span>
                    </div>

                    <form @submit.prevent="submit" class="space-y-4">

                        {{-- Usuario --}}
                        <div class="fu fu1">
                            <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-widest text-blue-300">Usuario o Email</label>
                            <div class="relative">
                                <i class="ti ti-user absolute left-3.5 top-1/2 -translate-y-1/2 text-blue-400 text-sm"></i>
                                <input x-model="form.user" type="text" autocomplete="username" placeholder="tu@usuario.com"
                                       class="w-full rounded-xl border border-white/20 bg-white/10 py-3 pl-10 pr-4 text-sm text-white placeholder-blue-300/40
                                              focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                            </div>
                        </div>

                        {{-- Contraseña --}}
                        <div class="fu fu2">
                            <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-widest text-blue-300">Contraseña</label>
                            <div class="relative">
                                <i class="ti ti-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-blue-400 text-sm"></i>
                                <input x-model="form.clave" :type="showPass?'text':'password'" autocomplete="current-password" placeholder="••••••••"
                                       class="w-full rounded-xl border border-white/20 bg-white/10 py-3 pl-10 pr-11 text-sm text-white placeholder-blue-300/40
                                              focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                                <button type="button" @click="showPass=!showPass"
                                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-blue-400 hover:text-white transition-colors">
                                    <i :class="showPass?'ti-eye-off':'ti-eye'" class="ti text-sm"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Sucursal --}}
                        <div class="fu fu2">
                            <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-widest text-blue-300">Sucursal</label>
                            <div class="relative">
                                <i class="ti ti-building absolute left-3.5 top-1/2 -translate-y-1/2 text-blue-400 text-sm"></i>
                                <select x-model="form.sucursal"
                                        class="w-full appearance-none rounded-xl border border-white/20 bg-white/10 py-3 pl-10 pr-4 text-sm text-white
                                               focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                                    <option value="1" class="bg-slate-900">Sucursal 1 — Principal</option>
                                    <option value="2" class="bg-slate-900">Sucursal 2</option>
                                    <option value="3" class="bg-slate-900">Sucursal 3</option>
                                </select>
                            </div>
                        </div>

                        {{-- Botón --}}
                        <div class="fu fu3 pt-1">
                            <button type="submit" :disabled="loading"
                                    class="relative w-full overflow-hidden rounded-xl bg-gradient-to-r from-blue-600 to-blue-500
                                           py-3.5 text-sm font-semibold text-white shadow-lg shadow-blue-900/40
                                           hover:from-blue-500 hover:to-blue-400 disabled:opacity-60 disabled:cursor-not-allowed
                                           focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2
                                           focus:ring-offset-transparent transition-all">
                                <span x-show="!loading">Ingresar al sistema</span>
                                <span x-show="loading" x-cloak class="flex items-center justify-center gap-2">
                                    <i class="ti ti-loader-2 text-lg spin"></i> Verificando...
                                </span>
                            </button>
                        </div>

                    </form>
                </div>

                <p class="mt-5 text-center text-[10px] text-blue-400/50">
                    &copy; {{ date('Y') }} ProjRoma · projroma.com · Laravel 13 · PHP 8.3
                </p>
            </div>
        </div>
    </div>

    <script>
        function loginApp() {
            return {
                form: { user:'', clave:'', sucursal:'1' },
                loading: false, error: '', showPass: false,

async submit() {
    this.error = '';
    if (!this.form.user || !this.form.clave) {
        this.error = 'Completa usuario y contraseña.';
        return;
    }

    this.loading = true;
    try {
        // Obtener CSRF token fresco del meta tag
        const token = document.querySelector('meta[name="csrf-token"]')?.content;

	const res = await fetch('{{ config("app.url") }}/login', {
	method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            credentials: 'same-origin',  // ← incluir cookies
            body: JSON.stringify(this.form),
        });

        const data = await res.json();
        if (data.res) {
            window.location.href = data.ruta;
        } else {
            this.error = data.msg || 'Credenciales inválidas.';
            this.form.clave = '';
        }
    } catch(e) {
        this.error = 'Error de conexión.';
    } finally {
        this.loading = false;
    }
}

            }
        }
    </script>
</body>
</html>
