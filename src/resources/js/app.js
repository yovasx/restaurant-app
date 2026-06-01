import './bootstrap';

const MODAL_OPEN_CLASS = 'overflow-hidden';

function initDropzones(root = document) {
    root.querySelectorAll('[data-dropzone="true"]').forEach((zone) => {
        if (zone.dataset.dropzoneBound === 'true') {
            return;
        }

        zone.dataset.dropzoneBound = 'true';

        const inputId = zone.dataset.input;
        const previewId = zone.dataset.preview;
        const placeholderId = zone.dataset.placeholder;
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        const placeholder = placeholderId ? document.getElementById(placeholderId) : null;

        if (!input || !preview) {
            return;
        }

        zone.addEventListener('click', () => input.click());

        zone.addEventListener('dragover', (event) => {
            event.preventDefault();
            zone.classList.add('border-[#9e2016]', 'bg-red-50', 'scale-[1.01]');
        });

        zone.addEventListener('dragleave', () => {
            zone.classList.remove('border-[#9e2016]', 'bg-red-50', 'scale-[1.01]');
        });

        zone.addEventListener('drop', (event) => {
            event.preventDefault();
            zone.classList.remove('border-[#9e2016]', 'bg-red-50', 'scale-[1.01]');

            const files = event.dataTransfer?.files;
            if (!files?.[0]) {
                return;
            }

            const transfer = new DataTransfer();
            transfer.items.add(files[0]);
            input.files = transfer.files;
            showPreview(files[0], preview, placeholder);
        });

        input.addEventListener('change', () => {
            if (input.files?.[0]) {
                showPreview(input.files[0], preview, placeholder);
            }
        });
    });
}

function showPreview(file, preview, placeholder) {
    const reader = new FileReader();

    reader.onload = (event) => {
        preview.src = event.target?.result ?? '';
        preview.classList.remove('hidden');
        placeholder?.classList.add('hidden');
    };

    reader.readAsDataURL(file);
}

function openModal(modal) {
    if (!modal) {
        return;
    }

    const alreadyOpen = document.querySelector('[data-modal][data-open="true"]');
    if (alreadyOpen && alreadyOpen !== modal) {
        closeModal(alreadyOpen);
    }

    modal.classList.remove('hidden');
    modal.dataset.open = 'true';
    document.body.classList.add(MODAL_OPEN_CLASS);
    initDropzones(modal);

    const autoFocusTarget = modal.querySelector('[data-modal-initial-focus], input, select, textarea, button');
    autoFocusTarget?.focus();
}

function closeModal(modal) {
    if (!modal) {
        return;
    }

    modal.classList.add('hidden');
    modal.dataset.open = 'false';

    if (!document.querySelector('[data-modal][data-open="true"]')) {
        document.body.classList.remove(MODAL_OPEN_CLASS);
    }
}

function initModals() {
    document.addEventListener('click', (event) => {
        const openTrigger = event.target.closest('[data-modal-open]');
        if (openTrigger) {
            const modal = document.getElementById(openTrigger.dataset.modalOpen);
            if (modal) {
                openModal(modal);
            }
            return;
        }

        const closeTrigger = event.target.closest('[data-modal-close]');
        if (!closeTrigger) {
            return;
        }

        const modal = closeTrigger.closest('[data-modal]');
        closeModal(modal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        const modal = document.querySelector('[data-modal][data-open="true"]');
        if (modal) {
            closeModal(modal);
        }
    });

    document.querySelectorAll('[data-modal-auto-open="true"]').forEach((modal) => {
        openModal(modal);
    });
}

function initToasts() {
    const toast = document.querySelector('[data-screen-toast]');
    if (!toast) {
        return;
    }

    const dismiss = () => {
        toast.classList.add('opacity-0', 'translate-x-4', 'scale-95');
        window.setTimeout(() => toast.remove(), 250);
    };

    toast.classList.remove('hidden');
    window.requestAnimationFrame(() => {
        toast.classList.remove('opacity-0', 'translate-x-4', 'scale-95');
    });

    toast.querySelector('[data-toast-close]')?.addEventListener('click', dismiss);
    window.setTimeout(dismiss, Number(toast.dataset.timeout || 3500));
}

function boot() {
    initDropzones();
    initModals();
    initToasts();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}
