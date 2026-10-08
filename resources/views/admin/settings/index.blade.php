@extends('admin.layouts.app')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --shadow-sm: 0 2px 8px rgba(0,0,0,0.06);
        --shadow-md: 0 4px 20px rgba(0,0,0,0.08);
        --shadow-lg: 0 8px 40px rgba(0,0,0,0.12);
        --radius: 16px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .user-settings-card {
        background: #f8f9fa;
        border-radius: var(--radius);
        padding: 20px;
        margin-top: 20px;
        border: 1px solid #e9ecef;
    }
    
    .user-settings-card .form-section-title {
        margin-top: 0;
    }

    .settings-wrapper {
    padding: 1.5rem 0;
}

/* Main Card */
.settings-main-card {
    background: #ffffff;
    border-radius: var(--radius);
    box-shadow: var(--shadow-md);
    border: none;
    overflow: hidden;
    transition: var(--transition);
}

.settings-main-card:hover {
    box-shadow: var(--shadow-lg);
}

/* Card Header */
.settings-header {
    background: var(--primary-gradient);
    padding: 1.5rem 2rem;
    border-bottom: none;
    position: relative;
    overflow: hidden;
}

.settings-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: rgba(255,255,255,0.05);
    border-radius: 50%;
    pointer-events: none;
}

.settings-header::after {
    content: '';
    position: absolute;
    bottom: -60%;
    left: 20%;
    width: 200px;
    height: 200px;
    background: rgba(255,255,255,0.03);
    border-radius: 50%;
    pointer-events: none;
}

.settings-header h3 {
    color: #ffffff;
    font-weight: 600;
    font-size: 1.5rem;
    margin: 0;
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 12px;
}

.settings-header h3 i {
    font-size: 1.6rem;
    opacity: 0.9;
}

.settings-header .btn-reset {
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    color: #ffffff;
    border-radius: 50px;
    padding: 0.5rem 1.5rem;
    font-weight: 500;
    transition: var(--transition);
    position: relative;
    z-index: 1;
}

