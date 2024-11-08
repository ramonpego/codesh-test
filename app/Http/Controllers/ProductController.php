<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return ProductResource::collection(Product::query()->paginate());
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): ProductResource
    {
        return ProductResource::make($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductUpdateRequest $request, Product $product): ProductResource
    {
        $data = $request->validated();
        $product->update($data);
        return ProductResource::make($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->update(['status' => 'trash']);
        return response()->json(['message' => 'Product has been moved to trash']);
    }

    public function search(Request $request)
    {
        $data = $request->validate([
            'query' => 'required|string',
        ]);
        $products = Product::search($data['query'])->paginate();
        return ProductResource::collection($products);
    }
}
