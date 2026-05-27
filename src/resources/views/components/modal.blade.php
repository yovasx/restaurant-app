@props([
    'id',
    'title' => null,
    'subtitle' => null,
    'maxWidth' => 'max-w-3xl',
    'autoOpen' => false,
])

<div
    id="{{ $id }}"
    data-modal
    data-open="false"
    data-modal-auto-open="{{ $autoOpen ? 'true' : 'false' }}"
    class="fixed inset-0 z-[80] hidden"
    aria-modal="true"
    role="dialog"
>
    <div class="absolute inset-0 bg-stone-950/55 backdrop-blur-[2px]" data-modal-close></div>

    <div class="relative flex min-h-full items-center justify-center p-4 sm:p-6">
        <div class="relative w-full {{ $maxWidth }} rounded-[1.75rem] border border-stone-200 bg-white shadow-2xl shadow-stone-950/20">
            <div class="flex items-start justify-between gap-4 border-b border-stone-100 px-6 py-5 sm:px-7">
                <div>
                    @if($title)
                        <h3 class="font-headline text-2xl font-extrabold text-on-surface">{{ $title }}</h3>
                    @endif
                    @if($subtitle)
                        <p class="mt-1 text-sm text-stone-500">{{ $subtitle }}</p>
                    @endif
                </div>

                <button
                    type="button"
                    data-modal-close
                    class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-stone-100 text-stone-500 transition hover:bg-stone-200 hover:text-stone-700"
                    aria-label="Cerrar"
                >
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="max-h-[calc(100vh-10rem)] overflow-y-auto px-6 py-6 sm:px-7">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
