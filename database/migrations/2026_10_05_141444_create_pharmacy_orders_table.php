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
        Schema::create('pharmacy_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->text('patient_name');
            $table->text('phone');
            $table->text('items');
            $table->text('delivery_address')->nullable();
            $table->string('fulfillment', 16);
            $table->string('status', 32)->default('menunggu_konfirmasi');
            $table->unsignedBigInteger('total');
            $table->boolean('consent_accepted');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_orders');
    }
};
