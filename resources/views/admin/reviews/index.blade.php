@extends('admin.layouts.app')
@section('title', 'Reviews')
@section('page-title', 'Reviews')
@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-star me-2"></i> Reviews
                    </h5>
                    <a href="{{ route('admin.reviews.create') }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-plus me-1"></i> Add New Review
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
                        <table id="reviewsTable" class="table table-hover table-striped w-100">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">Photo</th>
                                    <th>Name</th>
                                    <th>Location / Designation</th>
                                    <th>Message</th>
                                    <th class="text-center">Rating</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reviews as $review)
                                    <tr>
                                        <td class="text-center">
                                            @if($review->photo)
                                                <img src="{{ asset('uploads/reviews/' . $review->photo) }}"
                                                     alt="{{ $review->name }}"
                                                     class="review-avatar rounded-circle border"
                                                     width="50" height="50"
                                                     style="object-fit: cover;">
                                            @else
                                                <div class="review-avatar-placeholder rounded-circle d-inline-flex align-items-center justify-content-center bg-light border"
                                                     style="width: 50px; height: 50px;">
                                                    <i class="fas fa-user text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="fw-semibold">{{ $review->name }}</td>
                                        <td>{{ $review->designation ?? '—' }}</td>
                                        <td>
                                            <span class="review-message-preview" title="{{ $review->message }}">
                                                {{ Str::limit($review->message, 80) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="review-stars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $review->rating_point ? 'text-warning' : 'text-muted' }}"
                                                       style="font-size: 14px;"></i>
                                                @endfor
                                                <span class="rating-number ms-1 text-muted small">({{ $review->rating_point }})</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if($review->active)
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
                                                <a href="{{ route('admin.reviews.edit', $review->id) }}"
                                                   class="btn btn-outline-primary btn-sm rounded-pill me-1 px-3"
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.reviews.destroy', $review->id) }}"
                                                      method="POST" class="d-inline-block"
                                                      onsubmit="return confirm('Are you sure you want to delete this review?');">
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
                                                <i class="fas fa-comment-slash fa-3x mb-3 d-block"></i>
                                                <p class="mb-0">No reviews found.</p>
                                                <a href="{{ route('admin.reviews.create') }}" class="btn btn-primary btn-sm mt-3">
                                                    <i class="fas fa-plus me-1"></i> Create First Review
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
        $('#reviewsTable').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search reviews...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ reviews",
                infoEmpty: "No reviews found",
                infoFiltered: "(filtered from _MAX_ total reviews)",
                zeroRecords: "No matching reviews found"
            },
            columnDefs: [
                { orderable: false, targets: [0, 3, 6] },
                { width: "70px", targets: [0] },
                { width: "120px", targets: [4] },
                { width: "100px", targets: [5] }
            ],
            order: [[1, 'asc']],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]]
        });
    });
</script>
@endpush