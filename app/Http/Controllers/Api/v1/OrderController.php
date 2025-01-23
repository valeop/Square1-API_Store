<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    //GET method: get all orders
    public function index(): JsonResponse
    {
        $orders = auth()->user()->order;
        return response()->json($orders, 200);
    }

    //POST method: store an order
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => $request->user()->id,
                'date' => $request->input('date'),
                'total_amount'=>0,
                'status'=> $request->input('status'),
                'payment_method'=> $request->input('payment_method'),
                'shipping_address' => $request->input('shipping_address'),]);

            if ($request->has('order_items')) {
                $totalPrice = 0;
                $orderItems = $request->input('order_items');
                foreach ($orderItems as $orderItem) {
                    $totalPrice += $orderItem['price'] * $orderItem['quantity'];
                    $orderItem['order_id'] = $order->id;
                    OrderItem::create($orderItem);
                }
                $order->update(['total_amount' => $totalPrice]);
            }

            DB::commit();
            return response()->json($order->load('orderItem'), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating order: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);

            return response()->json([
                'error' => 'Failed to create order'
            ], 500);
        }
    }

    //GET method: get an order by id
    public function show(string $id): JsonResponse
    {
        $order = Order::with('orderItem')->find($id);
        if (!Gate::allows('user-view-order', $order)) {
            return response()->json([
                'message' => "Sorry, you can't access this order."
            ], 403);
        }
        return response()->json($order, 200);
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
