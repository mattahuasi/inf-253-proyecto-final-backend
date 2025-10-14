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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name', 90);
            $table->string('slug', 45)->unique();
            $table->string('description', 225);
            $table->decimal('price', 10, 2)->default(0.00);
            $table->string('photo', 180)->nullable()->default(null);
            $table->integer('stock', false, true)->default(0);
            $table->enum('priority', ['H', 'M', 'L'])->default('L'); // high H, medium M, low L
            $table->boolean('enabled')->default(true);
            $table->unsignedBigInteger('category_id');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->restrictOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
