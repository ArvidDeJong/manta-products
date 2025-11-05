<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manta_carts', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable(); // Voor guest users
            $table->unsignedBigInteger('user_id')->nullable(); // Voor ingelogde users
            $table->string('currency', 3)->default('EUR');
            $table->decimal('subtotal_excl', 12, 2)->default(0);
            $table->decimal('tax_total', 12, 2)->default(0);
            $table->decimal('total_incl', 12, 2)->default(0);
            $table->json('meta')->nullable(); // Voor extra data zoals kortingscodes
            $table->timestamp('expires_at')->nullable(); // Wanneer cart verloopt
            $table->timestamps();
            
            $table->index(['session_id', 'user_id']);
            $table->index('expires_at');
        });

        Schema::create('manta_cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('manta_carts')->cascadeOnDelete();
            
            // Product referenties - een van deze moet gevuld zijn
            $table->foreignId('product_id')->nullable()->constrained('manta_products')->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained('manta_product_variants')->cascadeOnDelete();
            $table->foreignId('gift_card_id')->nullable()->constrained('manta_gift_cards')->cascadeOnDelete();
            
            // Item details
            $table->string('item_type')->default('product'); // product|variant|gift_card
            $table->decimal('quantity', 10, 4)->default(1);
            $table->decimal('unit_price_excl', 12, 2);
            $table->decimal('tax_rate', 5, 2);
            $table->decimal('line_total_excl', 12, 2);
            $table->decimal('line_tax', 12, 2);
            $table->decimal('line_total_incl', 12, 2);
            
            // Voor producten met dimensies/configuratie
            $table->json('configuration')->nullable(); // Afmetingen, attributen, etc.
            $table->json('meta')->nullable(); // Extra data
            
            $table->timestamps();
            
            $table->index(['cart_id', 'item_type']);
            $table->index(['product_id', 'product_variant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manta_cart_items');
        Schema::dropIfExists('manta_carts');
    }
};
