@php
    $toastType = null;
    $toastMessage = null;

    if (session('success')) {
        $toastType = 'success';
        $toastMessage = session('success');
    } elseif ($errors->any()) {
        $toastType = 'error';
        $toastMessage = $errors->all();
    }

    $toastStyles = [
        'success' => 'border-emerald-200 bg-white text-emerald-900',
        'error' => 'border-red-200 bg-white text-red-900',
    ];

    $toastIcons = [
        'success' => 'check_circle',
        'error' => 'error',
    ];
@endphp

@if($toastType && $toastMessage)
    <div class="pointer-events-none fixed inset-0 z-[90] flex items-center justify-center p-4">
        <div
            data-screen-toast
            data-timeout="3800"
            class="sidebar-toast pointer-events-auto hidden w-full max-w-md rounded-[1.75rem] border px-6 py-5 shadow-2xl transition duration-200 opacity-0 translate-y-4 scale-95 {{ $toastStyles[$toastType] }}"
        >
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl {{ $toastType === 'success' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }}">
                    <span class="material-symbols-outlined">{{ $toastIcons[$toastType] }}</span>
                </div>

                <div class="min-w-0 flex-1">
                    <p class="font-headline text-lg font-extrabold">
                        {{ $toastType === 'success' ? 'Cambio guardado' : 'Revisa el formulario' }}
                    </p>

                    @if(is_array($toastMessage))
                        <ul class="mt-2 space-y-1 text-sm">
                            @foreach($toastMessage as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mt-2 text-sm text-current/80">{{ $toastMessage }}</p>
                    @endif
                </div>

                <button type="button" data-toast-close class="rounded-full p-2 text-current/60 transition hover:bg-black/5 hover:text-current">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
        </div>
    </div>
@endif
