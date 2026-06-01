@php
    $nombre = old('nombre', $sucursal?->nombre ?? '');
    $descripcion = old('descripcion', $sucursal?->descripcion ?? '');
    $telefono = old('telefono', $sucursal?->telefono ?? '');
    $email_reservas = old('email_reservas', $sucursal?->email_reservas ?? '');
    $instagram = old('instagram', $sucursal?->instagram ?? '');
    $facebook_url = old('facebook_url', $sucursal?->facebook_url ?? '');
    $direccion = old('direccion', $sucursal?->direccion ?? '');
    $zona = old('zona', $sucursal?->zona ?? '');
    $lat = old('latitud', $sucursal?->latitud ?? -16.500000);
    $lng = old('longitud', $sucursal?->longitud ?? -68.119293);
    $horario_apertura = old('horario_apertura', $sucursal?->horario_apertura ?? '09:00');
    $horario_cierre = old('horario_cierre', $sucursal?->horario_cierre ?? '22:00');
    $hora_apertura_sabado = old('hora_apertura_sabado', $sucursal?->hora_apertura_sabado ?? '10:00');
    $hora_cierre_sabado = old('hora_cierre_sabado', $sucursal?->hora_cierre_sabado ?? '23:30');
    $hora_apertura_domingo = old('hora_apertura_domingo', $sucursal?->hora_apertura_domingo ?? '11:00');
    $hora_cierre_domingo = old('hora_cierre_domingo', $sucursal?->hora_cierre_domingo ?? '16:00');
    $foto_portada = $sucursal?->foto_portada ?? null;
@endphp
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-4">
        <div>
            <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">Nombre de la Sucursal</label>
            <input name="nombre" class="w-full bg-surface-container-highest border-0 rounded-lg p-4 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium" type="text" required value="{{ $nombre }}" placeholder="Ej: Gustu Sopocachi"/>
        </div>
        <div>
            <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">Descripción</label>
            <textarea name="descripcion" class="w-full bg-surface-container-highest border-0 rounded-lg p-4 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium leading-relaxed" rows="3" placeholder="Descripción de esta sucursal...">{{ $descripcion }}</textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">Teléfono</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-500 font-semibold">+591</span>
                    <input name="telefono" class="w-full bg-surface-container-highest border-0 rounded-lg p-4 pl-16 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium" type="tel" required value="{{ $telefono }}"/>
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">Email de Reservas</label>
                <input name="email_reservas" class="w-full bg-surface-container-highest border-0 rounded-lg p-4 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium" type="email" required value="{{ $email_reservas }}" placeholder="reservas@sucursal.com"/>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">Instagram</label>
                <input name="instagram" class="w-full bg-surface-container-highest border-0 rounded-lg p-4 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium" type="text" value="{{ $instagram }}" placeholder="@usuario"/>
            </div>
            <div>
                <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">Facebook / Web</label>
                <input name="facebook_url" class="w-full bg-surface-container-highest border-0 rounded-lg p-4 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium" type="text" value="{{ $facebook_url }}" placeholder="facebook.com/..."/>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">Dirección</label>
                <input name="direccion" class="w-full bg-surface-container-highest border-0 rounded-lg p-4 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium" type="text" required value="{{ $direccion }}" placeholder="Av. Montenegro #123"/>
            </div>
            <div>
                <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">Zona</label>
                <select name="zona" class="w-full bg-surface-container-highest border-0 rounded-lg p-4 focus:ring-2 focus:ring-primary transition-all font-medium" required>
                    @foreach(['Sopocachi','Zona Sur','Miraflores','Centro','Achumani','Otra'] as $opt)
                    <option value="{{ $opt }}" {{ $zona === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">Foto de Portada</label>
            <div class="relative h-40 rounded-xl overflow-hidden bg-surface-container-highest group cursor-pointer" onclick="document.getElementById('foto_portada_suc').click()">
                @if($foto_portada)
                <img id="portada-preview-suc" class="w-full h-full object-cover" src="{{ media_url($foto_portada) }}" alt="Portada">
                @else
                <div id="portada-placeholder-suc" class="w-full h-full flex items-center justify-center flex-col gap-1 text-stone-400">
                    <span class="material-symbols-outlined text-3xl">add_photo_alternate</span>
                    <span class="text-xs font-semibold">Subir foto</span>
                </div>
                <img id="portada-preview-suc" class="w-full h-full object-cover hidden" src="" alt="Portada">
                @endif
                <div class="absolute inset-0 bg-black/30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <span class="text-white text-xs font-bold">Cambiar</span>
                </div>
            </div>
            <input type="file" id="foto_portada_suc" name="foto_portada" class="hidden" accept="image/*" onchange="document.getElementById('portada-placeholder-suc')?.classList.add('hidden');const p=document.getElementById('portada-preview-suc');if(this.files[0]){const r=new FileReader();r.onload=function(e){p.src=e.target.result;p.classList.remove('hidden')};r.readAsDataURL(this.files[0])}">
        </div>
        <div class="space-y-3">
            <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">Horarios</label>
            <div class="flex items-center justify-between p-2 rounded-lg bg-surface-container-low">
                <span class="text-xs font-semibold text-on-surface">Lun - Vie</span>
                <div class="flex items-center gap-1">
                    <input name="horario_apertura" class="bg-transparent border-0 text-primary font-bold p-0 focus:ring-0 text-xs w-16 text-center" type="time" value="{{ $horario_apertura }}">
                    <span class="text-stone-400">—</span>
                    <input name="horario_cierre" class="bg-transparent border-0 text-primary font-bold p-0 focus:ring-0 text-xs w-16 text-center" type="time" value="{{ $horario_cierre }}">
                </div>
            </div>
            <div class="flex items-center justify-between p-2 rounded-lg bg-surface-container-low">
                <span class="text-xs font-semibold text-on-surface">Sábado</span>
                <div class="flex items-center gap-1">
                    <input name="hora_apertura_sabado" class="bg-transparent border-0 text-primary font-bold p-0 focus:ring-0 text-xs w-16 text-center" type="time" value="{{ $hora_apertura_sabado }}">
                    <span class="text-stone-400">—</span>
                    <input name="hora_cierre_sabado" class="bg-transparent border-0 text-primary font-bold p-0 focus:ring-0 text-xs w-16 text-center" type="time" value="{{ $hora_cierre_sabado }}">
                </div>
            </div>
            <div class="flex items-center justify-between p-2 rounded-lg bg-surface-container-low">
                <span class="text-xs font-semibold text-on-surface">Domingo</span>
                <div class="flex items-center gap-1">
                    <input name="hora_apertura_domingo" class="bg-transparent border-0 text-primary font-bold p-0 focus:ring-0 text-xs w-16 text-center" type="time" value="{{ $hora_apertura_domingo }}">
                    <span class="text-stone-400">—</span>
                    <input name="hora_cierre_domingo" class="bg-transparent border-0 text-primary font-bold p-0 focus:ring-0 text-xs w-16 text-center" type="time" value="{{ $hora_cierre_domingo }}">
                </div>
            </div>
        </div>
        <div class="space-y-2">
            <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">Ubicación</label>
            <input name="latitud" class="w-full bg-surface-container-low border-0 rounded-lg p-2 text-xs font-mono" readonly type="text" value="{{ $lat }}">
            <input name="longitud" class="w-full bg-surface-container-low border-0 rounded-lg p-2 text-xs font-mono" readonly type="text" value="{{ $lng }}">
        </div>
    </div>
</div>
