<?php $__env->startSection('content'); ?>
<header class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-3xl font-extrabold text-on-surface tracking-tight mb-1">Auditoría</h2>
        <p class="text-on-surface-variant font-medium">Bitácora de acciones administrativas.</p>
    </div>
</header>

<form method="GET" action="<?php echo e(route('admin.auditoria.index')); ?>" class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-stone-100/50 mb-8">
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div>
            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1 block">Módulo</label>
            <select name="modulo" class="w-full bg-surface-container-highest border-0 rounded-lg p-3 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium text-sm">
                <option value="">Todos</option>
                <?php $__currentLoopData = $modulos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($m); ?>" <?php echo e(request('modulo') === $m ? 'selected' : ''); ?>><?php echo e(ucfirst($m)); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1 block">Acción</label>
            <select name="accion" class="w-full bg-surface-container-highest border-0 rounded-lg p-3 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium text-sm">
                <option value="">Todas</option>
                <?php $__currentLoopData = $acciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($a); ?>" <?php echo e(request('accion') === $a ? 'selected' : ''); ?>><?php echo e(str_replace('_', ' ', ucfirst($a))); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1 block">Admin</label>
            <select name="usuario_id" class="w-full bg-surface-container-highest border-0 rounded-lg p-3 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium text-sm">
                <option value="">Todos</option>
                <?php $__currentLoopData = $admins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($admin->id); ?>" <?php echo e(request('usuario_id') == $admin->id ? 'selected' : ''); ?>><?php echo e($admin->nombre); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1 block">Desde</label>
            <input type="date" name="from" value="<?php echo e(request('from')); ?>" class="w-full bg-surface-container-highest border-0 rounded-lg p-3 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium text-sm">
        </div>
        <div>
            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1 block">Hasta</label>
            <input type="date" name="to" value="<?php echo e(request('to')); ?>" class="w-full bg-surface-container-highest border-0 rounded-lg p-3 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium text-sm">
        </div>
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
        <div class="lg:col-span-2">
            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1 block">Búsqueda</label>
            <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Buscar en descripción o metadatos..." class="w-full bg-surface-container-highest border-0 rounded-lg p-3 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium text-sm">
        </div>
        <div>
            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1 block">Entidad</label>
            <select name="entidad" class="w-full bg-surface-container-highest border-0 rounded-lg p-3 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium text-sm">
                <option value="">Todas</option>
                <?php $__currentLoopData = $entidades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($ent); ?>" <?php echo e(request('entidad') === $ent ? 'selected' : ''); ?>><?php echo e(ucfirst($ent)); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1 block">ID Entidad</label>
            <input type="text" name="entidad_id" value="<?php echo e(request('entidad_id')); ?>" placeholder="Ej: 42" class="w-full bg-surface-container-highest border-0 rounded-lg p-3 focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-medium text-sm">
        </div>
    </div>
    <div class="flex gap-3 mt-4">
        <button type="submit" class="flex-1 lg:flex-none bg-primary text-on-primary px-6 py-3 rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:bg-primary-container transition-all">Filtrar</button>
        <a href="<?php echo e(route('admin.auditoria.index')); ?>" class="flex-1 lg:flex-none text-center bg-stone-100 text-stone-600 px-6 py-3 rounded-xl font-bold text-sm hover:bg-stone-200 transition-all">Limpiar</a>
    </div>
</form>

