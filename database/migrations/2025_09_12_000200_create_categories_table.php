<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manta_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('manta_categories')->onDelete('cascade');
            $table->integer('sort')->default(0);
            $table->boolean('active')->default(true);
            $table->string('image')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['parent_id', 'sort']);
            $table->index('active');
        });

        // Pivot table voor product-category relatie (zonder foreign key naar products)
        Schema::create('manta_category_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('manta_categories')->onDelete('cascade');
            $table->unsignedBigInteger('product_id');
            $table->timestamps();

            $table->unique(['category_id', 'product_id']);
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manta_category_product');
        Schema::dropIfExists('manta_categories');
    }
};
