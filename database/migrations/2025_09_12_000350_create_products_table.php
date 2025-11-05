<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('manta_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('capacity')->default(1);
            $table->unsignedInteger('block_size')->default(1);
            $table->string('product_type')->default('bookable'); // bookable|sellable|both
            $table->string('time_unit')->nullable(); // minute|day
            $table->foreignId('resource_id')->nullable()->constrained('manta_resources')->nullOnDelete();
            $table->string('title');
            $table->text('title_2')->nullable();
            $table->text('title_3')->nullable();
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->text('description')->nullable();
            $table->text('description_2')->nullable();
            $table->text('description_3')->nullable();
            $table->text('comments')->nullable();

            // Unit pricing
            $table->string('unit_type')->nullable(); // piece|meter|m2|m3
            $table->decimal('unit_step', 10, 4)->nullable();
            $table->decimal('min_order_qty', 12, 4)->nullable();
            $table->decimal('max_order_qty', 12, 4)->nullable();
            $table->decimal('price_per_unit', 12, 2)->nullable();
            $table->decimal('tax_rate', 5, 2)->nullable();
            $table->string('calc_mode')->nullable(); // direct_length|dimensions_2d|dimensions_3d
            $table->decimal('wastage_pct', 5, 2)->nullable();
            $table->string('rounding_mode')->nullable(); // ceil|floor|round

            // Dimensions (mm)
            $table->unsignedInteger('length_mm')->nullable();
            $table->unsignedInteger('width_mm')->nullable();
            $table->unsignedInteger('height_mm')->nullable();
            $table->string('dimension_unit')->default('mm');

            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->integer('company_id')->nullable();
            $table->string('host')->nullable();
            $table->integer('pid')->nullable();
            $table->string('locale')->nullable();
            $table->boolean('active')->default(true);
            $table->integer('sort')->default(1);
            $table->index(['active', 'product_type', 'unit_type']);
            $table->index(['length_mm', 'width_mm', 'height_mm']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manta_products');
    }
};
