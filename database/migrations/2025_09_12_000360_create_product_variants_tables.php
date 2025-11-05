<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('manta_product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('manta_products')->cascadeOnDelete();
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
            $table->softDeletes();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->index(['product_id', 'active']);
        });

        Schema::create('manta_product_variant_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('manta_product_variants')->cascadeOnDelete();
            $table->foreignId('attribute_id')->constrained('manta_attributes')->cascadeOnDelete();
            $table->foreignId('attribute_value_id')->constrained('manta_attribute_values')->cascadeOnDelete();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();

            $table->unique(['product_variant_id', 'attribute_id'], 'variant_attribute_unique');
            $table->index(['attribute_id', 'attribute_value_id'], 'variant_attr_value_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manta_product_variant_values');
        Schema::dropIfExists('manta_product_variants');
    }
};
