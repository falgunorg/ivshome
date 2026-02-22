<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void {
        // 1. Independent Tables
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 191);
            $table->string('email', 191)->unique();
            $table->string('password', 191);
            $table->rememberToken();
            $table->enum('role', ['admin', 'staff'])->default('staff');
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 191);
            $table->text('address')->nullable();
            $table->string('email', 191)->nullable();
            $table->string('phone', 191)->nullable();
            $table->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 191);
            $table->text('address')->nullable();
            $table->string('email', 191)->nullable();
            $table->string('phone', 191)->nullable();
            $table->timestamps();
        });

        Schema::create('item_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 191);
            $table->string('code', 191)->nullable();
            $table->timestamps();
        });

        Schema::create('groceries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 191);
            $table->decimal('qty', 8, 2);
            $table->string('unit', 191);
            $table->string('category', 191)->nullable();
            $table->string('image', 191)->nullable();
            $table->integer('min_stock')->default(0);
            $table->timestamps();
        });

        Schema::create('recipes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 191);
            $table->text('instructions');
            $table->text('note')->nullable();
            $table->string('image', 191)->nullable();
            $table->timestamps();
        });

        // 2. Structural Tables
        Schema::create('locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->string('name', 191)->nullable();
            $table->string('code', 191)->nullable();
            $table->timestamps();
        });

        Schema::create('cabinets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('location_id');
            $table->unsignedInteger('user_id');
            $table->string('title', 191)->nullable();
            $table->string('code', 191)->nullable();
            $table->timestamps();
        });

        Schema::create('drawers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('cabinet_id');
            $table->string('title', 191);
            $table->timestamps();
        });

        // 3. Core Items Table
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('serial_number', 191)->nullable();
            $table->unsignedInteger('user_id');
            $table->string('item_type', 191)->nullable();
            $table->string('name', 191);
            $table->string('description', 191)->nullable();
            $table->string('image', 191)->nullable();
            $table->integer('qty')->nullable();
            $table->string('condition', 191)->nullable();
            $table->string('date_of_purchase', 191)->nullable();
            $table->string('warranty_date', 191)->nullable();
            $table->string('date_of_expiry', 191)->nullable();
            $table->integer('location_id')->nullable();
            $table->integer('cabinet_id')->nullable();
            $table->integer('drawer_id')->nullable();
            $table->timestamps();
        });

        // 4. Transactional/Relational Tables
        Schema::create('damages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('item_id');
            $table->unsignedInteger('user_id');
            $table->string('image', 191)->nullable();
            $table->integer('qty');
            $table->date('date');
            $table->string('remarks', 191)->nullable();
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
        });

        Schema::create('item_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('item_id');
            $table->unsignedInteger('user_id');
            $table->string('message', 191)->nullable();
            $table->timestamps();
        });

        Schema::create('item_purchase', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('item_id');
            $table->unsignedInteger('supplier_id');
            $table->unsignedInteger('user_id');
            $table->integer('qty');
            $table->date('date');
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });

        Schema::create('item_sale', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('item_id');
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('user_id');
            $table->integer('qty');
            $table->date('date');
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });

        // 5. System Tables
        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email', 191)->index();
            $table->string('token', 191);
            $table->timestamp('created_at')->nullable();
        });

        // 6. Default Data Seed
        DB::table('users')->insert([
            [
                'name' => 'Admin',
                'email' => 'admin@mail.com',
                'password' => bcrypt('admin1234'),
                'created_at' => now(),
                'updated_at' => now(),
                'role' => 'admin'
            ],
            [
                'name' => 'Staff',
                'email' => 'staff@mail.com',
                'password' => bcrypt('staff1234'),
                'created_at' => now(),
                'updated_at' => now(),
                'role' => 'staff'
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        // Drop in reverse order to respect foreign key constraints

        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('item_sale');
        Schema::dropIfExists('item_purchase');
        Schema::dropIfExists('item_logs');
        Schema::dropIfExists('damages');
        Schema::dropIfExists('items');
        Schema::dropIfExists('drawers');
        Schema::dropIfExists('cabinets');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('recipes');
        Schema::dropIfExists('groceries');
        Schema::dropIfExists('item_types');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('users');
    }
};
