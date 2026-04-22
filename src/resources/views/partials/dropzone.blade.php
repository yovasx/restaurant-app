{{-- 
    Drop Zone JS Partial
    Adds drag-and-drop + click-to-upload behaviour to any element with:
      data-dropzone="true"
      data-input="<file-input-id>"
      data-preview="<img-element-id>"
      data-placeholder="<placeholder-div-id>"  (optional)
--}}
<script>
(function () {
    function initDropzones() {
        document.querySelectorAll('[data-dropzone="true"]').forEach(function (zone) {
            const inputId   = zone.dataset.input;
            const previewId = zone.dataset.preview;
            const phId      = zone.dataset.placeholder;
            const input     = document.getElementById(inputId);
            const preview   = document.getElementById(previewId);
            const placeholder = phId ? document.getElementById(phId) : null;

            if (!input || !preview) return;

            // Click to open the file picker
            zone.addEventListener('click', function () { input.click(); });

            // Drag visual feedback
            zone.addEventListener('dragover', function (e) {
                e.preventDefault();
                zone.classList.add('border-[#9e2016]', 'bg-red-50', 'scale-[1.01]');
            });
            zone.addEventListener('dragleave', function (e) {
                zone.classList.remove('border-[#9e2016]', 'bg-red-50', 'scale-[1.01]');
            });
            zone.addEventListener('drop', function (e) {
                e.preventDefault();
                zone.classList.remove('border-[#9e2016]', 'bg-red-50', 'scale-[1.01]');
                const files = e.dataTransfer.files;
                if (files && files[0]) {
                    // Assign files to the actual input (use DataTransfer trick)
                    const dt = new DataTransfer();
                    dt.items.add(files[0]);
                    input.files = dt.files;
                    showPreview(files[0], preview, placeholder);
                }
            });

            // On file picked via dialog
            input.addEventListener('change', function () {
                if (input.files && input.files[0]) {
                    showPreview(input.files[0], preview, placeholder);
                }
            });
        });
    }

    function showPreview(file, preview, placeholder) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDropzones);
    } else {
        initDropzones();
    }
})();
</script>
