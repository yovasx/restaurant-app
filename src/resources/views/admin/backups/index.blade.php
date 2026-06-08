@extends('layouts.admin')

@section('content')
<header class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h2 class="text-3xl font-extrabold text-on-surface tracking-tight mb-1">Backups</h2>
        <p class="text-on-surface-variant font-medium">Archivos de respaldo de base de datos disponibles para descarga.</p>
    </div>
    <form method="POST" action="{{ route('admin.backups.generate') }}" class="inline" id="backup-form">
        @csrf
        <button type="submit" id="btn-generate" class="inline-flex items-center gap-2 bg-primary text-on-primary px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:bg-primary-container transition-all disabled:opacity-50 disabled:cursor-not-allowed" onclick="this.disabled=true; this.innerHTML='<span class=\'material-symbols-outlined\' style=\'font-size:18px\'>sync</span> Generando...'; document.getElementById('backup-form').submit();">
            <span class="material-symbols-outlined" style="font-size: 18px;">backup</span>
            Generar Backup
        </button>
    </form>
</header>


<div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 overflow-hidden">
    @if(count($files) > 0)
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-stone-500 text-xs font-bold uppercase tracking-wider bg-surface-container-low">
                    <th class="px-6 py-4">Nombre</th>
                    <th class="px-6 py-4">Tamaño</th>
                    <th class="px-6 py-4">Fecha</th>
                    <th class="px-6 py-4 text-right">Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($files as $file)
                <tr class="border-t border-stone-100 hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-4 font-medium text-on-surface">{{ $file->name }}</td>
                    <td class="px-6 py-4 text-stone-500">
                        @if($file->size > 1048576)
                            {{ round($file->size / 1048576, 2) }} MB
                        @else
                            {{ round($file->size / 1024, 1) }} KB
                        @endif
                    </td>
                    <td class="px-6 py-4 text-stone-500">{{ \Carbon\Carbon::createFromTimestamp($file->last_modified)->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.backups.download', $file->name) }}" class="inline-flex items-center gap-1 bg-primary text-on-primary px-4 py-2 rounded-lg font-bold text-xs shadow-sm hover:bg-primary-container transition-all">
                            <span class="material-symbols-outlined" style="font-size: 16px;">download</span>
                            Descargar
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="p-12 text-center">
            <span class="material-symbols-outlined text-5xl text-stone-300 mb-4">backup</span>
            <p class="text-stone-500 font-medium">No hay backups disponibles.</p>
            <p class="text-stone-400 text-sm mt-1">Presiona "Generar Backup" para crear el primer respaldo.</p>
        </div>
    @endif
</div>
@endsection
