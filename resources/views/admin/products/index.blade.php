@extends('admin.layouts.app')
@section('title', 'Product')
@section('page-title', 'Product')
@section('content')

{{--
    NOTE: this view drives its search + category filter entirely through DataTables
    on the client side, so the controller should pass the FULL products collection
    (with ->with('category') eager loaded) rather than ->paginate(). DataTables
    handles searching, filtering and pagination itself once the table is initialised
    below. $categories (all categories) is also expected for the filter dropdown.
--}}

@if (session('success'))
    <div class="admin-alert admin-alert-success" data-auto-dismiss>
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
        <button type="button" class="btn-close" onclick="this.closest('.admin-alert').remove()">&times;</button>
    </div>
@endif

<div class="admin-card">
    <div class="admin-card-header">
        <h3><i class="fa-solid fa-box"></i> Products</h3>
        <a href="{{ route('admin.products.create') }}" class="btn-admin btn-admin-primary">
            <i class="fa-solid fa-plus"></i> Add New Product
        </a>
    </div>

    <div class="filters-bar">
        <div class="filter-field" style="flex:1;">
            <label for="product-search">Search</label>
            <input type="text" id="product-search" class="form-control" placeholder="Search by name or SKU...">
        </div>

        <div class="filter-field filter-select-field">
            <label for="category-filter">Category</label>
            <select id="category-filter" class="form-select">
                <option value="">All Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->name }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-field" style="align-self:flex-end;">
            <button type="button" id="reset-filters" class="btn-admin btn-admin-outline">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </button>
        </div>
    </div>

    <div class="admin-card-body">
        <div class="table-responsive">
            <table id="products-table" class="table-admin" style="width:100%;">
                <thead>
                    <tr>
                        <th style="width: 50px;">SL</th>
                        <th style="width: 70px;">Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>
                                <img src="{{ asset('uploads/products/' . $product->featured_image) }}"
                                    alt="{{ $product->name }}"
                                    class="product-thumb">
                            </td>

                            <td>
                                <div class="product-name">{{ $product->name }}</div>

                                @if ($product->sku)
                                    <div class="product-sku">SKU: {{ $product->sku }}</div>
                                @endif
                            </td>

                            <td>
                                {{ $product->category->name ?? '-' }}
                            </td>

                            <td>
                                <span class="price-current">
                                    {{ number_format($product->price, 2) }}
                                </span>

                                @if ($product->previous_price)
                                    <br>
                                    <span class="price-previous">
                                        {{ number_format($product->previous_price, 2) }}
                                    </span>
                                @endif
                            </td>

                            <td>
                                @php
                                    $stockClass = match ($product->stock_status) {
                                        'In Stock' => 'completed',
                                        'Out of Stock' => 'cancelled',
                                        'Pre-Order' => 'pending',
                                        default => 'processing',
                                    };
                                @endphp

                                <span class="status-pill {{ $stockClass }}">
                                    {{ $product->stock_status }}
                                </span>
                            </td>

                            <td>
                                <span class="status-pill {{ $product->status === 'Active' ? 'completed' : 'cancelled' }}">
                                    {{ $product->status }}
                                </span>
                            </td>

                            <td>
                                <a href="{{ route('admin.products.edit', $product->id) }}"
                                class="btn-icon-admin"
                                title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>

                                <form action="{{ route('admin.products.destroy', $product->id) }}"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Are you sure you want to delete this product?');">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn-icon-admin danger"
                                            title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(function () {
    var table = $('#products-table').DataTable({
        responsive: true,
        pageLength: 10,

        order: [[0, 'asc']],

        language: {
            emptyTable: 'No products found.',
            zeroRecords: 'No products match your search.'
        },

        columnDefs: [
            { orderable: false, targets: [1, 7] }
        ]
    });

    $('#product-search').on('keyup', function () {
        table.search(this.value).draw();
    });

    $('#category-filter').on('change', function () {
        var value = this.value;

        table
            .column(3)
            .search(
                value
                    ? '^' + $.fn.dataTable.util.escapeRegex(value) + '$'
                    : '',
                true,
                false
            )
            .draw();
    });

    $('#reset-filters').on('click', function () {
        $('#product-search').val('');
        $('#category-filter').val('');

        table.search('');
        table.column(3).search('');
        table.draw();
    });
});
</script>
@endpush