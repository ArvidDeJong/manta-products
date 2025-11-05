<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('manta_price_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('manta_products')->cascadeOnDelete();
            $table->string('name');
            $table->string('rule_type'); // per_piece|per_person|per_period|per_hour|per_day
            $table->decimal('amount', 12, 2);
            $table->decimal('tax_rate', 5, 2)->nullable();
            $table->integer('min_persons')->nullable();
            $table->integer('max_persons')->nullable();
            $table->integer('min_blocks')->nullable();
            $table->integer('max_blocks')->nullable();
            $table->string('days_of_week')->nullable(); // e.g. 6,7 for weekend
            $table->smallInteger('priority')->default(100);
            $table->boolean('combinable')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->index(['product_id', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manta_price_rules');
    }
};
