@extends('admin.layouts.app')
@section('title', 'Add Product Category')
@section('page-title', 'Add Product Category')

@section('content')
<div class="admin-card">
    <div class="admin-card-header">
        <h3><i class="fa-solid fa-plus"></i> Add New Product Category</h3>
        <a href="{{ route('admin.product-categories.index') }}" class="btn-admin btn-admin-outline">
            <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="admin-card-body">

        @if($errors->any())
            <div class="admin-alert admin-alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.product-categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="product-form-section">
                <div class="product-form-section-header">
                    <div class="section-icon"><i class="fa-solid fa-circle-info"></i></div>
                    <div>
                        <h5>Category Details</h5>
                        <div class="section-subtitle">Basic information about the product category.</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Enter category name" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="serial" class="form-label">Serial</label>
                        <input type="number" name="serial" id="serial" value="{{ old('serial', 0) }}"
                               class="form-control @error('serial') is-invalid @enderror"
                               placeholder="Display order">
                        @error('serial')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="product-form-section media-upload-box">
                <div class="product-form-section-header">
                    <div class="section-icon"><i class="fa-solid fa-image"></i></div>
                    <div>
                        <h5>Logo / Image</h5>
                        <div class="section-subtitle">Recommended formats: JPG, PNG, WEBP, SVG. Max size 5MB.</div>
                    </div>
                </div>

                <input type="file" name="image" id="image" accept="image/*"
                       class="form-control @error('image') is-invalid @enderror">
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="product-form-section">
                <div class="product-form-section-header">
                    <div class="section-icon"><i class="fa-solid fa-toggle-on"></i></div>
                    <div>
                        <h5>Visibility</h5>
                        <div class="section-subtitle">Control whether this category is active on the storefront.</div>
                    </div>
                </div>

                <label class="toggle-card" style="max-width:280px;">
                    <input type="checkbox" name="status" id="status" value="1"
                           class="form-check-input" {{ old('status', 1) ? 'checked' : '' }}>
                    <span>
                        <span class="toggle-card-title">Active</span>
                        <span class="toggle-card-subtitle">Visible to customers</span>
                    </span>
                </label>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn-admin btn-admin-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Save Category
                </button>
                <a href="{{ route('admin.product-categories.index') }}" class="btn-admin btn-admin-outline">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection