@extends('admin.layouts.app')
@section('title', 'Edit Review')
@section('page-title', 'Reviews')
@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-edit me-2"></i> Edit Review
                    </h5>
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form action="{{ route('admin.reviews.update', $review->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="name" class="form-label fw-semibold">
                                        Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           value="{{ old('name', $review->name) }}"
                                           class="form-control form-control-lg @error('name') is-invalid @enderror"
                                           placeholder="e.g., John Doe"
                                           required
                                           autofocus>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="designation" class="form-label fw-semibold">
                                        Location / Designation
                                    </label>
                                    <input type="text" 
                                           name="designation" 
                                           id="designation" 
                                           value="{{ old('designation', $review->designation) }}"
                                           class="form-control form-control-lg @error('designation') is-invalid @enderror"
                                           placeholder="e.g., CEO, Example Co.">
                                    @error('designation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="rating_point" class="form-label fw-semibold">
                                        Rating <span class="text-danger">*</span>
                                    </label>
                                    <select name="rating_point" 
                                            id="rating_point"
                                            class="form-select form-control-lg @error('rating_point') is-invalid @enderror"
                                            required>
                                        @for($i = 5; $i >= 1; $i--)
                                            <option value="{{ $i }}" {{ old('rating_point', $review->rating_point) == $i ? 'selected' : '' }}>
                                                {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('rating_point')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="photo" class="form-label fw-semibold">Photo</label>
                                    
                                    @if($review->photo)
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="{{ asset('uploads/reviews/' . $review->photo) }}" 
                                                     alt="{{ $review->name }}" 
                                                     class="rounded-circle border" 
                                                     style="width: 80px; height: 80px; object-fit: cover;">
                                                <div>
                                                    <span class="badge bg-success">Current Photo</span>
                                                    <p class="text-muted small mb-0 mt-1">{{ basename($review->photo) }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <input type="file" 
                                           name="photo" 
                                           id="photo" 
                                           class="form-control form-control-lg @error('photo') is-invalid @enderror"
                                           accept="image/*">
                                    <div class="form-text text-muted">
                                        <i class="fas fa-info-circle me-1"></i> 
                                        JPG, PNG or WEBP. Max 5MB. Leave empty to keep current photo.
                                    </div>
                                    @error('photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div id="photoPreviewContainer" class="mt-3 d-none">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="" alt="Preview" id="photoPreview" 
                                                 class="rounded-circle border" 
                                                 style="width: 80px; height: 80px; object-fit: cover;">
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearPhotoPreview()">
                                                <i class="fas fa-times"></i> Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="message" class="form-label fw-semibold">
                                Message <span class="text-danger">*</span>
                            </label>
                            <textarea name="message" 
                                      id="message" 
                                      rows="4" 
                                      class="form-control form-control-lg @error('message') is-invalid @enderror"
                                      placeholder="Write the review message...">{{ old('message', $review->message) }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input type="checkbox" 
                                       name="active" 
                                       id="active" 
                                       value="1" 
                                       {{ old('active', $review->active) ? 'checked' : '' }}
                                       class="form-check-input">
                                <label for="active" class="form-check-label fw-semibold">
                                    Active
                                </label>
                            </div>
                            <div class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i> Inactive reviews won't be displayed on the website.
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i> Update Review
                            </button>
                            <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary px-4">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('photo')?.addEventListener('change', function(e) {
        const container = document.getElementById('photoPreviewContainer');
        const preview = document.getElementById('photoPreview');
        const file = e.target.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                container.classList.remove('d-none');
            }
            reader.readAsDataURL(file);
        } else {
            container.classList.add('d-none');
        }
    });

    function clearPhotoPreview() {
        const container = document.getElementById('photoPreviewContainer');
        const preview = document.getElementById('photoPreview');
        const input = document.getElementById('photo');
        
        container.classList.add('d-none');
        preview.src = '';
        input.value = '';
    }
</script>
@endpush
@endsection