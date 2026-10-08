@extends('admin.layouts.app')
@section('title', 'Edit Order')
@section('page-title', 'Edit Order')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
<style>
    .item-row { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 10px; }
    .remove-item { cursor: pointer; color: #dc3545; }
    .remove-item:hover { color: #bb2d3b; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-pen-to-square me-2"></i> Edit Order <span class="text-muted">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </h5>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to Orders
                    </a>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" id="orderForm">
                        @csrf
                        @method('PUT')

                        <h6 class="text-uppercase text-muted small fw-bold mb-3">Customer Information</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $order->name) }}" class="form-control @error('name') is-invalid @enderror">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" name="phone_number" value="{{ old('phone_number', $order->phone_number) }}" class="form-control @error('phone_number') is-invalid @enderror" required>
                                @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" value="{{ old('email', $order->email) }}" class="form-control @error('email') is-invalid @enderror">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Address <span class="text-danger">*</span></label>
                                <input type="text" name="address" value="{{ old('address', $order->address) }}" class="form-control @error('address') is-invalid @enderror" required>
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">City <span class="text-danger">*</span></label>
                                <input type="text" name="city" value="{{ old('city', $order->city) }}" class="form-control @error('city') is-invalid @enderror" required>
                                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <h6 class="text-uppercase text-muted small fw-bold mb-3">Order Items</h6>
                        <div id="itemsContainer">
                            @foreach(old('items', $order->items->toArray()) as $index => $item)
                                <div class="item-row row g-3">
                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item['id'] ?? '' }}">
                                    <div class="col-md-3">
                                        <label class="form-label">Product <span class="text-danger">*</span></label>
                                        <select name="items[{{ $index }}][product_id]" class="form-select product-select @error('items.'.$index.'.product_id') is-invalid @enderror" required>
                                            <option value="">Select Product</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}" {{ old('items.'.$index.'.product_id', $item['product_id']) == $product->id ? 'selected' : '' }}>
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                        <input type="number" min="1" name="items[{{ $index }}][quantity]" value="{{ old('items.'.$index.'.quantity', $item['quantity']) }}" class="form-control quantity-input @error('items.'.$index.'.quantity') is-invalid @enderror" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Price <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0" name="items[{{ $index }}][price]" value="{{ old('items.'.$index.'.price', $item['price']) }}" class="form-control price-input @error('items.'.$index.'.price') is-invalid @enderror" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Subtotal <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0" name="items[{{ $index }}][subtotal]" value="{{ old('items.'.$index.'.subtotal', $item['subtotal']) }}" class="form-control subtotal-input @error('items.'.$index.'.subtotal') is-invalid @enderror" required readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Size</label>
                                        <input type="text" name="items[{{ $index }}][size]" value="{{ old('items.'.$index.'.size', $item['size'] ?? '') }}" class="form-control">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Color</label>
                                        <input type="text" name="items[{{ $index }}][color]" value="{{ old('items.'.$index.'.color', $item['color'] ?? '') }}" class="form-control">
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger btn-sm remove-item">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mb-3">
                            <button type="button" id="addItemBtn" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-plus me-1"></i> Add Item
                            </button>
                        </div>

                        <h6 class="text-uppercase text-muted small fw-bold mb-3">Order Summary</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Delivery Charge</label>
                                <input type="number" step="0.01" min="0" name="delivery_charge" value="{{ old('delivery_charge', $order->delivery_charge) }}" class="form-control @error('delivery_charge') is-invalid @enderror">
                                @error('delivery_charge') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Discount</label>
                                <input type="number" step="0.01" min="0" name="discount" value="{{ old('discount', $order->discount) }}" class="form-control @error('discount') is-invalid @enderror">
                                @error('discount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                                <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                                    <option value="">Select Method</option>
                                    @foreach (['cod' => 'Cash on Delivery', 'bkash' => 'bKash', 'nagad' => 'Nagad', 'bank_transfer' => 'Bank Transfer', 'card' => 'Card'] as $value => $label)
                                        <option value="{{ $value }}" {{ old('payment_method', $order->payment_method) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('payment_method') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Order Total</label>
                                <input type="text" id="orderTotal" class="form-control" value="0.00" readonly>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Note</label>
                                <textarea name="note" rows="2" class="form-control @error('note') is-invalid @enderror">{{ old('note', $order->note) }}</textarea>
                                @error('note') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <h6 class="text-uppercase text-muted small fw-bold mb-3">Courier &amp; Status</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    @foreach (['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'returned'] as $status)
                                        <option value="{{ $status }}" {{ old('status', $order->status) == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Courier Name</label>
                                <input type="text" name="courier_name" value="{{ old('courier_name', $order->courier_name) }}" class="form-control @error('courier_name') is-invalid @enderror">
                                @error('courier_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Courier Tracking ID</label>
                                <input type="text" name="courier_tracking_id" value="{{ old('courier_tracking_id', $order->courier_tracking_id) }}" class="form-control @error('courier_tracking_id') is-invalid @enderror">
                                @error('courier_tracking_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Consignment ID</label>
                                <input type="text" name="consignment_id" value="{{ old('consignment_id', $order->consignment_id) }}" class="form-control @error('consignment_id') is-invalid @enderror">
                                @error('consignment_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <h6 class="text-uppercase text-muted small fw-bold mb-3">Admin Note</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <textarea name="admin_note" rows="3" class="form-control @error('admin_note') is-invalid @enderror" placeholder="Internal note, visible to admins only">{{ old('admin_note', $order->admin_note) }}</textarea>
                                @error('admin_note') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function () {
    let itemIndex = {{ count(old('items', $order->items)) }};

    // Initialize Select2
    $('.product-select').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    // Calculate subtotal for a row
    function calculateSubtotal(row) {
        const quantity = parseInt($(row).find('.quantity-input').val()) || 0;
        const price = parseFloat($(row).find('.price-input').val()) || 0;
        const subtotal = quantity * price;
        $(row).find('.subtotal-input').val(subtotal.toFixed(2));
        calculateTotal();
    }

    // Calculate overall total
    function calculateTotal() {
        let itemsTotal = 0;
        $('.subtotal-input').each(function() {
            itemsTotal += parseFloat($(this).val()) || 0;
        });

        const delivery = parseFloat($('input[name="delivery_charge"]').val()) || 0;
        const discount = parseFloat($('input[name="discount"]').val()) || 0;
        const total = itemsTotal + delivery - discount;
        $('#orderTotal').val(total.toFixed(2));
    }

    // Attach calculation events
    $(document).on('input', '.quantity-input, .price-input', function() {
        calculateSubtotal($(this).closest('.item-row'));
    });

    $(document).on('input', 'input[name="delivery_charge"], input[name="discount"]', function() {
        calculateTotal();
    });

    // Add new item
    $('#addItemBtn').on('click', function() {
        const row = `
            <div class="item-row row g-3">
                <div class="col-md-3">
                    <label class="form-label">Product <span class="text-danger">*</span></label>
                    <select name="items[${itemIndex}][product_id]" class="form-select product-select" required>
                        <option value="">Select Product</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Quantity <span class="text-danger">*</span></label>
                    <input type="number" min="1" name="items[${itemIndex}][quantity]" value="1" class="form-control quantity-input" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Price <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="items[${itemIndex}][price]" value="0.00" class="form-control price-input" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Subtotal <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="items[${itemIndex}][subtotal]" value="0.00" class="form-control subtotal-input" readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Size</label>
                    <input type="text" name="items[${itemIndex}][size]" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Color</label>
                    <input type="text" name="items[${itemIndex}][color]" class="form-control">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm remove-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;

        $('#itemsContainer').append(row);
        $('.product-select').last().select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
        itemIndex++;
    });

    // Remove item
    $(document).on('click', '.remove-item', function() {
        if ($('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
            calculateTotal();
        } else {
            alert('Order must have at least one item.');
        }
    });

    // Calculate initial totals
    $('.item-row').each(function() {
        calculateSubtotal(this);
    });

    // Calculate total on page load
    calculateTotal();
});
</script>
@endpush