<div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-100/50 overflow-hidden">
    <?php if($eventos->count() > 0): ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-stone-50 text-left">
                        <th class="px-5 py-4 text-[10px] font-bold text-stone-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-5 py-4 text-[10px] font-bold text-stone-500 uppercase tracking-wider">Admin</th>
                        <th class="px-5 py-4 text-[10px] font-bold text-stone-500 uppercase tracking-wider">Módulo</th>
                        <th class="px-5 py-4 text-[10px] font-bold text-stone-500 uppercase tracking-wider">Acción</th>
                        <th class="px-5 py-4 text-[10px] font-bold text-stone-500 uppercase tracking-wider">Entidad</th>
                        <th class="px-5 py-4 text-[10px] font-bold text-stone-500 uppercase tracking-wider">Descripción</th>
                        <th class="px-5 py-4 text-[10px] font-bold text-stone-500 uppercase tracking-wider w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    <?php $__currentLoopData = $eventos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-stone-50/50 transition-colors cursor-pointer" onclick="const r=this.nextElementSibling; if(r)r.classList.toggle('hidden')">
                        <td class="px-5 py-4 text-xs text-stone-600 whitespace-nowrap"><?php echo e($e->created_at->format('d/m/Y H:i')); ?></td>
                        <td class="px-5 py-4 text-sm font-medium text-on-surface whitespace-nowrap"><?php echo e($e->usuario?->nombre ?? '—'); ?></td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider 
                                <?php echo e($e->modulo === 'backups' ? 'bg-purple-100 text-purple-700' : ''); ?>

                                <?php echo e($e->modulo === 'reportes' ? 'bg-blue-100 text-blue-700' : ''); ?>

                                <?php echo e($e->modulo === 'restaurantes' ? 'bg-orange-100 text-orange-700' : ''); ?>

                                <?php echo e($e->modulo === 'comensales' ? 'bg-teal-100 text-teal-700' : ''); ?>

                                <?php echo e($e->modulo === 'categorias' ? 'bg-pink-100 text-pink-700' : ''); ?>

                                <?php echo e($e->modulo === 'roles' ? 'bg-indigo-100 text-indigo-700' : ''); ?>

                                <?php echo e($e->modulo === 'usuarios' ? 'bg-stone-100 text-stone-700' : ''); ?>

                                <?php echo e($e->modulo === 'auth' ? 'bg-red-100 text-red-700' : ''); ?>

                            "><?php echo e($e->modulo); ?></span>
                        </td>
                        <td class="px-5 py-4 text-sm text-stone-700 whitespace-nowrap"><?php echo e($e->accionLabel()); ?></td>
                        <td class="px-5 py-4 text-sm text-stone-600 whitespace-nowrap"><?php echo e($e->entidad ?? '—'); ?> <?php echo e($e->entidad_id ? "#{$e->entidad_id}" : ''); ?></td>
                        <td class="px-5 py-4 text-sm text-stone-600 max-w-xs truncate" title="<?php echo e($e->descripcion); ?>"><?php echo e($e->descripcion ?? '—'); ?></td>
                        <td class="px-5 py-4 text-center">
                            <span class="text-stone-400 text-xs font-bold">+</span>
                        </td>
                    </tr>
                    <tr class="hidden">
                        <td colspan="7" class="px-5 py-4 bg-stone-50/70">
                            <div class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
                                <div><span class="font-semibold text-stone-500 text-[10px] uppercase tracking-wider">Fecha exacta</span><br><span class="text-stone-700"><?php echo e($e->created_at->format('d/m/Y H:i:s')); ?></span></div>
                                <div><span class="font-semibold text-stone-500 text-[10px] uppercase tracking-wider">IP</span><br><span class="text-stone-700"><?php echo e($e->meta['ip'] ?? '—'); ?></span></div>
                                <div><span class="font-semibold text-stone-500 text-[10px] uppercase tracking-wider">Admin</span><br><span class="text-stone-700"><?php echo e($e->usuario?->nombre ?? '—'); ?> (ID <?php echo e($e->usuario_id); ?>)</span></div>
                                <div><span class="font-semibold text-stone-500 text-[10px] uppercase tracking-wider">Ruta</span><br><span class="text-stone-700"><?php echo e($e->meta['ruta'] ?? '—'); ?></span></div>
                                <div><span class="font-semibold text-stone-500 text-[10px] uppercase tracking-wider">Entidad</span><br><span class="text-stone-700"><?php echo e($e->entidad ?? '—'); ?> <?php echo e($e->entidad_id ? "#{$e->entidad_id}" : ''); ?></span></div>
                                <div><span class="font-semibold text-stone-500 text-[10px] uppercase tracking-wider">Route name</span><br><span class="text-stone-700"><?php echo e($e->meta['route_name'] ?? '—'); ?></span></div>
                                <?php if($e->descripcion): ?>
                                <div class="col-span-2"><span class="font-semibold text-stone-500 text-[10px] uppercase tracking-wider">Descripción</span><br><span class="text-stone-700"><?php echo e($e->descripcion); ?></span></div>
                                <?php endif; ?>
                                <?php $cambios = $e->cambios(); ?>
                                <?php if(!empty($cambios)): ?>
                                <div class="col-span-2 mt-2">
                                    <span class="font-semibold text-stone-500 text-[10px] uppercase tracking-wider">Cambios</span>
                                    <div class="mt-1 space-y-1">
                                        <?php $__currentLoopData = $cambios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="text-xs flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg border border-stone-100">
                                            <span class="font-medium text-stone-600 min-w-[100px]"><?php echo e($c['campo']); ?>:</span>
                                            <span class="line-through text-red-500"><?php echo e(is_scalar($c['antes']) ? $c['antes'] : (is_null($c['antes']) ? '—' : json_encode($c['antes']))); ?></span>
                                            <span class="text-stone-300">→</span>
                                            <span class="text-green-600 font-medium"><?php echo e(is_scalar($c['despues']) ? $c['despues'] : (is_null($c['despues']) ? '—' : json_encode($c['despues']))); ?></span>
                                        </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-stone-100">
            <?php echo e($eventos->appends(request()->query())->onEachSide(1)->links()); ?>

        </div>
    <?php else: ?>
        <div class="text-center py-12">
            <span class="material-symbols-outlined text-4xl text-stone-300 mb-3">history</span>
            <p class="text-stone-500 font-medium">No se encontraron eventos de auditoría.</p>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\yovan\Proyecto-restaurant\src\resources\views/admin/auditoria/index.blade.php ENDPATH**/ ?>