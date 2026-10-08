@extends('admin.layouts.app')
@section('title', 'Product')
@section('page-title', 'Product')
@section('content')

<div class="admin-card">
    <div class="admin-card-header">
        <h3><i class="fa-solid fa-box-open"></i> Add New Product</h3>
        <a href="{{ route('admin.products.index') }}" class="btn-admin btn-admin-outline">
            <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="admin-card-body">
        @if ($errors->any())
            <div class="admin-alert admin-alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.products.form')

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn-admin btn-admin-primary">
                    <i class="fa-solid fa-check"></i> Save Product
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn-admin btn-admin-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

@include('admin.products.scripts')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const attributeSelect = document.getElementById('product_attribute_id');
    const optionSelect = document.getElementById('product_attribute_option_id');

    if (attributeSelect && optionSelect) {
        attributeSelect.addEventListener('change', function() {
            const attributeId = this.value;

            // Clear current options
            optionSelect.innerHTML = '<option value="">-- Select Attribute Option --</option>';

            if (attributeId) {
                // Fetch options via AJAX
                fetch(`/admin/get-attribute-options?attribute_id=${attributeId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(option => {
                            const opt = document.createElement('option');
                            opt.value = option.id;
                            let text = option.name;
                            if (option.added_price) {
                                text += ` (+$${parseFloat(option.added_price).toFixed(2)})`;
                            }
                            opt.textContent = text;
                            optionSelect.appendChild(opt);
                        });
                    })
                    .catch(error => console.error('Error fetching options:', error));
            }
        });
    }
});
</script>
@endpush