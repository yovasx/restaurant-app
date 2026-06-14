@extends('layouts.restaurante')

@section('title', 'Configuración del Restaurante')
@section('page-title', 'Gestión de Perfil')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    <section class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-4xl font-extrabold font-headline text-primary tracking-tight">Gestión de Perfil</h1>
            <p class="text-on-surface-variant font-body mt-2">Configura la identidad pública de tu restaurante en La Paz.</p>
            @if($restaurante)
            <div class="flex items-center gap-2 mt-2">
                <span class="text-sm font-bold text-on-surface">Editando: {{ $restaurante->nombre }}</span>
                @if($restaurante->es_principal)
                <span class="text-[10px] font-bold bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full uppercase tracking-wider">Principal</span>
                @endif
            </div>
            @endif
        </div>
        <a href="{{ route('restaurante.sucursales.index') }}" class="flex items-center gap-2 text-sm text-primary font-bold hover:underline">
            <span class="material-symbols-outlined text-sm">store</span>
            Administrar sucursales
        </a>
    </section>

    <form action="{{ route('restaurante.configuracion.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <!-- Cover Photo Preview -->
        <div class="relative group h-64 rounded-xl overflow-hidden bg-surface-container-highest">
            @if($restaurante && $restaurante->foto_portada_url)
            <img id="portada-preview" alt="Cover Photo" class="w-full h-full object-cover opacity-90 group-hover:scale-105 transition-transform duration-700" src="{{ $restaurante->foto_portada_url }}"/>
            @else
            <img id="portada-preview" alt="Cover Photo" class="w-full h-full object-cover opacity-90 group-hover:scale-105 transition-transform duration-700 hidden" src=""/>
            <div class="w-full h-full flex items-center justify-center flex-col gap-2 text-stone-400">
                <span class="material-symbols-outlined text-5xl">add_photo_alternate</span>
                <p class="text-sm font-semibold">Haz clic para subir foto de portada</p>
            </div>
            @endif
            <div class="absolute inset-0 bg-black/30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer" onclick="document.getElementById('foto_portada').click()">
                <div class="bg-white/90 backdrop-blur-md px-4 py-2 rounded-lg flex items-center gap-2 text-primary font-bold">
                    <span class="material-symbols-outlined">cloud_upload</span>
                    Cambiar Foto de Portada
                </div>
            </div>
            <div class="absolute bottom-4 left-6 flex items-center gap-2 text-white bg-black/40 backdrop-blur-sm px-3 py-1 rounded-full text-sm">
                <span class="material-symbols-outlined text-sm">photo_camera</span>
                <span>Resolución sugerida: 1200 x 400px</span>
            </div>
        </div>
        <input type="file" id="foto_portada" name="foto_portada" class="hidden" accept="image/*" onchange="previewPortada(this)">
        <div class="mt-4">
            <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">Logo del Restaurante (URL)</label>
            <input name="logo_url" class="w-full bg-surface-container-highest border-0 rounded-lg p-4 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium" type="url" value="{{ old('logo_url', $restaurante->logo_url ?? '') }}" placeholder="https://ejemplo.com/logo.png"/>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- General Info -->
                <div class="bg-surface-container-lowest p-8 rounded-xl shadow-sm space-y-6">
                    <h3 class="text-xl font-bold font-headline flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">info</span>
                        Información General
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">Nombre del Restaurante</label>
                            <input name="nombre" class="w-full bg-surface-container-highest border-0 rounded-lg p-4 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium" type="text" value="{{ old('nombre', $restaurante->nombre ?? $usuario->nombre) }}" required/>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">Descripción</label>
                            <textarea name="descripcion" class="w-full bg-surface-container-highest border-0 rounded-lg p-4 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium leading-relaxed" rows="4" placeholder="Cocina de autor, ambiente familiar...">{{ old('descripcion', $restaurante->descripcion ?? '') }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">Teléfono de Contacto</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-500 font-semibold">+591</span>
                                    <input name="telefono" class="w-full bg-surface-container-highest border-0 rounded-lg p-4 pl-16 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium" type="tel" required value="{{ old('telefono', $restaurante->telefono ?? $usuario->telefono) }}"/>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">Email de Reservas</label>
                                <input name="email_reservas" class="w-full bg-surface-container-highest border-0 rounded-lg p-4 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium" type="email" required value="{{ old('email_reservas', $restaurante->email_reservas ?? '') }}" placeholder="reservas@local.com"/>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">Instagram</label>
                                <input name="instagram" class="w-full bg-surface-container-highest border-0 rounded-lg p-4 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium" type="text" value="{{ old('instagram', $restaurante->instagram ?? '') }}" placeholder="@usuario"/>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">Facebook / Web</label>
                                <input name="facebook_url" class="w-full bg-surface-container-highest border-0 rounded-lg p-4 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium" type="text" value="{{ old('facebook_url', $restaurante->facebook_url ?? '') }}" placeholder="facebook.com/..."/>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Password change -->
                <div class="bg-surface-container-lowest p-8 rounded-xl shadow-sm space-y-4">
                    <h3 class="text-xl font-bold font-headline flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">lock</span>
                        Nueva Contraseña
                    </h3>
                    <input name="password" class="w-full bg-surface-container-highest border-0 rounded-lg p-4 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium" type="password" placeholder="Déjalo en blanco para no cambiar"/>
                </div>

                <!-- Business Data -->
                <div class="bg-surface-container-lowest p-8 rounded-xl shadow-sm space-y-4">
                    <h3 class="text-xl font-bold font-headline flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">badge</span>
                        Datos del Negocio
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">NIT</label>
                            <input name="nit" class="w-full bg-surface-container-highest border-0 rounded-lg p-4 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium" type="text" required value="{{ old('nit', optional($perfil)->nit) }}"/>
                        </div>
                    </div>
                </div>

                <!-- Location -->
                <div class="bg-surface-container-lowest p-8 rounded-xl shadow-sm space-y-6">
                    <h3 class="text-xl font-bold font-headline flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">location_on</span>
                        Ubicación Exacta
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">Dirección Física</label>
                            <input name="direccion" class="w-full bg-surface-container-highest border-0 rounded-lg p-4 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium" type="text" value="{{ old('direccion', $restaurante->direccion ?? '') }}" placeholder="Av. Montenegro #123"/>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">Zona</label>
                            <select name="zona" class="w-full bg-surface-container-highest border-0 rounded-lg p-4 focus:ring-2 focus:ring-primary transition-all font-medium">
                                @foreach(['Sopocachi','Zona Sur','Miraflores','Centro','Achumani','Otra'] as $zona)
                                <option value="{{ $zona }}" {{ old('zona', $restaurante->zona ?? '') === $zona ? 'selected' : '' }}>{{ $zona }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Leaflet Map -->
                    @php
                        $lat = old('latitud', $restaurante->latitud ?? -16.500000);
                        $lng = old('longitud', $restaurante->longitud ?? -68.119293);
                    @endphp
                    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
                    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                    <div id="config-map" class="h-[300px] rounded-lg overflow-hidden border border-outline-variant/20 z-10"></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-stone-400 uppercase tracking-widest mb-1">Latitud</label>
                            <input id="latitud" name="latitud" class="w-full bg-surface-container-low border-0 rounded-lg p-2 text-sm font-mono" readonly type="text" value="{{ $lat }}"/>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-stone-400 uppercase tracking-widest mb-1">Longitud</label>
                            <input id="longitud" name="longitud" class="w-full bg-surface-container-low border-0 rounded-lg p-2 text-sm font-mono" readonly type="text" value="{{ $lng }}"/>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Schedules -->
            <div class="space-y-6">
                <div class="bg-surface-container-lowest p-8 rounded-xl shadow-sm space-y-6 sticky top-28">
                    <h3 class="text-xl font-bold font-headline flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">schedule</span>
                        Horarios de Atención
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 rounded-lg bg-surface-container-low hover:bg-orange-50 transition-colors">
                            <span class="font-semibold text-sm text-on-surface">Lunes - Viernes</span>
                            <div class="flex items-center gap-1">
                                <input name="horario_apertura" class="bg-transparent border-0 text-primary font-bold p-0 focus:ring-0 text-sm w-20 text-center" type="time" value="{{ old('horario_apertura', $restaurante->horario_apertura ?? '09:00') }}">
                                <span class="text-stone-400">—</span>
                                <input name="horario_cierre" class="bg-transparent border-0 text-primary font-bold p-0 focus:ring-0 text-sm w-20 text-center" type="time" value="{{ old('horario_cierre', $restaurante->horario_cierre ?? '22:00') }}">
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-surface-container-low hover:bg-orange-50 transition-colors">
                            <span class="font-semibold text-sm text-on-surface">Sábado</span>
                            <div class="flex items-center gap-1">
                                <input name="hora_apertura_sabado" class="bg-transparent border-0 text-primary font-bold p-0 focus:ring-0 text-sm w-20 text-center" type="time" value="{{ old('hora_apertura_sabado', $restaurante->hora_apertura_sabado ?? '10:00') }}">
                                <span class="text-stone-400">—</span>
                                <input name="hora_cierre_sabado" class="bg-transparent border-0 text-primary font-bold p-0 focus:ring-0 text-sm w-20 text-center" type="time" value="{{ old('hora_cierre_sabado', $restaurante->hora_cierre_sabado ?? '23:30') }}">
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-surface-container-low hover:bg-orange-50 transition-colors">
                            <span class="font-semibold text-sm text-on-surface">Domingo</span>
                            <div class="flex items-center gap-1">
                                <input name="hora_apertura_domingo" class="bg-transparent border-0 text-primary font-bold p-0 focus:ring-0 text-sm w-20 text-center" type="time" value="{{ old('hora_apertura_domingo', $restaurante->hora_apertura_domingo ?? '11:00') }}">
                                <span class="text-stone-400">—</span>
                                <input name="hora_cierre_domingo" class="bg-transparent border-0 text-primary font-bold p-0 focus:ring-0 text-sm w-20 text-center" type="time" value="{{ old('hora_cierre_domingo', $restaurante->hora_cierre_domingo ?? '16:00') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Estado del Local -->
                    <div class="pt-6 border-t border-outline-variant/30">
                        <h4 class="text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-4">Estado del Local</h4>
                        <div class="flex items-center gap-4 bg-green-50 p-4 rounded-xl">
                            <div class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-green-900 leading-none">Visible y Activo</p>
                                <p class="text-xs text-green-700 mt-1">Los clientes pueden reservar ahora.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Branding -->
                    <div class="pt-6 border-t border-outline-variant/30" id="branding-section">
                        <h4 class="text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-4">Identidad Visual</h4>
                        <p class="text-xs text-stone-500 mb-4">Personaliza los colores de tu página pública.</p>

                        <label class="flex items-center gap-3 mb-4 cursor-pointer">
                            <input type="hidden" name="brand_enabled" value="0">
                            <input type="checkbox" name="brand_enabled" value="1" class="w-5 h-5 rounded border-stone-300 text-primary focus:ring-primary" {{ old('brand_enabled', $restaurante->brand_enabled ?? true) ? 'checked' : '' }} onchange="toggleBrandingPreview()">
                            <span class="text-sm font-semibold">Personalizar colores</span>
                        </label>

                        <div id="branding-fields" class="space-y-3 {{ (old('brand_enabled', $restaurante->brand_enabled ?? true)) ? '' : 'opacity-50 pointer-events-none' }}">
                            <div>
                                <label class="block text-[10px] font-bold text-stone-400 uppercase tracking-widest mb-1">Color Principal</label>
                                <div class="flex items-center gap-2">
                                    <input name="brand_primary_color" type="color" class="w-10 h-10 rounded-lg border-0 cursor-pointer" value="{{ old('brand_primary_color', $restaurante->brand_primary_color ?? '#9e2016') }}" oninput="updateBrandingPreview()">
                                    <input name="brand_primary_color_hex" type="text" class="flex-1 bg-surface-container-highest border-0 rounded-lg p-2 text-sm font-mono uppercase" value="{{ old('brand_primary_color', $restaurante->brand_primary_color ?? '#9e2016') }}" oninput="syncColorInput(this, 'brand_primary_color')" placeholder="#9e2016" pattern="^#[0-9A-Fa-f]{6}$">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-stone-400 uppercase tracking-widest mb-1">Color Secundario</label>
                                <div class="flex items-center gap-2">
                                    <input name="brand_secondary_color" type="color" class="w-10 h-10 rounded-lg border-0 cursor-pointer" value="{{ old('brand_secondary_color', $restaurante->brand_secondary_color ?? '#c0392b') }}" oninput="updateBrandingPreview()">
                                    <input name="brand_secondary_color_hex" type="text" class="flex-1 bg-surface-container-highest border-0 rounded-lg p-2 text-sm font-mono uppercase" value="{{ old('brand_secondary_color', $restaurante->brand_secondary_color ?? '#c0392b') }}" oninput="syncColorInput(this, 'brand_secondary_color')" placeholder="#c0392b" pattern="^#[0-9A-Fa-f]{6}$">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-stone-400 uppercase tracking-widest mb-1">Color de Acento</label>
                                <div class="flex items-center gap-2">
                                    <input name="brand_accent_color" type="color" class="w-10 h-10 rounded-lg border-0 cursor-pointer" value="{{ old('brand_accent_color', $restaurante->brand_accent_color ?? '#fc8f34') }}" oninput="updateBrandingPreview()">
                                    <input name="brand_accent_color_hex" type="text" class="flex-1 bg-surface-container-highest border-0 rounded-lg p-2 text-sm font-mono uppercase" value="{{ old('brand_accent_color', $restaurante->brand_accent_color ?? '#fc8f34') }}" oninput="syncColorInput(this, 'brand_accent_color')" placeholder="#fc8f34" pattern="^#[0-9A-Fa-f]{6}$">
                                </div>
                            </div>
                        </div>

                        <!-- Preview -->
                        <div class="mt-4 p-4 rounded-xl bg-white border border-stone-200" id="branding-preview">
                            <p class="text-[10px] font-bold text-stone-400 uppercase tracking-widest mb-3">Vista previa</p>
                            <div class="space-y-3">
                                <div class="h-12 rounded-lg flex items-center justify-center text-white font-bold text-sm" id="preview-header" style="background-color:#9e2016">
                                    {{ $restaurante->nombre ?? 'Mi Restaurante' }}
                                </div>
                                <div class="flex gap-2">
                                    <span class="px-3 py-1 text-xs font-bold rounded-full text-white" id="preview-badge" style="background-color:#c0392b">Promoción</span>
                                    <span class="px-3 py-1 text-xs font-bold rounded-full text-white" id="preview-badge-accent" style="background-color:#fc8f34">2x1</span>
                                </div>
                                <div class="flex gap-2">
                                    <button class="flex-1 py-2 rounded-lg text-white text-sm font-bold" id="preview-btn" style="background-color:#9e2016">Ver Menú</button>
                                    <button class="flex-1 py-2 rounded-lg text-white text-sm font-bold" id="preview-btn-secondary" style="background-color:#c0392b">Reservar</button>
                                </div>
                                <div class="p-3 rounded-lg text-sm" id="preview-card" style="background-color:#fff8f2;border:1px solid #fc8f34">
                                    <span class="font-bold" id="preview-card-title" style="color:#9e2016">Plato destacado</span>
                                    <p class="text-xs text-stone-600">Descripción del plato</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full mt-4 px-8 py-4 rounded-xl bg-primary text-white font-bold shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all bg-gradient-to-br from-primary to-primary-container">
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const lat = parseFloat('{{ $lat }}');
    const lng = parseFloat('{{ $lng }}');
    
    const map = L.map('config-map').setView([lat, lng], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    let marker = L.marker([lat, lng]).addTo(map);

    map.on('click', function(e) {
        const newLat = e.latlng.lat;
        const newLng = e.latlng.lng;
        if (marker) { map.removeLayer(marker); }
        marker = L.marker([newLat, newLng]).addTo(map);
        document.getElementById('latitud').value = newLat.toFixed(8);
        document.getElementById('longitud').value = newLng.toFixed(8);
    });

    setTimeout(() => map.invalidateSize(), 300);
});

function previewPortada(input) {
    const preview = document.getElementById('portada-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function syncColorInput(textInput, baseName) {
    const val = textInput.value.trim();
    if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
        document.querySelector(`input[name="${baseName}"]`).value = val;
        updateBrandingPreview();
    }
}

function toggleBrandingPreview() {
    const enabled = document.querySelector('input[name="brand_enabled"]').checked;
    const fields = document.getElementById('branding-fields');
    fields.classList.toggle('opacity-50', !enabled);
    fields.classList.toggle('pointer-events-none', !enabled);
    updateBrandingPreview();
}

function updateBrandingPreview() {
    const enabled = document.querySelector('input[name="brand_enabled"]').checked;
    const primary = document.querySelector('input[name="brand_primary_color"]').value || '#9e2016';
    const secondary = document.querySelector('input[name="brand_secondary_color"]').value || '#c0392b';
    const accent = document.querySelector('input[name="brand_accent_color"]').value || '#fc8f34';

    const p = enabled ? primary : '#9e2016';
    const s = enabled ? secondary : '#c0392b';
    const a = enabled ? accent : '#fc8f34';

    document.getElementById('preview-header').style.backgroundColor = p;
    document.getElementById('preview-btn').style.backgroundColor = p;
    document.getElementById('preview-card-title').style.color = p;
    document.getElementById('preview-badge').style.backgroundColor = s;
    document.getElementById('preview-btn-secondary').style.backgroundColor = s;
    document.getElementById('preview-badge-accent').style.backgroundColor = a;
    document.getElementById('preview-card').style.backgroundColor = '#fff8f2';
    document.getElementById('preview-card').style.borderColor = a;
}
</script>
@endsection
