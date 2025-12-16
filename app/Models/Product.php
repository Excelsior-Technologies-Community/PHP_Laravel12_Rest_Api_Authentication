<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Product model represents the products in the application.
 * It supports soft deletes and can be mass assigned using $fillable.
 */
class Product extends Model
{
    // Use HasFactory for database factories
    // Use SoftDeletes to enable soft deleting of products
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * These fields can be set via Product::create() or $product->update().
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',        // Product name
        'detail',      // Product description/details
        'status',      // Product status: 1=active, 0=inactive
        'created_by',  // ID of user who created the product
        'updated_by'   // ID of user who last updated the product
    ];
}
