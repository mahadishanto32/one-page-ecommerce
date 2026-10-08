@extends('admin.layouts.app')
@section('title', 'Orders')
@section('page-title', 'Orders')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .filter-card .form-label { font-weight: 500; font-size: .8rem; color: #6c757d; margin-bottom: .3rem; }
    table.dataTable thead th { white-space: nowrap; }
    #ordersTable .badge { font-weight: 500; font-size: .75rem; padding: .4em .65em; }
    .product-list { font-size: 0.85rem; }
    .product-item { 
        display: inline-block; 
        background: #f8f9fa; 
        padding: 0.15rem 0.5rem; 
        border-radius: 3px; 
        margin: 0.1rem;
        font-size: 0.8rem;
    }
    /* Remove double pagination */
    .dataTables_wrapper .dataTables_paginate { padding-top: 0.5rem; }
    .dataTables_wrapper .dataTables_info { padding-top: 0.5rem; }
    .dataTables_length { display: none; }
    .dataTables_filter { display: none; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">

            {{-- Filters --}}
            <div class="card shadow-sm mb-3 filter-card">
                <div class="card-body pb-3">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <input type="text" id="filterSearch" class="form-control form-control-sm" placeholder="Name, phone, email, tracking...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select id="filterStatus" class="form-select form-select-sm">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="returned">Returned</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Payment Method</label>
                            <select id="filterPaymentMethod" class="form-select form-select-sm">
                                <option value="">All Methods</option>
                                <option value="cod">Cash on Delivery</option>
                                <option value="bkash">bKash</option>
                                <option value="nagad">Nagad</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="card">Card</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Date From</label>
                            <input type="text" id="filterDateFrom" class="form-control form-control-sm" placeholder="Select date" autocomplete="off">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Date To</label>
                            <input type="text" id="filterDateTo" class="form-control form-control-sm" placeholder="Select date" autocomplete="off">
                        </div>
                        <div class="col-md-1">
                            <button type="button" id="btnFilter" class="btn btn-primary btn-sm w-100" title="Apply Filters">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12 text-end">
                            <button type="button" id="btnResetFilter" class="btn btn-link btn-sm text-decoration-none">
                                <i class="fas fa-rotate-left me-1"></i> Reset Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-cart-shopping me-2"></i> Orders
                    </h5>
                    <div>
                        <a href="{{ route('admin.orders.incomplete') }}" class="btn btn-outline-warning btn-sm me-2">
                            <i class="fas fa-exclamation-triangle me-1"></i> Incomplete Orders
                        </a>
                        <a href="{{ route('admin.orders.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> Add New Order
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="ordersTable" class="table table-hover align-middle w-100">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:30px;">
                                        <input type="checkbox" id="checkAll" class="form-check-input">
                                    </th>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Products</th>
                                    <th>City</th>
                                    <th>Total</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th class="text-center" style="width: 100px;">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-triangle-exclamation text-danger me-2"></i>Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this order? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-1"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
$(function () {
    let deleteUrl = null;

    flatpickr('#filterDateFrom', { dateFormat: 'Y-m-d' });
    flatpickr('#filterDateTo', { dateFormat: 'Y-m-d' });

    const table = $('#ordersTable').DataTable({
        processing: true,
        serverSide: true,
        searching: true,
        ajax: {
            url: "{{ route('admin.orders.index') }}",
            data: function (d) {
                d.status = $('#filterStatus').val();
                d.payment_method = $('#filterPaymentMethod').val();
                d.date_from = $('#filterDateFrom').val();
                d.date_to = $('#filterDateTo').val();
            }
        },
        columns: [
            { data: 'checkbox', orderable: false, searchable: false },
            { data: 'order_id' },
            { data: 'customer' },
            { data: 'products', orderable: false },
            { data: 'city' },
            { data: 'total' },
            { data: 'payment_method', orderable: false },
            { data: 'status' },
            { data: 'date' },
            { data: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[1, 'desc']],
        // Fixed dom to remove duplicate pagination
        dom: '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        language: {
            emptyTable: 'No orders found.',
            zeroRecords: 'No matching orders found.',
            paginate: {
                previous: '<i class="fas fa-chevron-left"></i>',
                next: '<i class="fas fa-chevron-right"></i>'
            }
        },
        // Column definitions for proper rendering
        columnDefs: [
            {
                targets: [3], // Products column
                render: function(data) {
                    if (!data) return '<span class="text-muted">No products</span>';
                    return '<div class="product-list">' + data + '</div>';
                }
            },
            {
                targets: [6], // Payment column
                render: function(data) {
                    if (!data) return '<span class="text-muted">N/A</span>';
                    return data;
                }
            }
        ]
    });

    // Custom search box drives DataTables' global search
    let searchTimer;
    $('#filterSearch').on('keyup', function () {
        clearTimeout(searchTimer);
        const val = $(this).val();
        searchTimer = setTimeout(function () {
            table.search(val).draw();
        }, 400);
    });

    $('#btnFilter').on('click', function () {
        table.draw();
    });

    $('#btnResetFilter').on('click', function () {
        $('#filterSearch').val('');
        $('#filterStatus').val('');
        $('#filterPaymentMethod').val('');
        $('#filterDateFrom').val('');
        $('#filterDateTo').val('');
        table.search('').draw();
    });

    $('#checkAll').on('change', function () {
        $('.row-checkbox').prop('checked', $(this).is(':checked'));
    });

    // Delete flow
    $(document).on('click', '.btn-delete', function () {
        deleteUrl = $(this).data('url');
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    });

    $('#confirmDeleteBtn').on('click', function () {
        if (!deleteUrl) return;

        $.ajax({
            url: deleteUrl,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function () {
                bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
                table.draw(false);
            },
            error: function () {
                alert('Something went wrong while deleting the order.');
            }
        });
    });
});
</script>
@endpush