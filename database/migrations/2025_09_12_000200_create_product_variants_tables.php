<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->string('title')->nullable();
            $table->boolean('active')->default(true);
            $table->string('variant_key')->nullable(); // color:red|size:l

            // Overrides
            $table->decimal('price_override_excl', 12, 2)->nullable();
            $table->decimal('tax_rate', 5, 2)->nullable();
            $table->integer('stock_qty')->nullable(); // null = no tracking
            $table->integer('capacity')->nullable();  // for bookable

            // Unit overrides
            $table->string('unit_type')->nullable();
            $table->decimal('unit_step', 10, 4)->nullable();
            $table->string('calc_mode')->nullable();
            $table->decimal('wastage_pct', 5, 2)->nullable();
            $table->string('rounding_mode')->nullable();

            // Dimension overrides
            $table->unsignedInteger('length_mm')->nullable();
            $table->unsignedInteger('width_mm')->nullable();
            $table->unsignedInteger('height_mm')->nullable();

            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['product_id','active']);
        });

        Schema::create('product_variant_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
            $table->foreignId('attribute_value_id')->constrained('attribute_values')->cascadeOnDelete();

            $table->unique(['product_variant_id','attribute_id']);
            $table->index(['attribute_id','attribute_value_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variant_values');
        Schema::dropIfExists('product_variants');
    }
};
