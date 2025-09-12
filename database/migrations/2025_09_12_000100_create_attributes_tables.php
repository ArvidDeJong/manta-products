<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // e.g. color, size
            $table->string('type')->default('select'); // select|color_swatches|boolean|text
            $table->json('config')->nullable();
            $table->smallInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
            $table->string('value');           // e.g. Rood
            $table->string('code');            // e.g. red
            $table->string('hex')->nullable(); // #ff0000 for color
            $table->smallInteger('sort')->default(0);
            $table->timestamps();

            $table->unique(['attribute_id','code']);
        });

        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
            $table->boolean('is_required')->default(true);
            $table->smallInteger('sort')->default(0);

            $table->unique(['product_id','attribute_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attributes');
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attributes');
    }
};
