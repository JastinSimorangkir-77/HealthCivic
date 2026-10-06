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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_code')->unique();
            $table->text('patient_name');
            $table->text('phone');
            $table->text('bpjs_number')->nullable();
            $table->string('service_type', 24);
            $table->string('specialty', 40);
            $table->string('doctor')->nullable();
            $table->date('visit_date');
            $table->string('time_slot', 20);
            $table->string('insurance', 20);
            $table->string('status', 32)->default('menunggu_konfirmasi');
            $table->boolean('consent_accepted');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