.settings-header .btn-reset:hover {
    background: rgba(255,255,255,0.25);
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

/* Card Body */
.settings-body {
    padding: 2rem;
}

/* Section Titles */
.form-section-title {
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
    margin-bottom: 1.25rem;
    padding-bottom: 0.6rem;
    border-bottom: 2px solid #f1f3f5;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-section-title i {
    color: #667eea;
    font-size: 1rem;
    width: 24px;
    text-align: center;
}

.form-section-title:not(:first-child) {
    margin-top: 2rem;
}

/* Form Groups */
.form-group {
    margin-bottom: 1.25rem;
}

.form-group label {
    font-weight: 600;
    font-size: 0.9rem;
    color: #2d3748;
    margin-bottom: 0.4rem;
    display: flex;
    align-items: center;
    gap: 6px;
}

.form-group label .text-danger {
    font-weight: 700;
}

.form-control {
    border: 2px solid #edf2f7;
    border-radius: 10px;
    padding: 0.6rem 1rem;
    font-size: 0.95rem;
    transition: var(--transition);
    background: #fafbfc;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
    background: #ffffff;
}

.form-control.is-invalid {
    border-color: #fc8181;
    box-shadow: 0 0 0 4px rgba(252, 129, 129, 0.12);
}

.form-control.is-invalid:focus {
    border-color: #fc8181;
    box-shadow: 0 0 0 4px rgba(252, 129, 129, 0.2);
}

textarea.form-control {
    resize: vertical;
    min-height: 80px;
}

/* Custom File Input */
.custom-file {
    position: relative;
    display: inline-block;
    width: 100%;
    height: calc(2.25rem + 2px);
    margin-bottom: 0;
}

.custom-file-input {
    position: relative;
    z-index: 2;
    width: 100%;
    height: calc(2.25rem + 2px);
    margin: 0;
    opacity: 0;
    cursor: pointer;
}

.custom-file-label {
    position: absolute;
    top: 0;
    right: 0;
    left: 0;
    z-index: 1;
    height: calc(2.25rem + 2px);
    padding: 0.6rem 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #4a5568;
    background-color: #fafbfc;
    border: 2px solid #edf2f7;
    border-radius: 10px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    transition: var(--transition);
    display: flex;
    align-items: center;
}

.custom-file-label::after {
    content: "Browse";
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    z-index: 3;
    display: flex;
    align-items: center;
    padding: 0 1.2rem;
    color: #ffffff;
    background: var(--primary-gradient);
    border-radius: 0 8px 8px 0;
    font-weight: 500;
    font-size: 0.85rem;
}

.custom-file-input:focus ~ .custom-file-label {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
}

.custom-file-input:focus ~ .custom-file-label::after {
    box-shadow: inset 0 0 0 2px rgba(255,255,255,0.3);
}

/* Social Media Inputs */
.social-link-group {
    position: relative;
}

.social-link-group .social-icon-prefix {
    position: absolute;
    left: 14px;
    top: 38px;
    font-size: 1.2rem;
    z-index: 4;
    opacity: 0.7;
    transition: var(--transition);
}

.social-link-group .form-control {
    padding-left: 44px !important;
}

.social-link-group .form-control:focus ~ .social-icon-prefix {
    opacity: 1;
    transform: scale(1.1);
}

/* Current Image Display */
.current-image-container {
    margin-top: 0.75rem;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 10px;
    border: 1px dashed #e2e8f0;
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}

.current-image-container label {
    margin: 0;
    font-weight: 600;
    font-size: 0.85rem;
    color: #4a5568;
}

.current-image-container img {
    border-radius: 8px;
    border: 2px solid #e2e8f0;
    padding: 4px;
    background: #ffffff;
    max-height: 80px;
    width: auto;
    object-fit: contain;
}

.default-image-info {
    margin-top: 0.5rem;
    padding: 0.5rem 1rem;
    background: #f0f4ff;
    border-radius: 8px;
    color: #667eea;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.default-image-info i {
    font-size: 1rem;
}

/* Alerts */
.alert {
    border: none;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    font-weight: 500;
    box-shadow: var(--shadow-sm);
}

.alert-success {
    background: #f0fff4;
    color: #276749;
    border-left: 4px solid #48bb78;
}

.alert-danger {
    background: #fff5f5;
    color: #9b2c2c;
    border-left: 4px solid #fc8181;
}

.alert ul {
    padding-left: 1.5rem;
    margin-top: 0.3rem;
}

.alert ul li {
    list-style-type: none;
    position: relative;
    padding-left: 0.5rem;
}

.alert ul li::before {
    content: '•';
    position: absolute;
    left: -1rem;
    color: #fc8181;
    font-weight: 700;
}

/* Buttons */
.btn {
    border-radius: 50px;
    padding: 0.6rem 2rem;
    font-weight: 600;
    font-size: 0.9rem;
    transition: var(--transition);
    letter-spacing: 0.3px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: var(--primary-gradient);
    border: none;
    color: #ffffff;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.35);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(102, 126, 234, 0.45);
    color: #ffffff;
}

.btn-primary:disabled {
    opacity: 0.7;
    transform: none;
    box-shadow: none;
}

.btn-secondary {
    background: #edf2f7;
    border: none;
    color: #4a5568;
}

.btn-secondary:hover {
    background: #e2e8f0;
    color: #2d3748;
    transform: translateY(-2px);
}

.btn-danger {
    background: #fc8181;
    border: none;
    color: #ffffff;
    box-shadow: 0 4px 15px rgba(252, 129, 129, 0.3);
}

.btn-danger:hover {
    background: #f56565;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(252, 129, 129, 0.4);
}

/* Modal */
.modal-content {
    border: none;
    border-radius: var(--radius);
    box-shadow: var(--shadow-lg);
    overflow: hidden;
}

.modal-header {
    background: var(--primary-gradient);
    color: #ffffff;
    border-bottom: none;
    padding: 1.25rem 1.5rem;
}

.modal-header .modal-title {
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.modal-header .close {
    color: #ffffff;
    opacity: 0.8;
    text-shadow: none;
    font-size: 1.8rem;
    padding: 0;
    margin: -1rem -0.5rem -1rem auto;
}

.modal-header .close:hover {
    opacity: 1;
    color: #ffffff;
}

.modal-body {
    padding: 1.5rem;
}

.modal-body .reset-warning-list {
    background: #fff5f5;
    border-radius: 10px;
    padding: 1rem 1.5rem;
    margin-top: 0.75rem;
}

.modal-body .reset-warning-list li {
    padding: 0.3rem 0;
    list-style-type: none;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #9b2c2c;
}

.modal-body .reset-warning-list li i {
    color: #fc8181;
    width: 20px;
    text-align: center;
}

.modal-footer {
    border-top: 1px solid #edf2f7;
    padding: 1rem 1.5rem;
}

/* Small text */
.form-text {
    font-size: 0.8rem;
    color: #a0aec0;
    margin-top: 0.4rem;
    display: flex;
    align-items: center;
    gap: 6px;
}

.form-text i {
    font-size: 0.9rem;
    color: #667eea;
}

/* Action row */
.action-row {
    padding-top: 1.5rem;
    border-top: 2px solid #f1f3f5;
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

/* Responsive */
@media (max-width: 768px) {
    .settings-header {
        padding: 1.25rem;
    }

    .settings-header h3 {
        font-size: 1.25rem;
    }

    .settings-body {
        padding: 1.25rem;
    }

    .settings-header .d-flex {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 12px;
    }

    .settings-header .d-flex .btn-reset {
        width: 100%;
        text-align: center;
    }

    .action-row {
        flex-direction: column;
    }

    .action-row .btn {
        width: 100%;
        justify-content: center;
    }

    .current-image-container {
        flex-direction: column;
        align-items: flex-start;
    }
}

@media (max-width: 576px) {
    .settings-body {
        padding: 1rem;
    }

    .modal-dialog {
        margin: 1rem;
    }
}

/* Loading spinner animation */
@keyframes pulse-dot {
    0%, 100% { opacity: 0.4; }
    50% { opacity: 1; }
}

.btn-loading .spinner-border {
    width: 1rem;
    height: 1rem;
    border-width: 0.15rem;
}

</style>

<div class="settings-wrapper">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-11 col-xxl-10">
            <div class="settings-main-card">

                <!-- Header -->
                <div class="settings-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <h3>
                            <i class="fa-solid fa-gear fa-spin" style="--fa-animation-duration: 8s;"></i>
                            Website Settings
                        </h3>
                        <button type="button" class="btn-reset" data-toggle="modal" data-target="#resetModal">
                            <i class="fa-solid fa-rotate"></i> Reset to Default
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div class="settings-body">

                    <!-- Alerts -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-exclamation-circle"></i> Please fix the following errors:
                            <ul class="mb-0 mt-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Website Settings Form -->
                    <form action="{{ route('admin.settings.update', $settings->id ?? 1) }}" method="POST" enctype="multipart/form-data" id="settingsForm">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">

                            <!-- LEFT COLUMN -->
                            <div class="col-lg-6">

                                <!-- Basic Information -->
                                <div class="form-section-title">
                                    <i class="fa-solid fa-info-circle"></i> Basic Information
                                </div>

                                <div class="form-group">
                                    <label for="app_name">Application Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('app_name') is-invalid @enderror"
                                           id="app_name" name="app_name"
                                           value="{{ old('app_name', $settings->app_name ?? '') }}"
                                           placeholder="Enter application name" required>
                                    @error('app_name')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="phone_number">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                                           id="phone_number" name="phone_number"
                                           value="{{ old('phone_number', $settings->phone_number ?? '') }}"
                                           placeholder="Enter phone number" required>
                                    @error('phone_number')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="whatsapp_number">WhatsApp Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('whatsapp_number') is-invalid @enderror"
                                           id="whatsapp_number" name="whatsapp_number"
                                           value="{{ old('whatsapp_number', $settings->whatsapp_number ?? '') }}"
                                           placeholder="Enter WhatsApp number" required>
                                    @error('whatsapp_number')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="email">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email"
                                           value="{{ old('email', $settings->email ?? '') }}"
                                           placeholder="Enter email address" required>
                                    @error('email')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Address Information -->
                                <div class="form-section-title">
                                    <i class="fa-solid fa-location-dot"></i> Address Information
                                </div>

                                <div class="form-group">
                                    <label for="address">Address <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('address') is-invalid @enderror"
                                              id="address" name="address" rows="3"
                                              placeholder="Enter address" required>{{ old('address', $settings->address ?? '') }}</textarea>
                                    @error('address')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="alternative_address">Alternative Address</label>
                                    <textarea class="form-control @error('alternative_address') is-invalid @enderror"
                                              id="alternative_address" name="alternative_address" rows="2"
                                              placeholder="Enter alternative address">{{ old('alternative_address', $settings->alternative_address ?? '') }}</textarea>
                                    @error('alternative_address')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Map Settings -->
                                <div class="form-section-title">
                                    <i class="fa-solid fa-map"></i> Map Settings
                                </div>

                                <div class="form-group">
                                    <label for="google_map_link">Google Map Embed Link</label>
                                    <textarea class="form-control @error('google_map_link') is-invalid @enderror"
                                              id="google_map_link" name="google_map_link" rows="2"
                                              placeholder="Paste Google Map embed link">{{ old('google_map_link', $settings->google_map_link ?? '') }}</textarea>
                                    @error('google_map_link')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text">
                                        <i class="fa-solid fa-circle-info"></i>
                                        Go to Google Maps, click Share, then Embed Map, and paste the iframe src URL
                                    </small>
                                </div>
                            </div>

                            <!-- RIGHT COLUMN -->
                            <div class="col-lg-6">

                                <!-- Images -->
                                <div class="form-section-title">
                                    <i class="fa-solid fa-image"></i> Images
                                </div>

                                <!-- Logo -->
                                <div class="form-group">
                                    <label for="logo">Logo</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('logo') is-invalid @enderror"
                                               id="logo" name="logo" accept="image/*">
                                        <label class="custom-file-label" for="logo">Choose logo file</label>
                                    </div>
                                    @error('logo')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text">
                                        <i class="fa-solid fa-circle-info"></i> Max size: 5MB. Allowed: jpeg, png, jpg, gif, svg, webp
                                    </small>
                                    @if($settings->logo && $settings->logo != 'default-logo.png')
                                        <div class="current-image-container">
                                            <label>Current Logo:</label>
                                            <div>
                                                <img src="{{ asset('uploads/settings/' . $settings->logo) }}"
                                                     alt="Logo" style="max-height: 80px; width: auto;">
                                            </div>
                                        </div>
                                    @else
                                        <div class="default-image-info">
                                            <i class="fa-solid fa-info-circle"></i> Using default logo
                                        </div>
                                    @endif
                                </div>

                                <!-- Favicon -->
                                <div class="form-group">
                                    <label for="favicon">Favicon</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('favicon') is-invalid @enderror"
                                               id="favicon" name="favicon" accept="image/*">
                                        <label class="custom-file-label" for="favicon">Choose favicon file</label>
                                    </div>
                                    @error('favicon')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text">
                                        <i class="fa-solid fa-circle-info"></i> Max size: 1MB. Allowed: jpeg, png, jpg, gif, svg, ico, webp
                                    </small>
                                    @if($settings->favicon && $settings->favicon != 'default-favicon.ico')
                                        <div class="current-image-container">
                                            <label>Current Favicon:</label>
                                            <div>
                                                <img src="{{ asset('uploads/settings/' . $settings->favicon) }}"
                                                     alt="Favicon" style="max-height: 40px; width: auto;">
                                            </div>
                                        </div>
                                    @else
                                        <div class="default-image-info">
                                            <i class="fa-solid fa-info-circle"></i> Using default favicon
                                        </div>
                                    @endif
                                </div>

                                <!-- Social Media -->
                                <div class="form-section-title">
                                    <i class="fa-solid fa-share-nodes"></i> Social Media Links
                                </div>

                                <div class="form-group social-link-group">
                                    <label for="facebook_link">Facebook Link</label>
                                    <i class="fa-brands fa-facebook social-icon-prefix" style="color: #1877f2;"></i>
                                    <input type="url" class="form-control @error('facebook_link') is-invalid @enderror"
                                           id="facebook_link" name="facebook_link"
                                           value="{{ old('facebook_link', $settings->facebook_link ?? '') }}"
                                           placeholder="https://facebook.com/yourpage" style="padding-left: 40px;">
                                    @error('facebook_link')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group social-link-group">
                                    <label for="instagram_link">Instagram Link</label>
                                    <i class="fa-brands fa-instagram social-icon-prefix" style="color: #e4405f;"></i>
                                    <input type="url" class="form-control @error('instagram_link') is-invalid @enderror"
                                           id="instagram_link" name="instagram_link"
                                           value="{{ old('instagram_link', $settings->instagram_link ?? '') }}"
                                           placeholder="https://instagram.com/yourprofile" style="padding-left: 40px;">
                                    @error('instagram_link')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group social-link-group">
                                    <label for="youtube_link">YouTube Link</label>
                                    <i class="fa-brands fa-youtube social-icon-prefix" style="color: #ff0000;"></i>
                                    <input type="url" class="form-control @error('youtube_link') is-invalid @enderror"
                                           id="youtube_link" name="youtube_link"
                                           value="{{ old('youtube_link', $settings->youtube_link ?? '') }}"
                                           placeholder="https://youtube.com/@yourchannel" style="padding-left: 40px;">
                                    @error('youtube_link')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group social-link-group">
                                    <label for="x_id">X (Twitter) ID</label>
                                    <i class="fa-brands fa-x-twitter social-icon-prefix" style="color: #000000;"></i>
                                    <input type="text" class="form-control @error('x_id') is-invalid @enderror"
                                           id="x_id" name="x_id"
                                           value="{{ old('x_id', $settings->x_id ?? '') }}"
                                           placeholder="@yourhandle" style="padding-left: 40px;">
                                    @error('x_id')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>                               
                            </div>
                        </div>

                        <!-- Submit Row for Website Settings -->
                        <div class="action-row">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fa-solid fa-save"></i> Save Settings
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                                <i class="fa-solid fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>

                    <!-- User Settings Section -->
                    <div class="user-settings-card">
                        <h4 class="mb-3">
                            <i class="fa-solid fa-user-gear"></i> User Settings
                        </h4>
                        <p class="text-muted mb-3">Update your account name and password</p>

                        <form action="{{ route('admin.settings.update-user') }}" method="POST" id="userSettingsForm">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="user_name">User Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('user_name') is-invalid @enderror"
                                               id="user_name" name="user_name"
                                               value="{{ old('user_name', $user->name ?? '') }}"
                                               placeholder="Enter user name" required>
                                        @error('user_name')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <hr>
                                    <h6 class="mb-3 text-muted">
                                        <i class="fa-solid fa-lock"></i> Change Password (Leave blank to keep current)
                                    </h6>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="current_password">Current Password</label>
                                        <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                               id="current_password" name="current_password"
                                               placeholder="Enter current password">
                                        @error('current_password')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="new_password">New Password</label>
                                        <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                               id="new_password" name="new_password"
                                               placeholder="Enter new password">
                                        @error('new_password')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="new_password_confirmation">Confirm New Password</label>
                                        <input type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror"
                                               id="new_password_confirmation" name="new_password_confirmation"
                                               placeholder="Confirm new password">
                                        @error('new_password_confirmation')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <small class="form-text text-muted">
                                            <i class="fa-solid fa-info-circle"></i> Password must be at least 8 characters
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success" id="userSubmitBtn">
                                        <i class="fa-solid fa-user-pen"></i> Update User Settings
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reset Modal -->
<div class="modal fade" id="resetModal" tabindex="-1" role="dialog" aria-labelledby="resetModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetModalLabel">
                    <i class="fa-solid fa-exclamation-triangle"></i> Reset Settings
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to reset all settings to default values?</p>
                <p class="text-danger"><strong>Warning:</strong> This action cannot be undone!</p>
                <ul class="reset-warning-list">
                    <li><i class="fa-solid fa-circle-xmark"></i> All custom logo and favicon image will be deleted</li>
                    <li><i class="fa-solid fa-circle-xmark"></i> All social media links will be cleared</li>
                    <li><i class="fa-solid fa-circle-xmark"></i> Contact information will be reset</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa-solid fa-times"></i> Cancel
                </button>
                <form action="{{ route('admin.settings.reset', $settings->id ?? 1) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-rotate"></i> Reset All Settings
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {

        // Custom file input label update
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop() || 'Choose file';
            $(this).next('.custom-file-label').html(fileName);
        });

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow', function() {
                $(this).alert('close');
            });
        }, 5000);

        // Form submit loading state for website settings
        $('#settingsForm').on('submit', function() {
            var btn = $('#submitBtn');
            btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
            btn.prop('disabled', true);
            btn.addClass('btn-loading');
        });

        // Form submit loading state for user settings
        $('#userSettingsForm').on('submit', function() {
            var btn = $('#userSubmitBtn');
            btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');
            btn.prop('disabled', true);
            btn.addClass('btn-loading');
        });

        // Reset modal confirmation
        $('#resetModal').on('show.bs.modal', function(e) {
            // Any additional logic before showing modal
        });

        // Password validation on user form
        $('#userSettingsForm').on('submit', function(e) {
            var currentPassword = $('#current_password').val();
            var newPassword = $('#new_password').val();
            var confirmPassword = $('#new_password_confirmation').val();

            // If any password field is filled, validate all
            if (currentPassword || newPassword || confirmPassword) {
                if (!currentPassword) {
                    e.preventDefault();
                    alert('Please enter your current password to change it.');
                    $('#current_password').focus();
                    return false;
                }
                if (!newPassword) {
                    e.preventDefault();
                    alert('Please enter a new password.');
                    $('#new_password').focus();
                    return false;
                }
                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert('New password and confirmation do not match.');
                    $('#new_password_confirmation').focus();
                    return false;
                }
                if (newPassword.length < 8) {
                    e.preventDefault();
                    alert('Password must be at least 8 characters.');
                    $('#new_password').focus();
                    return false;
                }
            }
        });

    });
</script>
@endpush