@extends('admin.layouts.app')
@section('title', 'Attribute Options')
@section('page-title', 'Attribute Options')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-list-ul me-2"></i> Attribute Options
                    </h5>
                    <a href="{{ route('admin.attribute-options.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Add New Option
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
                        <table id="optionsTable" class="table table-hover table-striped w-100">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Name</th>
                                    <th>Attribute</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($options as $option)
                                    <tr>
                                        <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                        <td class="fw-semibold">{{ $option->name }}</td>
                                        <td>
                                            <span class="badge bg-info text-white bg-opacity-10 px-3 py-2 rounded-pill">
                                                <i class="fas fa-tag me-1"></i> {{ $option->attribute->name ?? 'N/A' }}
                                            </span>
                                        </td>

                                        <td>
                                            @if($option->status)
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
                                                <a href="{{ route('admin.attribute-options.edit', $option->id) }}"
                                                   class="btn btn-outline-primary btn-sm rounded-pill me-1 px-3"
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.attribute-options.destroy', $option->id) }}"
                                                      method="POST" class="d-inline-block"
                                                      onsubmit="return confirm('Are you sure you want to delete this option?');">
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
                                        <td colspan="5" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-list-ul fa-3x mb-3 d-block"></i>
                                                <p class="mb-0">No attribute options found.</p>
                                                <a href="{{ route('admin.attribute-options.create') }}" class="btn btn-primary btn-sm mt-3">
                                                    <i class="fas fa-plus me-1"></i> Create First Option
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