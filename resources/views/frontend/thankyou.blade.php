<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Thank You | Froggy Kids</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500;600;700;800&family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
<style>
    .thankyou-wrap {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
    }
    .thankyou-card {
        max-width: 520px;
        width: 100%;
        background: #fff;
        border-radius: 20px;
        padding: 40px 32px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        text-align: center;
    }
    .thankyou-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: #DFF3E6;
        color: #2E8B57;
        font-size: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }
    .order-id {
        display: inline-block;
        background: #F4F9F6;
        border: 1px dashed #4CAF7D;
        color: #2E8B57;
        font-weight: 800;
        padding: 8px 18px;
        border-radius: 10px;
        margin: 14px 0 24px;
        font-family: 'Baloo 2', sans-serif;
    }
    .order-details {
        text-align: left;
        border-top: 1px solid #eee;
        margin-top: 20px;
        padding-top: 20px;
    }
    .order-details .row {
        display: flex;
        justify-content: space-between;
        padding: 6px 0;
        font-size: .92rem;
    }
    .order-details .row span:first-child {
        color: #8a8a8a;
    }
    .order-details .row span:last-child {
        font-weight: 700;
        text-align: right;
    }
    .status-pill {
        display: inline-block;
        padding: 3px 12px;
        border-radius: 999px;
        background: #FFF4D6;
        color: #9A7B0A;
        font-size: .78rem;
        font-weight: 700;
        text-transform: capitalize;
    }
</style>
</head>
<body>

<div class="thankyou-wrap">
    <div class="thankyou-card">
        <div class="thankyou-icon">✓</div>
        <h2 style="font-family:'Baloo 2', sans-serif; margin-bottom:4px;">Thank You, {{ $order->name }}!</h2>
        <p style="color:#8a8a8a; font-size:.95rem;">Your order has been placed successfully. We'll call you shortly to confirm delivery.</p>

        <div class="order-id">Order #{{ $order->id }}</div>

        <div class="order-details">
            <div class="row">
                <span>Status</span>
                <span><span class="status-pill">{{ str_replace('_', ' ', $order->status) }}</span></span>
            </div>

            @foreach ($order->items as $item)
                <div class="row">
                    <span>{{ $item->product->name ?? ('Product #' . $item->product_id) }}
                        {{ $item->size ? '(' . $item->size : '' }}{{ $item->color ? ($item->size ? ', ' : '(') . $item->color : '' }}{{ ($item->size || $item->color) ? ')' : '' }}
                        &times; {{ $item->quantity }}</span>
                    <span>৳{{ number_format($item->subtotal, 2) }}</span>
                </div>
            @endforeach

            <div class="row">
                <span>Delivery Charge</span>
                <span>৳{{ number_format($order->delivery_charge, 2) }}</span>
            </div>
            <div class="row">
                <span>Total Payable</span>
                <span>৳{{ number_format($order->total, 2) }}</span>
            </div>
            <div class="row">
                <span>Payment Method</span>
                <span>{{ $order->payment_method }}</span>
            </div>
            <div class="row">
                <span>Delivery Address</span>
                <span>{{ $order->address }}, {{ $order->city }}</span>
            </div>
            <div class="row">
                <span>Phone</span>
                <span>{{ $order->phone_number }}</span>
            </div>
        </div>

        <a href="{{ url('/') }}" class="btn btn-primary btn-block" style="margin-top:28px; display:block;">
            Continue Shopping
        </a>
    </div>
</div>

</body>
</html>