<!DOCTYPE html>
<html class="light" lang="es">
<head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>GastroGuía | Acceso</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "secondary-fixed": "#ffdcc5",
              "on-error-container": "#93000a",
              "on-tertiary-fixed": "#351000",
              "tertiary-fixed": "#ffdbcd",
              "on-surface": "#1e1b18",
              "surface-bright": "#fff8f2",
              "primary-fixed": "#ffdad5",
              "primary": "#9e2016",
              "on-secondary-fixed-variant": "#713700",
              "inverse-surface": "#33302c",
              "on-primary-fixed": "#410000",
              "secondary-container": "#fc8f34",
              "tertiary-fixed-dim": "#ffb595",
              "surface-container-high": "#eee7e1",
              "tertiary": "#8e3600",
              "secondary-fixed-dim": "#ffb783",
              "primary-container": "#c0392b",
              "on-primary-container": "#ffe5e1",
              "background": "#fff8f2",
              "on-error": "#ffffff",
              "surface-dim": "#dfd9d3",
              "on-tertiary-container": "#ffe6dc",
              "on-secondary": "#ffffff",
              "on-background": "#1e1b18",
              "surface-tint": "#b02d21",
              "on-tertiary": "#ffffff",
              "inverse-on-surface": "#f6f0ea",
              "surface-container-highest": "#e8e1dc",
              "surface-container-lowest": "#ffffff",
              "error-container": "#ffdad6",
              "surface-variant": "#e8e1dc",
              "on-secondary-fixed": "#301400",
              "on-secondary-container": "#663100",
              "surface": "#fff8f2",
              "error": "#ba1a1a",
              "outline-variant": "#e1bfb9",
              "outline": "#8d706c",
              "secondary": "#944a00",
              "surface-container-low": "#f9f2ec",
              "on-tertiary-fixed-variant": "#7c2e00",
              "on-surface-variant": "#59413d",
              "inverse-primary": "#ffb4a9",
              "primary-fixed-dim": "#ffb4a9",
              "on-primary": "#ffffff",
              "tertiary-container": "#b54700",
              "surface-container": "#f3ede7",
              "on-primary-fixed-variant": "#8e130c"
            },
            fontFamily: {
              "headline": ["Plus Jakarta Sans"],
              "body": ["Inter"],
              "label": ["Inter"]
            },
          },
        },
      }
</script>
<style>
    .material-symbols-outlined { font-variation-settings: "FILL" 0, "wght" 400, "GRAD" 0, "opsz" 24; }
    .auth-split-bg {
        background-image: linear-gradient(rgba(158, 32, 22, 0.4), rgba(148, 74, 0, 0.4)), url('https://lh3.googleusercontent.com/aida-public/AB6AXuCeoN5FKuav4rWHt-LnAJTrZ6Cvk0PE7PPjqMMf98WBWJIP6T9QXwxVGSeDt86HYlzuWfAERo2do9Qa4K6NWifMf9XoSDoDZr4uz26fk-8hDuQs-Zibzq-MGwr0MpY5bj43wjDUxa9NkAo12e4RI-u_Gxr3GPVR63FuqI-yB0BUkA44LqVduHK4YHguOLlD9GF6oicwRmpm7qbjsil7Zh-Wmdae46IbRIciZtm5gzTSLeyTZkkWNNCSmZFnIz_WGXEjOAsIbyg4HCT-');
        background-size: cover;
        background-position: center;
    }
