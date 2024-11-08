<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

uses(RefreshDatabase::class);

it('can list products', function () {
    Product::factory()->count(3)->create();

    $response = $this->getJson('/api/products');

    $response->assertStatus(200)->assertJsonCount(3, 'data');
});

it('can show a product', function () {
    $product = Product::factory()->create();

    $response = $this->getJson("/api/products/{$product->code}");

    $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) =>
        $json->where('data.code', $product->code)
            ->etc()
        );
});

it('can update a product', function () {
    $product = Product::factory()->create();
    $data = ['product_name' => 'Updated Product'];

    $response = $this->putJson("/api/products/{$product->code}", $data);

    $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) =>
        $json->where('data.product_name', 'Updated Product')
            ->etc()
        );

    $this->assertDatabaseHas('products', ['code' => $product->code, 'product_name' => 'Updated Product']);
});

it('can move a product to trash', function () {
    $product = Product::factory()->create();

    $response = $this->deleteJson("/api/products/{$product->code}");

    $response->assertStatus(200)
        ->assertJson(['message' => 'Product has been moved to trash']);

    $this->assertDatabaseHas('products', ['code' => $product->code, 'status' => 'trash']);
});

it('can search products', function () {
    Product::factory()->create(['product_name' => 'Test Product']);
    Product::factory()->create(['product_name' => 'Another Product']);

    $response = $this->getJson('/api/products/search?query=Test');

    $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) =>
        $json->has('data', 1)
            ->where('data.0.product_name', 'Test Product')
            ->etc()
        );
});
