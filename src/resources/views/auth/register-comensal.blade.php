<!DOCTYPE html>
<html class="light" lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Regístrate - GastroGuía</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@400;500;600&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
            "colors": {
                    "secondary": "#944a00",
                    "inverse-surface": "#33302c",
                    "inverse-on-surface": "#f6f0ea",
                    "error-container": "#ffdad6",
                    "on-primary-container": "#ffe5e1",
                    "primary-fixed-dim": "#ffb4a9",
                    "on-surface-variant": "#59413d",
                    "on-tertiary-fixed": "#351000",
                    "on-secondary-fixed-variant": "#713700",
                    "on-primary": "#ffffff",
                    "tertiary-container": "#b54700",
                    "secondary-fixed-dim": "#ffb783",
                    "on-tertiary-container": "#ffe6dc",
                    "background": "#fff8f2",
                    "surface-variant": "#e8e1dc",
                    "on-secondary-fixed": "#301400",
                    "on-tertiary": "#ffffff",
                    "error": "#ba1a1a",
                    "on-surface": "#1e1b18",
                    "tertiary-fixed": "#ffdbcd",
                    "surface-bright": "#fff8f2",
                    "primary": "#9e2016",
                    "on-secondary": "#ffffff",
                    "surface-dim": "#dfd9d3",
                    "surface-tint": "#b02d21",
                    "surface-container-low": "#f9f2ec",
                    "surface-container-highest": "#e8e1dc",
                    "surface-container-lowest": "#ffffff",
                    "on-tertiary-fixed-variant": "#7c2e00",
                    "surface": "#fff8f2",
                    "tertiary": "#8e3600",
                    "primary-container": "#c0392b",
                    "surface-container-high": "#eee7e1",
                    "outline-variant": "#e1bfb9",
                    "on-primary-fixed": "#410000",
                    "inverse-primary": "#ffb4a9",
                    "on-error-container": "#93000a",
                    "secondary-container": "#fc8f34",
                    "outline": "#8d706c",
                    "on-secondary-container": "#663100",
                    "primary-fixed": "#ffdad5",
                    "on-background": "#1e1b18",
                    "surface-container": "#f3ede7",
                    "secondary-fixed": "#ffdcc5",
                    "on-primary-fixed-variant": "#8e130c",
                    "on-error": "#ffffff",
                    "tertiary-fixed-dim": "#ffb595"
            },
            "fontFamily": {
                    "headline": ["Plus Jakarta Sans"],
                    "body": ["Inter"],
                    "label": ["Inter"]
            }
          },
        },
    }
</script>
<style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    h1, h2, h3 { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col">
<main class="flex-grow flex items-center justify-center lg:p-6">
    <div class="w-full max-w-7xl h-full lg:h-[921px] bg-surface-container-lowest lg:rounded-xl overflow-hidden flex flex-col md:flex-row shadow-[0_20px_50px_rgba(158,32,22,0.05)]">
        
        <!-- Image Pane -->
        <section class="hidden md:block md:w-1/2 lg:w-3/5 relative overflow-hidden bg-surface-container-highest">
            <div class="absolute inset-0 z-10 bg-gradient-to-t from-primary/40 via-transparent to-transparent"></div>
            <img class="w-full h-full object-cover object-center transform transition-transform duration-[2000ms]" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAXE_fHiBBXKqL_pFqskmuGW5N4a9xJD_JsWjwQB0tSwHCtH_l7d50WcoxC3kieN976pjhKybkIBllX2rS1fA77rA4-JDix2HinU15Hs4e1EHIqX9KZVSGKweghalFPP38f77bYIKDUq9NVlw6eUE8mEtWOgYGUT6XFgrRM2kdvqwFXFdqJwN52qnj9sfxoZlIvPSLDfWxnCXKQUDni6biEqOeOM3aP0vfLK7wrOIW150f41crqJuBSVnk0hlY13N1RD3Rc-0eV7WpO" />
            <div class="absolute bottom-12 left-12 z-20 max-w-md">
                <div class="bg-surface/80 backdrop-blur-md p-6 rounded-xl border border-white/20">
                    <span class="text-primary font-headline text-xs font-bold tracking-widest uppercase mb-2 block">Cultura Gastronómica</span>
                    <h2 class="text-3xl font-extrabold text-on-surface leading-tight">Explora el sabor auténtico de La Paz.</h2>
                    <p class="text-on-surface-variant mt-3 leading-relaxed">Únete a la comunidad de paladares más exigentes de Bolivia y descubre tesoros culinarios ocultos.</p>
                </div>
            </div>
        </section>

        <!-- Register Pane -->
        <section class="w-full md:w-1/2 lg:w-2/5 flex flex-col p-8 md:p-12 lg:p-16 justify-center">
            <div class="mb-10 flex flex-col items-start">
                <span class="text-primary font-headline text-2xl font-extrabold tracking-tighter mb-1">GastroGuía</span>
                <h1 class="text-4xl font-extrabold text-on-surface tracking-tight mb-2">Bienvenido</h1>
                <p class="text-stone-500 font-medium">Crea tu cuenta de comensal para comenzar tu viaje.</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.comensal.post') }}" method="POST" class="space-y-6">
                @csrf
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-on-surface-variant ml-1" for="name">Nombre Completo</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-xl">person</span>
                        <input class="w-full pl-12 pr-4 py-3.5 bg-surface-container-low border-0 rounded-lg outline-none text-on-surface" id="name" name="nombre" value="{{ old('nombre') }}" placeholder="Ej. Juan Pérez" type="text" required/>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-on-surface-variant ml-1" for="email">Correo Electrónico</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-xl">mail</span>
                        <input class="w-full pl-12 pr-4 py-3.5 bg-surface-container-low border-0 rounded-lg outline-none text-on-surface" id="email" name="email" value="{{ old('email') }}" placeholder="nombre@ejemplo.com" type="email" required/>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-on-surface-variant ml-1" for="phone">Teléfono (opcional)</label>
                    <div class="flex gap-2">
                        <div class="relative w-24 shrink-0">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-on-surface font-semibold text-sm">+591</span>
                            <div class="w-full h-full border-0 bg-surface-container-low rounded-lg py-3.5"></div>
                        </div>
                        <input class="flex-grow px-4 py-3.5 bg-surface-container-low border-0 rounded-lg outline-none text-on-surface" id="phone" name="telefono" value="{{ old('telefono') }}" placeholder="70000000" type="tel"/>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-on-surface-variant ml-1" for="password">Contraseña</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-xl">lock</span>
                        <input class="w-full pl-12 pr-4 py-3.5 bg-surface-container-low border-0 rounded-lg outline-none text-on-surface" id="password" name="password" placeholder="••••••••" type="password" required minlength="6"/>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 py-2">
                    <input class="w-5 h-5 rounded border-stone-300 text-primary focus:ring-primary" id="terms" type="checkbox" required/>
                    <label class="text-xs text-on-surface-variant leading-tight" for="terms">
                        Acepto los Términos de Servicio y la Política de Privacidad.
                    </label>
                </div>

                <button class="w-full bg-primary-container text-on-primary py-4 rounded-lg font-bold text-base shadow hover:scale-[1.02] active:scale-[0.98] transition-all" type="submit">
                    Registrarme ahora
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-sm text-stone-500">
                    ¿Ya tienes una cuenta? 
                    <a class="text-primary font-bold hover:underline ml-1" href="{{ route('login') }}">Inicia sesión aquí</a>
                </p>
            </div>
        </section>
    </div>
</main>
</body>
</html>
