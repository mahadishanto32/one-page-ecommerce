@extends('admin.layouts.app')

@section('title', 'Edit Home Slider')
@section('page-title', 'Edit Home Slider')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-edit me-2"></i> Edit Home Slider
                    </h5>
                    <a href="{{ route('admin.homesliders.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.homesliders.update', $homeslider) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="name" class="form-label fw-semibold">
                                        Name <span class="text-muted">(Optional)</span>
                                    </label>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           value="{{ old('name', $homeslider->name) }}"
                                           class="form-control form-control-lg @error('name') is-invalid @enderror"
                                           placeholder="Enter slider name"
                                           autofocus>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="slider_type" class="form-label fw-semibold">
                                        Slider Type <span class="text-danger">*</span>
                                    </label>
                                    <select name="slider_type" 
                                            id="slider_type"
                                            class="form-select form-control-lg @error('slider_type') is-invalid @enderror">
                                        <option value="image" {{ old('slider_type', $homeslider->slider_type) == 'image' ? 'selected' : '' }}>Image</option>
                                        <option value="video" {{ old('slider_type', $homeslider->slider_type) == 'video' ? 'selected' : '' }}>Video</option>
                                    </select>
                                    @error('slider_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Current File Display -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Current File</label>
                            <div class="border rounded p-3 bg-light">
                                @if($homeslider->slider_type == 'image')
                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                        <img src="{{ asset($homeslider->file_path) }}" 
                                             alt="Current Image" 
                                             class="rounded border"
                                             style="max-height: 200px; width: auto;">
                                        <div>
                                            <span class="badge bg-success">Current Image</span>
                                            <p class="text-muted small mb-0 mt-1">
                                                <i class="fas fa-file me-1"></i> {{ basename($homeslider->file_path) }}
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                        <video src="{{ asset($homeslider->file_path) }}" 
                                               controls 
                                               class="rounded border"
                                               style="max-height: 200px; width: auto;"></video>
                                        <div>
                                            <span class="badge bg-info">Current Video</span>
                                            <p class="text-muted small mb-0 mt-1">
                                                <i class="fas fa-file me-1"></i> {{ basename($homeslider->file_path) }}
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="file" class="form-label fw-semibold">
                                        Change File <span class="text-muted">(Optional)</span>
                                    </label>
                                    <input type="file" 
                                           name="file" 
                                           id="file" 
                                           accept="image/*,video/*"
                                           class="form-control form-control-lg @error('file') is-invalid @enderror">
                                    <div class="form-text text-muted">
                                        <i class="fas fa-info-circle me-1"></i> 
                                        Accepted formats: Images (jpg, png, gif, webp) or Videos (mp4, webm). Max size: 50MB
                                    </div>
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="serial" class="form-label fw-semibold">
                                        Serial <span class="text-muted">(Optional)</span>
                                    </label>
                                    <input type="number" 
                                           name="serial" 
                                           id="serial" 
                                           value="{{ old('serial', $homeslider->serial) }}"
                                           class="form-control form-control-lg @error('serial') is-invalid @enderror"
                                           placeholder="Enter serial number"
                                           min="0">
                                    @error('serial')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text text-muted">
                                        <i class="fas fa-info-circle me-1"></i> Lower numbers appear first
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- File Preview -->
                        <div class="mb-4">
                            <div id="filePreview" class="d-none">
                                <label class="form-label fw-semibold">New File Preview:</label>
                                <div class="border rounded p-3 bg-light" id="previewContainer">
                                    <div class="text-center text-muted py-3">
                                        <i class="fas fa-image fa-2x d-block mb-2"></i>
                                        <span>Preview will appear here</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input type="checkbox" 
                                       name="status" 
                                       id="status" 
                                       value="1" 
                                       {{ old('status', $homeslider->status) ? 'checked' : '' }}
                                       class="form-check-input">
                                <label for="status" class="form-check-label fw-semibold">
                                    Active
                                </label>
                            </div>
                            <div class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i> Inactive sliders won't be displayed on the homepage.
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i> Update Slider
                            </button>
                            <a href="{{ route('admin.homesliders.index') }}" class="btn btn-secondary px-4">
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
    document.getElementById('file').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const previewDiv = document.getElementById('filePreview');
        const container = document.getElementById('previewContainer');

        if (file) {
            previewDiv.classList.remove('d-none');
            const reader = new FileReader();

            reader.onload = function(event) {
                if (file.type.startsWith('image/')) {
                    container.innerHTML = `<img src="${event.target.result}" alt="Preview" class="img-fluid rounded" style="max-height: 300px; width: auto;">`;
                } else if (file.type.startsWith('video/')) {
                    container.innerHTML = `<video src="${event.target.result}" controls class="img-fluid rounded" style="max-height: 300px; width: auto;"></video>`;
                } else {
                    container.innerHTML = `
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-file fa-2x d-block mb-2"></i>
                            <span>Preview not available for this file type</span>
                        </div>
                    `;
                }
            };

            reader.readAsDataURL(file);
        } else {
            previewDiv.classList.add('d-none');
            container.innerHTML = `
                <div class="text-center text-muted py-3">
                    <i class="fas fa-image fa-2x d-block mb-2"></i>
                    <span>Preview will appear here</span>
                </div>
            `;
        }
    });
</script>
@endpush
@endsection