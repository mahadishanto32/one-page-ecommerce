<script>
(function () {
    // ----- Generic repeater row (specification / size chart, if used as a manual key/value builder) -----
    function addRow(wrapperId, rowClass, keyName, valueName, keyPlaceholder, valuePlaceholder) {
        const wrapper = document.getElementById(wrapperId);
        if (!wrapper) return;
        const row = document.createElement('div');
        row.className = `row g-2 mb-2 repeater-row ${rowClass}`;
        row.innerHTML = `
            <div class="col-md-5">
                <input type="text" name="${keyName}[]" class="form-control" placeholder="${keyPlaceholder}">
            </div>
            <div class="col-md-6">
                <input type="text" name="${valueName}[]" class="form-control" placeholder="${valuePlaceholder}">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn-icon-admin danger remove-row"><i class="fa-solid fa-xmark"></i></button>
            </div>
        `;
        wrapper.appendChild(row);
    }

    document.getElementById('add-specification')?.addEventListener('click', function () {
        addRow('specification-wrapper', 'specification-row', 'specification_key', 'specification_value', 'e.g. Material', 'e.g. Cotton');
    });

    document.getElementById('add-size-chart')?.addEventListener('click', function () {
        addRow('size-chart-wrapper', 'size-chart-row', 'size_chart_key', 'size_chart_value', 'e.g. Size M', 'e.g. Chest 40in');
    });

    document.addEventListener('click', function (e) {
        if (e.target.closest('.remove-row')) {
            const row = e.target.closest('.specification-row, .size-chart-row');
            const wrapper = row?.parentElement;
            // keep at least one row in each wrapper
            if (wrapper && wrapper.children.length > 1) {
                row.remove();
            } else if (row) {
                row.querySelectorAll('input').forEach(input => input.value = '');
            }
        }
    });

    // ----- Remove existing gallery image (edit screen) -----
    document.querySelectorAll('.remove-gallery-image').forEach(function (button) {
        button.addEventListener('click', function () {
            if (!confirm('Remove this gallery image?')) {
                return;
            }

            const productId = this.dataset.productId;
            const filename = this.dataset.filename;
            const wrapperEl = this.closest('[data-filename]');

            fetch(`{{ url('admin/products') }}/${productId}/gallery-image`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ filename: filename })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        wrapperEl.remove();
                    }
                })
                .catch(() => {});
        });
    });
})();
</script>