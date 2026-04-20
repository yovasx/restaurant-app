<!DOCTYPE html><html lang="es"><head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>Registro de Restaurante - GastroGuía</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;family=Inter:wght@300;400;500;600&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
      body { font-family: 'Inter', sans-serif; }
      h1, h2, h3 { font-family: 'Plus Jakarta Sans', sans-serif; }
      .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
      .glass-effect { background: rgba(255, 248, 242, 0.8); backdrop-filter: blur(12px); }
</style>
</head>
<body class="bg-[#fff8f2] text-[#1e1b18]">
<header class="fixed top-0 w-full z-50 bg-[#FFF8F2] shadow-sm">
    <div class="flex justify-between items-center px-6 py-4 max-w-7xl mx-auto">
        <a href="{{ route('login') }}" class="text-2xl font-black text-[#9e2016] tracking-tight">GastroGuía</a>
        <nav class="hidden md:flex gap-8 items-center">
            <a class="bg-[#9e2016] text-white px-6 py-2 rounded-lg font-bold hover:scale-105 transition-all" href="{{ route('login') }}">Ya tengo cuenta</a>
        </nav>
    </div>
</header>
<main class="min-h-screen pt-24 pb-20 flex flex-col lg:flex-row gap-0">
    <section class="hidden lg:flex lg:w-1/3 fixed left-0 top-0 h-full z-0 overflow-hidden">
        <div class="relative w-full h-full">
            <img class="absolute inset-0 w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDeLrZ3lpInv_m75e13k9wYOG9exXo8b8PsHvZK7_w63skbuN1LCEk8Gu67kgiY5x1avu_cycxz17rsolLhYMcMjxO21tHtXfOXw2xaV-vIDpFAR-pXHC2VZmzBXmVKaE03GKWjYUyybM6klBNiZ8CMiic2GCoFgGb0p_cH3--Yd0rQoOerc41QXvSd5ijPFaVoVr8QKkUi3A8wpVLBshp8UGtcP4uFfX8Wlse9vEceG35Wf8EOW2YzffosXOcQE-ArLgJzIRTJTiBo">
            <div class="absolute inset-0 bg-gradient-to-t from-red-900/80 to-transparent"></div>
            <div class="absolute bottom-12 left-10 right-10 text-white">
                <span class="text-xs font-bold tracking-widest uppercase mb-2 block opacity-80">Únete a la Red Gourmet</span>
                <h2 class="text-4xl font-extrabold leading-tight">Posiciona tu restaurante ante los paladares más exigentes.</h2>
            </div>
        </div>
    </section>

    <section class="w-full lg:w-2/3 lg:ml-[33.333333%] px-6 md:px-12 py-8 max-w-4xl">
        <div class="mb-12">
            <h1 class="text-4xl md:text-5xl font-extrabold text-[#1e1b18] mb-4 tracking-tight">Registro GastroGuía</h1>
            <p class="text-[#59413d] text-lg max-w-2xl">Completa los detalles de tu establecimiento para comenzar a recibir reservas y destacar en nuestra comunidad culinaria.</p>
        </div>

        @if($errors->any())
        <div class="bg-red-100 text-red-800 p-4 rounded-xl font-bold mb-8">
            <p>Comprueba los campos ingresados e intenta nuevamente:</p>
            <ul class="list-disc pl-5 mt-2 text-sm text-red-700 font-normal">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('register.restaurante.post') }}" method="POST" enctype="multipart/form-data" class="space-y-12">
            @csrf
            
            <div class="bg-white p-8 rounded-xl shadow-sm border border-stone-200">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 rounded-full bg-[#c0392b] flex items-center justify-center text-white">
                        <span class="material-symbols-outlined">restaurant</span>
                    </div>
                    <h2 class="text-2xl font-bold">Datos Generales</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Nombre del local</label>
                        <input name="nombre" value="{{ old('nombre') }}" class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all" placeholder="Ej: Gustu" type="text" required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">NIT (Opcional)</label>
                        <input name="nit" value="{{ old('nit') }}" class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all" placeholder="Número de Identidad" type="text">
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Descripción del Restaurante</label>
                        <textarea name="descripcion" class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all min-h-[80px]" placeholder="Cuéntanos un poco sobre tu especialidad, ambiente y lo que te hace único...">{{ old('descripcion') }}</textarea>
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Correo Electrónico (Tu cuenta)</label>
                        <input name="email" value="{{ old('email') }}" class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all" placeholder="admin@local.com" type="email" required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Teléfono Principal</label>
                        <input name="telefono" value="{{ old('telefono') }}" class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all" placeholder="70000000" type="tel" required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Contraseña</label>
                        <input name="password" class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all" type="password" required minlength="6">
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Foto de Portada</label>
                        <input name="foto_portada" class="w-full bg-[#f3ede7] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all" type="file" accept="image/*">
                    </div>
                </div>
            </div>

            <!-- Section 2: Ubicación con Mapa -->
            <div class="bg-[#f9f2ec] p-8 rounded-xl border border-stone-200">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 rounded-full bg-[#944a00] flex items-center justify-center text-white">
                        <span class="material-symbols-outlined">location_on</span>
                    </div>
                    <h2 class="text-2xl font-bold">Ubicación Precisa</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Zona</label>
                        <select name="zona" class="w-full bg-[#e8e1dc] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all">
                            <option value="Sopocachi">Sopocachi</option>
                            <option value="Zona Sur">Zona Sur</option>
                            <option value="Miraflores">Miraflores</option>
                            <option value="Centro">Centro</option>
                            <option value="Achumani">Achumani</option>
                            <option value="Otra">Otra</option>
                        </select>
                    </div>
                    <div class="md:col-span-2 space-y-2">
                        <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Calle y Número</label>
                        <input name="direccion" value="{{ old('direccion') }}" class="w-full bg-[#e8e1dc] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all" placeholder="Ej: Calle Belisario Salinas #123" type="text" required>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider flex justify-between">
                        Fija tu ubicación en el mapa
                        <span class="text-[10px] text-stone-500 normal-case">(Haz clic en el mapa)</span>
                    </label>
                    <div id="map" class="relative w-full h-64 rounded-xl overflow-hidden shadow-inner group z-10 border-2 border-stone-300"></div>
                    <input type="hidden" name="latitud" id="latitud" value="-16.500000">
                    <input type="hidden" name="longitud" id="longitud" value="-68.119293">
                </div>
            </div>

            <!-- Section 3: Horarios -->
            <div class="bg-white p-8 rounded-xl shadow-sm border border-stone-200">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 rounded-full bg-[#b54700] flex items-center justify-center text-white">
                        <span class="material-symbols-outlined">schedule</span>
                    </div>
                    <h2 class="text-2xl font-bold">Horarios de Atención</h2>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-4 md:grid-cols-6 items-center gap-4 pb-4 border-b border-stone-100">
                        <div class="col-span-2 md:col-span-2 font-bold text-[#1e1b18]">Lunes a Viernes</div>
                        <div class="col-span-1 md:col-span-2 flex items-center gap-2">
                            <input name="hora_apertura_lunes_viernes" class="w-full bg-[#f3ede7] border-0 rounded-lg py-2 px-2 text-sm" type="time" value="09:00">
                        </div>
                        <div class="col-span-1 md:col-span-2 flex items-center gap-2">
                            <input name="hora_cierre_lunes_viernes" class="w-full bg-[#f3ede7] border-0 rounded-lg py-2 px-2 text-sm" type="time" value="22:00">
                        </div>
                    </div>
                    <div class="grid grid-cols-4 md:grid-cols-6 items-center gap-4 pb-4 border-b border-stone-100">
                        <div class="col-span-2 md:col-span-2 font-bold text-[#1e1b18]">Sábados</div>
                        <div class="col-span-1 md:col-span-2 flex items-center gap-2">
                            <input name="hora_apertura_sabado" class="w-full bg-[#f3ede7] border-0 rounded-lg py-2 px-2 text-sm" type="time" value="10:00">
                        </div>
                        <div class="col-span-1 md:col-span-2 flex items-center gap-2">
                            <input name="hora_cierre_sabado" class="w-full bg-[#f3ede7] border-0 rounded-lg py-2 px-2 text-sm" type="time" value="23:30">
                        </div>
                    </div>
                    <div class="grid grid-cols-4 md:grid-cols-6 items-center gap-4 pb-4 border-b border-stone-100">
                        <div class="col-span-2 md:col-span-2 font-bold text-[#1e1b18]">Domingos</div>
                        <div class="col-span-1 md:col-span-2 flex items-center gap-2">
                            <input name="hora_apertura_domingo" class="w-full bg-[#f3ede7] border-0 rounded-lg py-2 px-2 text-sm" type="time" value="11:00">
                        </div>
                        <div class="col-span-1 md:col-span-2 flex items-center gap-2">
                            <input name="hora_cierre_domingo" class="w-full bg-[#f3ede7] border-0 rounded-lg py-2 px-2 text-sm" type="time" value="16:00">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Contacto Digital -->
            <div class="bg-[#f9f2ec] p-8 rounded-xl border border-stone-200">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 rounded-full bg-[#9e2016] flex items-center justify-center text-white">
                        <span class="material-symbols-outlined">language</span>
                    </div>
                    <h2 class="text-2xl font-bold">Contacto Digital</h2>
                </div>
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Email de Reservas</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-stone-400">mail</span>
                            <input name="email_reservas" class="w-full bg-[#e8e1dc] border-0 rounded-lg py-3 pl-12 pr-4 focus:ring-2 focus:ring-[#9e2016] transition-all" placeholder="reservas@tu-restaurante.com" type="email">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Instagram</label>
                            <input name="instagram" class="w-full bg-[#e8e1dc] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all" placeholder="@usuario" type="text">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-[#59413d] uppercase tracking-wider">Facebook / Web</label>
                            <input name="facebook_url" class="w-full bg-[#e8e1dc] border-0 rounded-lg py-3 px-4 focus:ring-2 focus:ring-[#9e2016] transition-all" placeholder="https://..." type="text">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-8 flex flex-col md:flex-row items-center justify-between gap-6">
                <button type="submit" class="w-full md:w-auto bg-[#9e2016] text-white px-10 py-5 rounded-xl font-bold text-lg shadow-lg hover:scale-105 active:scale-95 transition-all duration-300">
                    Finalizar Registro
                </button>
            </div>
        </form>
    </section>
</main>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Init Map
        var map = L.map('map').setView([-16.500000, -68.119293], 13); // La Paz
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        var marker = L.marker([-16.500000, -68.119293]).addTo(map);

        map.on('click', function(e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;
            
            if (marker) { map.removeLayer(marker); }
            marker = L.marker([lat, lng]).addTo(map);
            
            document.getElementById('latitud').value = lat;
            document.getElementById('longitud').value = lng;
        });

        // Fix leaflet sizing issue in hidden containers
        setTimeout(() => { map.invalidateSize(); }, 500);
    });
</script>
</body>
</html>
