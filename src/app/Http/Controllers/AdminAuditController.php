<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use Illuminate\Http\Request;

class AdminAuditController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'modulo' => 'nullable|string|max:50',
            'accion' => 'nullable|string|max:50',
            'usuario_id' => 'nullable|integer|exists:usuarios,id',
            'entidad' => 'nullable|string|max:100',
            'entidad_id' => 'nullable|string|max:50',
            'q' => 'nullable|string|max:200',
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ]);

        $query = Auditoria::with('usuario:id,nombre');

        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }

        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }

        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        if ($request->filled('entidad')) {
            $query->where('entidad', $request->entidad);
        }

        if ($request->filled('entidad_id')) {
            $query->where('entidad_id', $request->entidad_id);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('descripcion', 'like', "%{$q}%")
                    ->orWhere('meta', 'like', "%{$q}%");
            });
        }

        if ($request->filled('from')) {
            $query->where('created_at', '>=', $request->from . ' 00:00:00');
        }

        if ($request->filled('to')) {
            $query->where('created_at', '<=', $request->to . ' 23:59:59');
        }

        $eventos = $query->orderByDesc('created_at')->paginate(30);

        $modulos = Auditoria::select('modulo')->distinct()->pluck('modulo');
        $acciones = Auditoria::select('accion')->distinct()->pluck('accion');
        $entidades = Auditoria::select('entidad')->whereNotNull('entidad')->distinct()->orderBy('entidad')->pluck('entidad');

        $admins = \App\Models\Usuario::whereHas('auditorias')->select('id', 'nombre')->get();

        return view('admin.auditoria.index', compact('eventos', 'modulos', 'acciones', 'entidades', 'admins'));
    }
}
