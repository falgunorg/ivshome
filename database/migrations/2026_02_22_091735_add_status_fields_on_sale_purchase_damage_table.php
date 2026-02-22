<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('item_purchase', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        });

        Schema::table('item_sale', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        });

        Schema::table('damages', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('item_purchase', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('item_sale', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('damages', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
