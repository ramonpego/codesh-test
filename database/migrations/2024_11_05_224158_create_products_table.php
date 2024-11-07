<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->enum('status',['draft','trash','published'])->default('draft');
            $table->dateTime('imported_t');
            $table->text('url');
            $table->string('creator');
            $table->unsignedBigInteger('created_t');
            $table->unsignedBigInteger('last_modified_t');
            $table->string('product_name');
            $table->string('quantity');
            $table->string('brands');
            $table->text('categories');
            $table->string('labels');
            $table->string('cities')->nullable();
            $table->text('purchase_places')->nullable();
            $table->string('stores')->nullable();
            $table->text('ingredients_text')->nullable();
            $table->text('traces')->nullable();
            $table->string('serving_size')->nullable();
            $table->string('serving_quantity')->nullable();
            $table->string('nutriscore_score')->nullable();
            $table->char('nutriscore_grade',1)->nullable();
            $table->string('main_category')->nullable();
            $table->text('image_url')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
