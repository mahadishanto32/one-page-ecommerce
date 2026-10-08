@extends('admin.layouts.app')
@section('title', 'Edit Attribute Option')
@section('page-title', 'Edit Attribute Option')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-edit me-2"></i> Edit Option: {{ $option->name }}
                    </h5>
                    <a href="{{ route('admin.attribute-options.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.attribute-options.update', $option->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="name" class="form-label fw-semibold">
                                        Option Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           value="{{ old('name', $option->name) }}"
                                           class="form-control form-control-lg @error('name') is-invalid @enderror"
                                           placeholder="e.g., Red, Large, Cotton"
                                           autofocus>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="attribute_id" class="form-label fw-semibold">
                                        Attribute <span class="text-danger">*</span>
                                    </label>
                                    <select name="attribute_id" 
                                            id="attribute_id"
                                            class="form-select form-control-lg @error('attribute_id') is-invalid @enderror">
                                        <option value="">Select Attribute</option>
                                        @foreach($attributes as $attribute)
                                            <option value="{{ $attribute->id }}" {{ old('attribute_id', $option->attribute_id) == $attribute->id ? 'selected' : '' }}>
                                                {{ $attribute->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('attribute_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input type="checkbox" 
                                       name="status" 
                                       id="status" 
                                       value="1" 
                                       {{ old('status', $option->status) ? 'checked' : '' }}
                                       class="form-check-input">
                                <label for="status" class="form-check-label fw-semibold">
                                    Active
                                </label>
                            </div>
                            <div class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i> Inactive options won't be displayed to customers.
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i> Update Option
                            </button>
                            <a href="{{ route('admin.attribute-options.index') }}" class="btn btn-secondary px-4">
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