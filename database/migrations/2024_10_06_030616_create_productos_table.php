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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('default_code')->nullable();
            $table->unsignedBigInteger('categ_id');
            $table->unsignedBigInteger('subcateg1_id')->nullable();
            $table->unsignedBigInteger('subcateg2_id')->nullable();
            $table->unsignedBigInteger('subcateg3_id')->nullable();
            $table->unsignedBigInteger('subcateg4_id')->nullable();
            $table->decimal('list_price', 8, 2)->nullable();
            $table->json('attributes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