</style>
</head>
<body class="bg-background text-on-background font-body min-h-screen flex flex-col">
<main class="flex-grow flex flex-col md:flex-row min-h-screen">
    <!-- LEFT SIDE: Auth Form -->
    <div class="w-full md:w-1/2 flex items-center justify-center p-8 lg:p-16 bg-surface-bright">
        <div class="max-w-md w-full space-y-10">
            <!-- Branding -->
            <div class="space-y-4">
                <h1 class="text-3xl font-black text-primary tracking-tighter font-headline">GastroGuía</h1>
                <div class="space-y-1">
                    <h2 class="text-3xl font-bold font-headline text-on-surface tracking-tight">Bienvenido de nuevo</h2>
                    <p class="text-on-surface-variant font-medium">Explora los mejores sabores de tu ciudad.</p>
                </div>
            </div>

            <!-- Role Toggle -->
            <div class="bg-surface-container-high p-1 rounded-xl flex gap-1 mb-6">
                <button type="button" onclick="setRole('comensal')" id="btn-comensal" class="flex-1 py-2 px-4 rounded-lg bg-surface-container-lowest text-primary font-bold shadow-sm flex items-center justify-center gap-2 transition-all">
                    <span class="material-symbols-outlined text-xl" data-icon="person">person</span>
                    <span class="font-label text-sm uppercase tracking-wider">Comensal</span>
                </button>
                <button type="button" onclick="setRole('usuario')" id="btn-usuario" class="flex-1 py-2 px-4 rounded-lg text-on-surface-variant font-semibold hover:bg-surface-container-highest flex items-center justify-center gap-2 transition-all">
                    <span class="material-symbols-outlined text-xl" data-icon="restaurant">restaurant</span>
                    <span class="font-label text-sm uppercase tracking-wider">Restaurante</span>
                </button>
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

            <!-- Form Section -->
            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="login_type" id="login_type" value="comensal">
                <div class="space-y-4">
                    <div class="group">
                        <label class="block text-sm font-semibold text-on-surface-variant mb-1 ml-1 font-label uppercase tracking-widest" for="email">Correo Electrónico</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline" data-icon="mail">mail</span>
                            <input class="w-full pl-10 pr-4 py-3 bg-surface-container-lowest border-0 rounded-lg focus:ring-2 focus:ring-primary-container transition-all placeholder:text-outline-variant text-on-surface" id="email" name="email" value="{{ old('email') }}" placeholder="nombre@ejemplo.com" type="email" required>
                        </div>
                    </div>
                    <div class="group">
                        <label class="block text-sm font-semibold text-on-surface-variant mb-1 ml-1 font-label uppercase tracking-widest" for="password">Contraseña</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline" data-icon="lock">lock</span>
                            <input class="w-full pl-10 pr-12 py-3 bg-surface-container-lowest border-0 rounded-lg focus:ring-2 focus:ring-primary-container transition-all placeholder:text-outline-variant text-on-surface" id="password" name="password" placeholder="••••••••" type="password" required>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input class="rounded border-outline-variant text-primary focus:ring-primary-container h-4 w-4" type="checkbox" name="remember">
                        <span class="text-sm font-medium text-on-surface-variant group-hover:text-on-surface transition-colors">Recordarme</span>
                    </label>
                </div>

                <!-- Primary CTA -->
                <button class="w-full py-4 bg-gradient-to-r from-primary to-primary-container text-white font-bold rounded-lg shadow-lg hover:shadow-xl hover:scale-[1.01] active:scale-[0.98] transition-all flex items-center justify-center gap-2" type="submit">
                    Iniciar Sesión
                    <span class="material-symbols-outlined" data-icon="arrow_forward">arrow_forward</span>
                </button>
            </form>
            
            <div class="mt-6 text-center" id="register-link-container">
                <p class="text-sm text-stone-500">
                    ¿No tienes una cuenta? 
                    <a class="text-primary font-bold hover:underline ml-1" href="{{ route('register.comensal') }}">Regístrate gratis</a>
                </p>
            </div>
        </div>
    </div>
    
    <!-- RIGHT SIDE: High-End Visuals -->
    <div class="hidden md:block md:w-1/2 relative overflow-hidden bg-stone-900">
        <div class="absolute inset-0 auth-split-bg opacity-80"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-primary/80 via-transparent to-transparent flex flex-col justify-end p-16 space-y-6">
            <div class="bg-surface-container-lowest/10 backdrop-blur-md p-8 rounded-2xl border border-white/20 max-w-lg">
                <div class="flex gap-1 mb-4">
                    <span class="material-symbols-outlined text-secondary-fixed" data-icon="star" style="font-variation-settings: 'FILL' 1;">star</span>
                    <span class="material-symbols-outlined text-secondary-fixed" data-icon="star" style="font-variation-settings: 'FILL' 1;">star</span>
                    <span class="material-symbols-outlined text-secondary-fixed" data-icon="star" style="font-variation-settings: 'FILL' 1;">star</span>
                    <span class="material-symbols-outlined text-secondary-fixed" data-icon="star" style="font-variation-settings: 'FILL' 1;">star</span>
                    <span class="material-symbols-outlined text-secondary-fixed" data-icon="star" style="font-variation-settings: 'FILL' 1;">star</span>
                </div>
                <blockquote class="text-white text-2xl font-headline font-bold leading-tight italic">
                    "La mejor plataforma de gestión en Bolivia. Creció mis ventas un 200% el primer mes."
                </blockquote>
            </div>
        </div>
    </div>
</main>
<script>
    function setRole(role) {
        document.getElementById('login_type').value = role;
        const btnComensal = document.getElementById('btn-comensal');
        const btnUsuario = document.getElementById('btn-usuario');
        const registerDiv = document.getElementById('register-link-container');
        
        if (role === 'comensal') {
            btnComensal.className = "flex-1 py-2 px-4 rounded-lg bg-surface-container-lowest text-primary font-bold shadow-sm flex items-center justify-center gap-2 transition-all";
            btnUsuario.className = "flex-1 py-2 px-4 rounded-lg text-on-surface-variant font-semibold hover:bg-surface-container-highest flex items-center justify-center gap-2 transition-all";
            if (registerDiv) registerDiv.style.display = 'block';
        } else {
            btnUsuario.className = "flex-1 py-2 px-4 rounded-lg bg-surface-container-lowest text-primary font-bold shadow-sm flex items-center justify-center gap-2 transition-all";
            btnComensal.className = "flex-1 py-2 px-4 rounded-lg text-on-surface-variant font-semibold hover:bg-surface-container-highest flex items-center justify-center gap-2 transition-all";
            if (registerDiv) registerDiv.style.display = 'none';
        }
    }
</script>
</body>
</html>
