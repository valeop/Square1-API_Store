<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShoppingCart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            //evaluate if there's any item in shopping cart
            $cartId = ShoppingCart::where('user_id', Auth::id())->value('id');
            $cart = CartItem::where('shopping_cart_id', $cartId)->get();
            if ($cart->count() < 1) {
                return response()->json([
                    'error' => "Sorry, you can't create an order without items in your cart.",
                ]);
            }

            //create a new order register
            $order = Order::create([
                'user_id' => $request->user()->id,
                'date' => $request->input('date'),
                'total_amount'=>0,
                'status'=> $request->input('status'),
                'payment_method'=> $request->input('payment_method'),
                'shipping_address' => $request->input('shipping_address'),]);

            //create a new order item
            $totalPrice = 0;
            foreach ($cart as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $cartItem['product_variant_id'],
                    'quantity' => $cartItem['quantity'],
                    'price' => $cartItem['price']
                    ]);
                $totalPrice += $cartItem['price'] * $cartItem['quantity'];
            }

            $order->update(['total_amount' => $totalPrice]);

            //delete cart items from shopping cart
            DB::table('cart_items')->where('shopping_cart_id', $cartId)->delete();

            //update shopping cart status to completed
            DB::table('shopping_carts')->where('user_id', $request->user()->id)->update(['status' => 'completed']);

            DB::commit();
            return response()->json([
                'order' => $order->load('orderItem'),
                'shopping_cart_status' => $request->user()->shoppingCart->status,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating order: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);

            return response()->json([
                'error' => 'Failed to create order',
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
