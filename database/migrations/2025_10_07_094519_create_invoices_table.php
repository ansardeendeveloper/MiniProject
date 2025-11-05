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
    Schema::create('invoices', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger('service_id');
         $table->unsignedBigInteger('customer_id');
         //fk
         $table->foreign('service_id')->references('id')->on('service_records')->onDelete('cascade');
         $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
         $table->string('total_amount', 20);
         $table->string('payment_status', 20);
         $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
