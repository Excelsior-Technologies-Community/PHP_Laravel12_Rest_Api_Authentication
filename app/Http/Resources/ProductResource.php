<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * ProductResource transforms the Product model data into a standardized JSON format.
 * This ensures consistent API responses for products.
 */
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,                        // Product ID
            'name' => $this->name,                    // Product name
            'detail' => $this->detail,                // Product description/details
            'status' => $this->status,                // Product status: 1=active, 0=inactive
            'created_by' => $this->created_by,        // ID of user who created the product
            'updated_by' => $this->updated_by,        // ID of user who last updated the product
            'created_at' => $this->created_at?->format('d/m/Y'), // Created date formatted
            'updated_at' => $this->updated_at?->format('d/m/Y'), // Updated date formatted
        ];
    }
}
