<?php

namespace Darvis\MantaProduct\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Darvis\MantaProduct\Models\Cart;
use Darvis\MantaProduct\Models\CartItem;
use Darvis\MantaProduct\Models\Product;
use Darvis\MantaProduct\Models\ProductVariant;
use Darvis\MantaProduct\Models\GiftCard;
use Darvis\MantaProduct\Services\CartService;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup test database
        $this->artisan('migrate', ['--database' => 'testing']);
    }

    /** @test */
    public function it_can_create_a_cart()
    {
        $cart = Cart::create([
            'session_id' => 'test-session',
            'currency' => 'EUR',
        ]);

        $this->assertInstanceOf(Cart::class, $cart);
        $this->assertEquals('EUR', $cart->currency);
        $this->assertTrue($cart->isEmpty());
    }

    /** @test */
    public function it_can_add_product_to_cart()
    {
        $product = Product::factory()->create([
            'title' => 'Test Product',
            'price_per_unit' => 10.00,
            'tax_rate' => 21.00,
        ]);

        $cart = Cart::create(['session_id' => 'test-session']);
        $item = $cart->addProduct($product, 2);

        $this->assertInstanceOf(CartItem::class, $item);
        $this->assertEquals('product', $item->item_type);
        $this->assertEquals(2, $item->quantity);
        $this->assertEquals(10.00, $item->unit_price_excl);
        $this->assertEquals(20.00, $item->line_total_excl);
        $this->assertEquals(4.20, $item->line_tax);
        $this->assertEquals(24.20, $item->line_total_incl);
    }

    /** @test */
    public function it_can_add_variant_to_cart()
    {
        $product = Product::factory()->create([
            'price_per_unit' => 10.00,
            'tax_rate' => 21.00,
        ]);

        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'price_override_excl' => 15.00,
            'tax_rate' => 21.00,
        ]);

        $cart = Cart::create(['session_id' => 'test-session']);
        $item = $cart->addVariant($variant, 1);

        $this->assertEquals('variant', $item->item_type);
        $this->assertEquals(15.00, $item->unit_price_excl);
        $this->assertEquals(15.00, $item->line_total_excl);
    }

    /** @test */
    public function it_calculates_cart_totals_correctly()
    {
        $product1 = Product::factory()->create([
            'price_per_unit' => 10.00,
            'tax_rate' => 21.00,
        ]);

        $product2 = Product::factory()->create([
            'price_per_unit' => 5.00,
            'tax_rate' => 21.00,
        ]);

        $cart = Cart::create(['session_id' => 'test-session']);
        $cart->addProduct($product1, 2); // 20.00 excl, 4.20 tax
        $cart->addProduct($product2, 1); // 5.00 excl, 1.05 tax

        $this->assertEquals(25.00, $cart->subtotal_excl);
        $this->assertEquals(5.25, $cart->tax_total);
        $this->assertEquals(30.25, $cart->total_incl);
    }

    /** @test */
    public function it_can_update_item_quantity()
    {
        $product = Product::factory()->create([
            'price_per_unit' => 10.00,
            'tax_rate' => 21.00,
        ]);

        $cart = Cart::create(['session_id' => 'test-session']);
        $item = $cart->addProduct($product, 1);

        $cart->updateItemQuantity($item, 3);

        $item->refresh();
        $this->assertEquals(3, $item->quantity);
        $this->assertEquals(30.00, $item->line_total_excl);

        $cart->refresh();
        $this->assertEquals(30.00, $cart->subtotal_excl);
    }

    /** @test */
    public function it_can_remove_items()
    {
        $product = Product::factory()->create([
            'price_per_unit' => 10.00,
            'tax_rate' => 21.00,
        ]);

        $cart = Cart::create(['session_id' => 'test-session']);
        $item = $cart->addProduct($product, 2);

        $this->assertFalse($cart->isEmpty());

        $cart->removeItem($item);

        $this->assertTrue($cart->isEmpty());
        $this->assertEquals(0, $cart->subtotal_excl);
    }

    /** @test */
    public function it_can_clear_cart()
    {
        $product1 = Product::factory()->create(['price_per_unit' => 10.00]);
        $product2 = Product::factory()->create(['price_per_unit' => 5.00]);

        $cart = Cart::create(['session_id' => 'test-session']);
        $cart->addProduct($product1, 1);
        $cart->addProduct($product2, 1);

        $this->assertEquals(2, $cart->items()->count());

        $cart->clear();

        $this->assertTrue($cart->isEmpty());
        $this->assertEquals(0, $cart->items()->count());
    }

    /** @test */
    public function cart_service_works_correctly()
    {
        $product = Product::factory()->create([
            'price_per_unit' => 10.00,
            'tax_rate' => 21.00,
        ]);

        $cartService = new CartService();
        $item = $cartService->addProduct($product, 2);

        $summary = $cartService->getSummary();

        $this->assertEquals(2, $summary['items_count']);
        $this->assertEquals(20.00, $summary['subtotal_excl']);
        $this->assertFalse($summary['is_empty']);
        $this->assertCount(1, $summary['items']);
    }
}
