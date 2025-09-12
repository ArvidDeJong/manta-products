<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('manta_product_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('manta_products')->cascadeOnDelete();
            $table->foreignId('attribute_id')->constrained('manta_attributes')->cascadeOnDelete();
            $table->boolean('is_required')->default(true);
            $table->smallInteger('sort')->default(0);

            $table->unique(['product_id','attribute_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manta_product_attributes');
    }
};
