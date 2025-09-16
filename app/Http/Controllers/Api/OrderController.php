<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // GET /api/orders
    public function index()
    {
        $orders = Order::with('items.product')->get();
        return response()->json($orders);
    }

    // POST /api/orders
    public function store(Request $request)
    {
        $request->validate([
            'customer.name' => 'required|string',
            'customer.email' => 'required|email',
            'customer.phone' => 'required|string',
            'customer.address' => 'required|string',
            'customer.payment' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Create order
            $order = Order::create([
                'user_id' => 1 ?? null,
                // 'user_id' => auth()->id() ?? null,
                'total_price' => collect($request->items)->sum(function($item){
                    $product = Product::find($item['id']);
                    return $product->price * $item['quantity'];
                }),
                'status' => 'pending',
                'payment_method' => $request->customer['payment'],
                'shipping_address' => $request->customer['address'],
            ]);

            // Create order items
            foreach ($request->items as $item) {
                $product = Product::find($item['id']);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);

                // Optionally decrease product stock
                $product->decrement('stock', $item['quantity']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order_id' => $order->id
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Order failed: '.$e->getMessage()
            ], 500);
        }
    }

    // GET /api/orders/{order}
    public function show(Order $order)
    {
        $order->load('items.product');
        return response()->json($order);
    }

    // PUT /api/orders/{order}
    public function update(Request $request, Order $order)
    {
        $order->update($request->only(['status', 'payment_method', 'shipping_address']));
        return response()->json($order);
    }

    // DELETE /api/orders/{order}
    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(['message' => 'Order deleted successfully']);
    }
}
