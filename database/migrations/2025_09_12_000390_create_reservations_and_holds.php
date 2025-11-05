<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('manta_reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable(); // app-defined customers table (optional FK)
            $table->foreignId('room_id')->nullable()->constrained('manta_rooms')->nullOnDelete();
            $table->unsignedBigInteger('user_id')->nullable();   // users table in app
            $table->unsignedBigInteger('staff_id')->nullable();  // staff table in app
            $table->string('status')->default('draft');          // draft|held|confirmed|cancelled|completed
            $table->string('currency', 3)->default('EUR');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->decimal('total_excl', 12, 2)->default(0);
            $table->decimal('total_tax', 12, 2)->default(0);
            $table->decimal('total_incl', 12, 2)->default(0);
            $table->string('channel')->default('web');
            $table->string('reference')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->index(['room_id', 'starts_at', 'ends_at']);
            $table->index(['status']);
        });

        Schema::create('manta_reservation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('manta_reservations')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('manta_products')->restrictOnDelete();
            $table->unsignedBigInteger('product_variant_id')->nullable(); // optional FK to product_variants
            $table->integer('blocks')->default(1);
            $table->integer('persons')->default(1);
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price_excl', 12, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(21.00);
            $table->decimal('line_total_excl', 12, 2)->default(0);
            $table->decimal('line_total_tax', 12, 2)->default(0);
            $table->decimal('line_total_incl', 12, 2)->default(0);
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->json('price_breakdown')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->index(['product_id', 'starts_at', 'ends_at']);
        });

        Schema::create('manta_holds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained('manta_products')->nullOnDelete();
            $table->foreignId('resource_id')->nullable()->constrained('manta_resources')->nullOnDelete();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->integer('quantity')->default(1);
            $table->uuid('token')->unique();
            $table->timestamp('expires_at');
            $table->timestamps();
            $table->softDeletes();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->index(['product_id', 'starts_at', 'ends_at']);
            $table->index(['resource_id', 'starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manta_holds');
        Schema::dropIfExists('manta_reservation_items');
        Schema::dropIfExists('manta_reservations');
    }
};
