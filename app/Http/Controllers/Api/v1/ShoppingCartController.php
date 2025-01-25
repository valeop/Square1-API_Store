<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShoppingCart;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShoppingCartController extends Controller
{

    // GET method: get shopping cart content
    public function index()
    {
        $cart = Auth::user()->shoppingCart;
        return response()->json($cart->load('cartItem'), 200);
    }

    //POST method: store cart items
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $shoppingCartId = ShoppingCart::where('user_id', Auth::id())->value('id');
            $cartItems = $request->input('cart_items');
            foreach ($cartItems as $cartItem) {
                $cartItem['shopping_cart_id'] = $shoppingCartId;
                $cartItem['price'] = Product::find(ProductVariant::find($cartItem['product_variant_id'])->product_id)->price;
                $cart = CartItem::create($cartItem);
            }

            DB::commit();
            return response()->json($cart->load('productVariant'), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating cart items' . $e->getMessage(), [
                'stack' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);

            return response()->json([
                'error' => 'Failed to create cart items',
            ], 500);
        }
    }

    //PUT method: update cart items' quantity
    public function update(Request $request, string $id)
    {
        try {
            $cartItem = CartItem::findOrFail($id);
            $cartItem->update($request->all());

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Cart item not found',
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error updating cart item',
                'message' => $e->getMessage()
            ], 500);
        }
        return response()->json($cartItem, 200);
    }

    //DELETE method: store cart items
    public function destroy(string $id)
    {
        $cartItem = CartItem::findOrFail($id);
        $cartItem->delete();
        return response()->noContent();
    }
}
