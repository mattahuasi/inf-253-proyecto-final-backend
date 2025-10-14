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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code', 45)->unique();
            $table->enum('type', ['O1', 'O2', 'O3'])->default('O3'); // Pedido Para Llevar (O1), Pedido A Domicilio (O2), Pedido En El Lugar (O3)
            $table->dateTime('ordered_at');
            $table->dateTime('delivered_at')->nullable();
            $table->string('customer_name',45);
            $table->string('customer_phone',20);
            $table->string('comment', 180)->nullable();
            $table->boolean('payment_made')->default(false);
            $table->unsignedBigInteger('state_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('address_id')->nullable();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('table_number')->nullable();
            $table->timestamps();

            $table->foreign('state_id')->references('id')->on('states')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreign('customer_id')->references('person_id')->on('customers')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('address_id')->references('id')->on('addresses')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('employee_id')->references('person_id')->on('employees')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreign('table_number')->references('number')->on('tables')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
