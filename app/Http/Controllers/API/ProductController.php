<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ProductResource;

/**
 * ProductController handles all Product-related API operations.
 * This includes listing, creating, showing, updating, and soft deleting products.
 * It extends BaseController to use standardized API response methods.
 */
class ProductController extends BaseController
{
    /**
     * List all active products.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function listProducts()
    {
        // Fetch only products with status = 1 (active)
        $products = Product::where('status', 1)->get();

        // Return success response with product collection
        return $this->sendResponse(
            ProductResource::collection($products),
            'Products retrieved successfully'
        );
    }

    /**
     * Create a new product.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createProduct(Request $request)
    {
        // Create a new product record
        $product = Product::create([
            'name'       => $request->name,          // Product name
            'detail'     => $request->detail,        // Product detail/description
            'status'     => 1,                        // Default status = active
            'created_by' => Auth::id(),              // ID of the logged-in user creating the product
        ]);

        // Return success response with the newly created product
        return $this->sendResponse(
            new ProductResource($product),
            'Product created successfully'
        );
    }

    /**
     * Show details of a single product.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showProduct($id)
    {
        // Find product by ID
        $product = Product::find($id);

        // Return error if product not found
        if (!$product) {
            return $this->sendError('Product not found');
        }

        // Return success response with product details
        return $this->sendResponse(
            new ProductResource($product),
            'Product retrieved successfully'
        );
    }

    /**
     * Update an existing product.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProduct(Request $request, $id)
    {
        // Find product by ID
        $product = Product::find($id);

        // Return error if product not found
        if (!$product) {
            return $this->sendError('Product not found');
        }

        // Update product details
        $product->update([
            'name'        => $request->name,          // Updated product name
            'detail'      => $request->detail,        // Updated product detail
            'updated_by'  => Auth::id(),             // ID of logged-in user updating the product
        ]);

        // Return success response with updated product
        return $this->sendResponse(
            new ProductResource($product),
            'Product updated successfully'
        );
    }

    /**
     * Soft delete a product.
     * Also sets status = 0 before deletion.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteProduct($id)
    {
        // Find product by ID
        $product = Product::find($id);

        // Return error if product not found
        if (!$product) {
            return $this->sendError('Product not found');
        }

        // Set status to 0 before soft delete
        $product->update([
            'status' => 0,             // Mark product as inactive
            'updated_by' => Auth::id(),// ID of user performing deletion
        ]);

        // Perform soft delete
        $product->delete();

        // Return success response
        return $this->sendResponse([], 'Product deleted successfully');
    }
}
