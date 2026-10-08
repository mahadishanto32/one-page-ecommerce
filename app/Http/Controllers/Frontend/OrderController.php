<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function saveIncomplete(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id'            => ['nullable', 'integer', 'exists:orders,id'],
            'phone_number'        => ['required', 'digits:11', 'regex:/^01[0-9]{9}$/'],
            'name'                => ['nullable', 'string', 'max:255'],
            'items'               => ['required', 'array', 'min:1'],
            'items.*.product_id'  => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'    => ['nullable', 'integer', 'min:1', 'max:50'],
            'items.*.price'       => ['nullable', 'numeric', 'min:0'],
            'items.*.size'        => ['nullable', 'string', 'max:20'],
            'items.*.color'       => ['nullable', 'string', 'max:50'],
        ]);

        $items = $validated['items'];
        $totalPrice = collect($items)->sum(fn ($i) => ($i['price'] ?? 0) * ($i['quantity'] ?? 1));

        $summary = collect($items)->map(function ($i) {
            $bits = array_filter([$i['size'] ?? null, $i['color'] ?? null]);
            return '#' . $i['product_id'] . ($bits ? ' (' . implode(', ', $bits) . ')' : '') . ' x' . ($i['quantity'] ?? 1);
        })->implode(' | ');

        $order = DB::transaction(function () use ($validated, $items, $totalPrice, $summary) {
            $order = null;
            if (!empty($validated['order_id'])) {
                $order = Order::where('id', $validated['order_id'])->where('status', 'incomplete')->first();
            }

            $data = [
                'name'         => $validated['name'] ?? null,
                'phone_number' => $validated['phone_number'],
                'total'        => $totalPrice,
                'status'       => 'incomplete',
                'note'         => $summary,
            ];

            $order = $order ? tap($order)->update($data) : Order::create($data);

            // wipe and re-insert items each time the draft is saved
            $order->items()->delete();
            foreach ($items as $item) {
                $qty = $item['quantity'] ?? 1;
                $price = $item['price'] ?? 0;
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity'   => $qty,
                    'price'      => $price,
                    'subtotal'   => $price * $qty,
                    'size'       => $item['size'] ?? null,
                    'color'      => $item['color'] ?? null,
                ]);
            }

            return $order;
        });

        return response()->json(['success' => true, 'order_id' => $order->id]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id'               => ['nullable', 'integer', 'exists:orders,id'],
            'name'                   => ['required', 'string', 'max:255'],
            'phone_number'           => ['required', 'digits:11', 'regex:/^01[0-9]{9}$/'],
            'email'                  => ['nullable', 'email', 'max:255'],
            'address'                => ['required', 'string', 'max:500'],
            'city'                   => ['required', 'string', 'max:100'],
            'delivery_charge'        => ['required', 'numeric', 'min:0'],
            'note'                   => ['nullable', 'string', 'max:1000'],
            'payment_method'         => ['nullable', 'string', 'max:50'],
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.product_id'     => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'       => ['required', 'integer', 'min:1', 'max:50'],
            'items.*.price'          => ['required', 'numeric', 'min:0'],
            'items.*.size'           => ['nullable', 'string', 'max:20'],
            'items.*.color'          => ['nullable', 'string', 'max:50'],
        ]);

        $order = DB::transaction(function () use ($validated) {
            $subtotal = collect($validated['items'])
                ->sum(fn ($i) => $i['price'] * $i['quantity']);

            $order = Order::create([
                'name'            => $validated['name'],
                'phone_number'    => $validated['phone_number'],
                'email'           => $validated['email'] ?? null,
                'address'         => $validated['address'],
                'city'            => $validated['city'],
                'delivery_charge' => $validated['delivery_charge'],
                'note'            => $validated['note'] ?? null,
                'payment_method'  => $validated['payment_method'],
                'status'          => 'pending',
                'total'           => $subtotal + $validated['delivery_charge'],
            ]);

            foreach ($validated['items'] as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                    'subtotal'   => $item['price'] * $item['quantity'],
                    'size'       => $item['size'] ?? null,
                    'color'      => $item['color'] ?? null,
                ]);
            }

            // the "incomplete" draft (and its items, via cascade) is superseded now
            if (!empty($validated['order_id'])) {
                Order::where('id', $validated['order_id'])->where('status', 'incomplete')->delete();
            }

            return $order;
        });

        return response()->json([
            'success'      => true,
            'redirect_url' => route('frontend.order.thankyou', $order->id),
        ]);
    }

    public function thankyou(Order $order): View
    {
        $order->load('items.product');

        return view('frontend.thankyou', ['order' => $order]);
    }
}