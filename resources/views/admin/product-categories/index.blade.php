@extends('admin.layouts.app')
@section('title', 'Product Categories')
@section('page-title', 'Product Categories')

@section('content')
<div class="admin-card">
    <div class="admin-card-header">
        <h3><i class="fa-solid fa-layer-group"></i> Product Categories</h3>
        <a href="{{ route('admin.product-categories.create') }}" class="btn-admin btn-admin-primary">
            <i class="fa-solid fa-plus"></i> Add New Category
        </a>
    </div>

    <div class="admin-card-body">

        @if(session('success'))
            <div class="admin-alert admin-alert-success" data-auto-dismiss>
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="btn-close" onclick="this.closest('.admin-alert').remove()">&times;</button>
            </div>
        @endif

        <!-- Filter Bar -->
        <div class="filters-bar" style="padding:0 0 18px; border-bottom:none;">
            <div class="filter-field">
                <label for="categorySearch">Search</label>
                <div class="input-group-icon">
                    <i class="fa-solid fa-magnifying-glass input-icon"></i>
                    <input type="text" id="categorySearch" class="form-control" placeholder="Search by category name...">
                </div>
            </div>
            <div class="filter-field filter-select-field">
                <label for="categoryStatusFilter">Status</label>
                <select id="categoryStatusFilter" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table id="categoriesTable" class="table-admin" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Serial</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if($category->image)
                                    <img src="{{ asset('uploads/product-categories/' . $category->image) }}"
                                         alt="{{ $category->name }}" class="product-thumb">
                                @else
                                    <div class="product-thumb d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-image text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="product-name">{{ $category->name }}</td>
                            <td>{{ $category->serial }}</td>
                            <td>
                                @if($category->status)
                                    <span class="status-pill completed">Active</span>
                                @else
                                    <span class="status-pill cancelled">Inactive</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('admin.product-categories.edit', $category->id) }}"
                                   class="btn-icon-admin" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('admin.product-categories.destroy', $category->id) }}"
                                      method="POST" class="d-inline-block"
                                      onsubmit="return confirm('Are you sure you want to delete this category?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon-admin danger" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No product categories found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    // Initialize DataTable — client-side search, sort & pagination.
    var table = $('#categoriesTable').DataTable({
        responsive: true,
        pageLength: 10,
        lengthChange: false,
        // Actions column shouldn't be sortable
        columnDefs: [
            { orderable: false, targets: -1 },
            { orderable: false, targets: 1 }
        ],
        language: {
            emptyTable: "No product categories found.",
            zeroRecords: "No matching categories found."
        }
    });

    // Custom search box (Name)
    $('#categorySearch').on('keyup', function () {
        table.search(this.value).draw();
    });

    // Custom status filter (matches the "Active" / "Inactive" text in the Status column)
    $('#categoryStatusFilter').on('change', function () {
        var val = this.value;
        table.column(4).search(val ? '^' + val + '$' : '', true, false).draw();
    });

    // Auto-dismiss success alert
    setTimeout(function () {
        $('[data-auto-dismiss]').fadeOut();
    }, 4000);
});
</script>
@endpush