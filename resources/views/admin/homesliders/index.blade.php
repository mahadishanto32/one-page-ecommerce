@extends('admin.layouts.app')

@section('title', 'Home Sliders')
@section('page-title', 'Home Sliders')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-sliders-h me-2"></i> Manage Home Sliders
                    </h5>
                    <a href="{{ route('admin.homesliders.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Add New Slider
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="slidersTable" class="table table-hover table-striped w-100">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>File</th>
                                    <th class="text-center">Serial</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sliders as $index => $slider)
                                    <tr>
                                        <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                        <td class="fw-semibold">
                                            {{ $slider->name ?? 'Untitled' }}
                                            @if(!$slider->name)
                                                <span class="badge bg-warning bg-opacity-10 text-warning ms-1">No Name</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $slider->slider_type == 'image' ? 'bg-primary bg-opacity-10 text-primary' : 'bg-info bg-opacity-10 text-white' }} px-3 py-2 rounded-pill">
                                                <i class="fas {{ $slider->slider_type == 'image' ? 'fa-image' : 'fa-video' }} me-1"></i>
                                                {{ ucfirst($slider->slider_type) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($slider->slider_type == 'image')
                                                <img src="{{ asset($slider->file_path) }}" 
                                                     alt="Slider" 
                                                     class="slider-thumbnail rounded border"
                                                     width="80" height="50"
                                                     style="object-fit: cover;">
                                            @else
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="fas fa-video text-info"></i>
                                                    <span class="text-muted small">Video File</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary bg-opacity-10 text-white px-3 py-2 rounded-pill">
                                                #{{ $slider->serial }}
                                            </span>
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.homesliders.toggle-status', $slider) }}" method="POST" class="d-inline-block">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="badge border-0 {{ $slider->status ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} px-3 py-2 rounded-pill"
                                                        style="cursor: pointer; transition: all 0.3s ease;">
                                                    <i class="fas {{ $slider->status ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                                    {{ $slider->status ? 'Active' : 'Inactive' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.homesliders.edit', $slider) }}" 
                                                   class="btn btn-outline-primary btn-sm rounded-pill me-1 px-3"
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.homesliders.destroy', $slider) }}" 
                                                      method="POST" class="d-inline-block"
                                                      onsubmit="return confirm('Are you sure you want to delete this slider?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-outline-danger btn-sm rounded-pill px-3"
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-sliders-h fa-3x mb-3 d-block"></i>
                                                <p class="mb-0">No sliders found. Create your first slider!</p>
                                                <a href="{{ route('admin.homesliders.create') }}" class="btn btn-primary btn-sm mt-3">
                                                    <i class="fas fa-plus me-1"></i> Create First Slider
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#slidersTable').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search sliders...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ sliders",
                infoEmpty: "No sliders found",
                infoFiltered: "(filtered from _MAX_ total sliders)",
                zeroRecords: "No matching sliders found"
            },
            columnDefs: [
                { orderable: false, targets: [0, 3, 6] },
                { width: "80px", targets: [3] },
                { width: "100px", targets: [4] }
            ],
            order: [[4, 'asc']],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]]
        });
    });
</script>
@endpush