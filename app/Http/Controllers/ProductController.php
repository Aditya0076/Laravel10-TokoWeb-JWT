<?php

namespace App\Http\Controllers;

use App\Models\CategoryProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * @OA\Get(
 *     path="/api/category-products",
 *     tags={"Products"},
 *     summary="Get all product categories",
 *     security={{"bearerAuth": {}}},
 *     @OA\Response(response=200, description="Successful operation"),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=404, description="Not found"),
 *     security={
 *         {"token": {}}
 * }
 * )
 * @OA\Get(
 *     path="/api/products",
 *     tags={"Products"},
 *     summary="Get all products",
 *     security={{"bearerAuth": {}}},
 *     @OA\Response(response=200, description="Successful operation"),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=404, description="Not found"),
 *     security={
 *         {"token": {}}
 * }
 * )
 * @OA\Post(
 *     path="/api/products",
 *     tags={"Products"},
 *     summary="Create a new product",
 *     security={{"bearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"product_category_id", "name", "price"},
 *             @OA\Property(property="product_category_id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="Product Name"),
 *             @OA\Property(property="price", type="number", format="float", example=29.99),
 *             @OA\Property(property="image", type="string", example="image.jpg")
 *         )
 *     ),
 *     @OA\Response(response=201, description="Product created successfully"),
 *     @OA\Response(response=400, description="Bad Request"),
 *      security={
 *         {"token": {}}
 *     }
 * )
 */
class ProductController extends Controller
{
    public function getCategoryProducts()
    {
        $categories = CategoryProduct::all();
        return response()->json($categories);
    }

    public function getProducts()
    {
        $products = Product::with('category')->get();
        return response()->json($products);
    }

    public function createProduct(Request $request)
    {
        $request->validate([
            'product_category_id' => 'required|exists:category_products,id',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'image' => 'nullable|string',
        ]);

        $product = Product::create($request->all());

        return response()->json([
            'message' => 'Product successfully created',
            'product' => $product,
        ], 201);
    }
}
