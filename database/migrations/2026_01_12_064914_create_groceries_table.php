<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('groceries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('qty', 8, 2);
            $table->string('unit'); // kg, pcs, grams, etc.
            $table->string('category')->nullable();
            $table->string('image')->nullable();
            $table->integer('min_stock')->default(0); // Optional: alert when low
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('groceries');
    }
};
