<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class ProductVariantController extends Controller
{

    //GET method: get all products variants
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 10);
        $variants = ProductVariant::paginate($perPage);
        return response()->json($variants, 200);
    }

    //PUT method: store  a new product variant
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $variantData = $request->only(['product_id', 'color', 'size', 'stock_quantity']);
            $variant = ProductVariant::create($variantData);

            DB::commit();
            return response()->json($variant, 201);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error creating product variant: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);

            return response()->json([
                'error' => 'Failed to create product variant',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    //GET method: get a product variant by id
    public function show(string $id): JsonResponse
    {
        try {
            $variant = ProductVariant::findOrFail($id);
            return response()->json($variant, 200);
        } catch (ModelNotFoundException $e) {
            \Log::error('Error retrieving product variant: ' . $e->getMessage(), [
                'variant_id' => $id,
                'stack' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Product variant not found',
                'message' => $e->getMessage()
            ], 404);
        }
    }


    //PUT method: modify a product variant
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $variant = ProductVariant::findOrFail($id);
            $variant->update($request->all());

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Product variant not found',
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error updating product variant',
                'message' => $e->getMessage()
            ], 500);
        }
        return response()->json($variant, 200);
    }

    // DELETE method: modify a product variant
    public function destroy(string $id): \Illuminate\Http\Response
    {
        $variant = ProductVariant::findOrFail($id);
        $variant->delete();
        return response()->noContent();
    }

    // SEARCH method: filter product variants by attributes
    public function search(Request $request): JsonResponse
    {
        $query = ProductVariant::with('product');

        //filter by product name
        if ($request->has('name')) {
            $name = $request->input('name');

            $query->whereHas('product', function (Builder $q) use ($name) {
                $q->where('name', 'like', '%' . $name . '%');
            });
        }

        //filter by color
        if ($request->has('color')) {
            $query->where('color', 'like', '%' . $request->input('color') . '%');
        }

        //filter by size
        if ($request->has('size')) {
            $query->where('size', 'like', '%' . $request->input('size') . '%');
        }

        //filter by attribute (brand, collection, gender)
        if ($request->has('attributes') && $request->has('value')) {
            $attributes = $request->input('attributes');
            $value = $request->input('value');

            $query->whereHas('product', function (Builder $q) use ($attributes, $value) {
                $q->where('other_attributes->' . $attributes, 'like', '%' . $value . '%');
            });
        }

        //filter by min price
        if ($request->has('min_price')) {
            $minPrice = $request->input('min_price');

            $query->whereHas('product', function (Builder $q) use ($minPrice) {
                $q->where('price', '>=', $minPrice);
            });
        }

        //filter by max price
        if ($request->has('max_price')) {
            $maxPrice = $request->input('max_price');

            $query->whereHas('product', function (Builder $q) use ($maxPrice) {
                $q->where('price', '<=', $maxPrice);
            });
        }

        $variants = $query->paginate();
        return response()->json($variants, 200);
    }
}
