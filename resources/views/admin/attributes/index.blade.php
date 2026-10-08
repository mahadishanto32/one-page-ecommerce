@extends('admin.layouts.app')
@section('title', 'Product Attributes')
@section('page-title', 'Product Attributes')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-tags me-2"></i> Product Attributes
                    </h5>
                    <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Add New Attribute
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
                        <table id="attributesTable" class="table table-hover table-striped w-100">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Name</th>
                                    <th>Slug</th>
                                    <th>Options</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attributes as $attribute)
                                    <tr>
                                        <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                        <td class="fw-semibold">{{ $attribute->name }}</td>
                                        <td><code class="bg-light px-2 py-1 rounded text-sm">{{ $attribute->slug }}</code></td>
                                        <td>
                                            @forelse($attribute->options as $option)
                                                <span class="badge bg-primary bg-opacity-10 text-primary me-1 mb-1 px-3 py-2 rounded-pill">
                                                    {{ $option->name }}
                                                </span>
                                            @empty
                                                <span class="text-muted fst-italic">No options</span>
                                            @endforelse
                                        </td>
                                        <td>
                                            @if($attribute->status)
                                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                                                    <i class="fas fa-check-circle me-1"></i> Active
                                                </span>
                                            @else
                                                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">
                                                    <i class="fas fa-times-circle me-1"></i> Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.attributes.edit', $attribute->id) }}"
                                                   class="btn btn-outline-primary btn-sm rounded-pill me-1 px-3"
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.attributes.destroy', $attribute->id) }}"
                                                      method="POST" class="d-inline-block"
                                                      onsubmit="return confirm('Are you sure you want to delete this attribute?');">
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
                                        <td colspan="6" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-tags fa-3x mb-3 d-block"></i>
                                                <p class="mb-0">No product attributes found.</p>
                                                <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary btn-sm mt-3">
                                                    <i class="fas fa-plus me-1"></i> Create First Attribute
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
        $('#optionsTable').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search options...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ options",
                infoEmpty: "No options found",
                infoFiltered: "(filtered from _MAX_ total options)",
                zeroRecords: "No matching options found"
            },
            columnDefs: [
                { orderable: false, targets: [0, 4] },
                { width: "60px", targets: [1] },
                { width: "100px", targets: [4] }
            ],
            order: [[2, 'asc']],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]]
        });
    });
</script>
@endpush