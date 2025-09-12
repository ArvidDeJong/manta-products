<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('manta_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // e.g. color, size
            $table->string('type')->default('select'); // select|color_swatches|boolean|text
            $table->json('config')->nullable();
            $table->smallInteger('sort')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });

        Schema::create('manta_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained('manta_attributes')->cascadeOnDelete();
            $table->string('value');           // e.g. Rood
            $table->string('code');            // e.g. red
            $table->string('hex')->nullable(); // #ff0000 for color
            $table->smallInteger('sort')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->unique(['attribute_id', 'code']);
        });

        // manta_product_attributes table will be created in a separate migration after products table
    }

    public function down(): void
    {
        Schema::dropIfExists('manta_attribute_values');
        Schema::dropIfExists('manta_attributes');
    }
};
