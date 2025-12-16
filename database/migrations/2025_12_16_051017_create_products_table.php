<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This method creates the 'products' table with all required columns.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Primary key 'id' (auto-increment)
            $table->string('name'); // Product name
            $table->text('detail'); // Product details/description
            $table->boolean('status')->default(value: 1); // Product status: 1 = active, 0 = inactive
            $table->unsignedBigInteger('created_by')->nullable(); // User ID who created the product (nullable)
            $table->unsignedBigInteger('updated_by')->nullable(); // User ID who last updated the product (nullable)
            $table->softDeletes(); // Soft delete column 'deleted_at'
            $table->timestamps(); // 'created_at' and 'updated_at' timestamps
        });
    }

    /**
     * Reverse the migrations.
     * This method drops the 'products' table if it exists.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
