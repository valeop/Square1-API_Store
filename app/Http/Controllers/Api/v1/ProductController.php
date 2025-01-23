<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    //GET method: get all products
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->query('per_page', 10);
            $products = Product::with('variant')->paginate($perPage);

            if ($products->isEmpty()) {
                return response()->json([
                    "message" => "No products found"
                ], 404);
            }
            return response()->json($products, 200);
        } catch (\Throwable $th) {
            \Log::error('Error getting products: ' . $th->getMessage(), [
                'stack' => $th->getTraceAsString(),
            ]);
            return response()->json([
                'error' => 'Error getting products',
                'message' => $th->getMessage()
            ], 500);
        }

    }

    //GET method: get a product by id
    public function show(int $id): JsonResponse
    {
        try {
            $product = Product::with('variant')->findOrFail($id);
            return response()->json($product, 200);
        } catch (ModelNotFoundException $e) {
            \Log::error('Error fetching product: ' . $e->getMessage(), [
                'product_id' => $id,
                'stack' => $e -> getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Product not found',
                'message' => $e->getMessage()
            ], 404);
        }

    }

    //POST method: store a new product
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $productData = $request->only(['name', 'description', 'price', 'other_attributes']);
            $product = Product::create($productData);

            if ($request->has('variants')) { //Variant in this conditional means Json's value
                $variants = $request->input('variants');
                foreach ($variants as $variant) {
                    $variant['product_id'] = $product->id;
                    ProductVariant::create($variant);
                }
            }

            DB::commit();
            return response()->json($product->load('variant'), 201); //This variant means the relation

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error creating product and variants: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);

            return response()->json([
               'error' => 'Failed to create product',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    //PUT method: modify a whole product or just an attribute
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $product = Product::find($id);
            $product->update($request->all());
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Product not found',
                'message' => $e->getMessage()
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Error updating product',
                'message' => $th->getMessage()
            ], 500);
        }
        return response()->json($product, 200);
    }

    //DELETE method: destroy a product's register
    public function destroy(int $id): \Illuminate\Http\Response
    {
        $product = Product::find($id);
        $product->delete();
        return response()->noContent();
    }

    //SEARCH method: filter products by attributes
    public function search(Request $request) {
        $query = Product::with('variant');

        //filter by product name
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        //filter by min price
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }

        //filter by max price
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        //filter by attribute
        if ($request->has('attributes') && $request->has('value')) {
            $attributes = $request->input('attributes');
            $value = $request->input('value');

            $query->whereJsonContains('other_attributes->' . $attributes, $value);
        }

        //filter by color
        if ($request->has('color')) {
            $color = $request->input('color');

            $query->whereHas('variant', function (Builder $q) use ($color) {
                $q->where('color', $color);
            });
        }

        //filter by size
        if ($request->has('size')) {
            $size = $request->input('size');

            $query->whereHas('variant', function (Builder $q) use ($size) {
                $q->where('size', $size);
            });
        }

        $products = $query->paginate();
        return response()->json($products, 200);
    }
}
