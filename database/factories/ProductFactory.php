<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->ean13,
            'creator' => $this->faker->name,
            'created_t' => $this->faker->unixTime,
            'product_name' => $this->faker->sentence,
            'brands' => $this->faker->company,
            'categories' => $this->faker->words(3, true),
            'purchase_places' => $this->faker->words(3, true),
            'stores' => $this->faker->words(3, true),
            'ingredients_text' => $this->faker->sentence,
            'imported_t' => $this->faker->unixTime,
            'url' => $this->faker->url,
            'quantity' => $this->faker->randomFloat(2, 0, 1000),
            'labels' => $this->faker->words(3, true),
        ];
    }
}
