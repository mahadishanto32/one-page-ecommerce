@extends('admin.layouts.app')
@section('title', 'Create Product Attribute')
@section('page-title', 'Create Product Attribute')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-plus-circle me-2"></i> Create New Attribute
                    </h5>
                    <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.attributes.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="form-label fw-semibold">
                                Attribute Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name') }}"
                                   class="form-control form-control-lg @error('name') is-invalid @enderror"
                                   placeholder="e.g., Color, Size, Material"
                                   autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i> This will be used to generate the slug automatically.
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input type="checkbox" 
                                       name="status" 
                                       id="status" 
                                       value="1" 
                                       {{ old('status', true) ? 'checked' : '' }}
                                       class="form-check-input">
                                <label for="status" class="form-check-label fw-semibold">
                                    Active
                                </label>
                            </div>
                            <div class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i> Inactive attributes won't be displayed to customers.
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i> Save Attribute
                            </button>
                            <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary px-4">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection