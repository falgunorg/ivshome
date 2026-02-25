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
            $table->softDeletes();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 191);
            $table->text('address')->nullable();
            $table->string('email', 191)->nullable();
            $table->string('phone', 191)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 191);
            $table->text('address')->nullable();
            $table->string('email', 191)->nullable();
            $table->string('phone', 191)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('item_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 191);
            $table->string('code', 191)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('groceries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('serial_number', 191)->nullable();
            $table->string('name', 255);
            $table->string('bengali_name', 255);
            $table->decimal('qty', 10, 2);
            $table->string('unit', 191);
            $table->string('category', 191)->nullable();
            $table->string('image', 191)->nullable();
            $table->integer('min_stock')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('grocery_requisitions', function (Blueprint $table) {
            $table->id();
            $table->string('requisition_no')->unique();
            $table->unsignedInteger('user_id');
            $table->date('requested_date');
            $table->enum('status', ['pending', 'approved', 'partial', 'completed'])->default('pending');
            $table->unsignedInteger('approved_by')->nullable(); // Add ->nullable()
            $table->date('approved_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('grocery_requisition_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grocery_requisition_id');
            $table->unsignedInteger('grocery_id');
            $table->decimal('qty_requested', 10, 2);
            $table->decimal('qty_received', 10, 2)->default(0); // Tracks progress
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('grocery_requisition_id')->references('id')->on('grocery_requisitions')->onDelete('cascade');
            $table->foreign('grocery_id')->references('id')->on('groceries');
        });

        Schema::create('grocery_receives', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('grocery_requisition_item_id'); // Link to the specific line
            $table->unsignedInteger('grocery_id');
            $table->decimal('received_qty', 10, 2);
            $table->decimal('current_stock', 10, 2);
            $table->date('purchase_date');
            $table->date('expiry_date');
            $table->string('lot_number')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('grocery_requisition_item_id')->references('id')->on('grocery_requisition_items');
            $table->foreign('grocery_id')->references('id')->on('groceries');
        });

        Schema::create('grocery_issues', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('grocery_id');
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('receive_id')->nullable(); // Optional: Link to specific batch (FIFO)
            $table->decimal('issued_qty', 10, 2);
            $table->date('issue_date');
            $table->string('issued_to')->nullable(); // Department or Person
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('grocery_id')->references('id')->on('groceries');
            $table->foreign('receive_id')->references('id')->on('grocery_receives');
        });

        Schema::create('recipes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 191);
            $table->text('instructions');
            $table->text('note')->nullable();
            $table->string('image', 191)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Structural Tables
        Schema::create('locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->string('name', 191)->nullable();
            $table->string('code', 191)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cabinets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('location_id');
            $table->unsignedInteger('user_id');
            $table->string('title', 191)->nullable();
            $table->string('code', 191)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('drawers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('cabinet_id');
            $table->string('title', 191);
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Core Items Table
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('serial_number', 191)->nullable();
            $table->unsignedInteger('user_id');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
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
            $table->softDeletes();
        });

        // 4. Transactional/Relational Tables
        Schema::create('damages', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('item_id');
            $table->unsignedInteger('user_id');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('image', 191)->nullable();
            $table->integer('qty');
            $table->date('date');
            $table->string('remarks', 191)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
        });

        Schema::create('item_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('item_id');
            $table->unsignedInteger('user_id');
            $table->string('message', 191)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('item_purchase', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('item_id');
            $table->unsignedInteger('supplier_id');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedInteger('user_id');
            $table->integer('qty');
            $table->date('date');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });

        Schema::create('item_sale', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('item_id');
            $table->unsignedInteger('customer_id');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedInteger('user_id');
            $table->integer('qty');
            $table->date('date');
            $table->timestamps();
            $table->softDeletes();
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
                'name' => 'Mustafa Mahmud',
                'email' => 'md',
                'password' => bcrypt('F@1gunHome@!@#'),
                'created_at' => now(),
                'updated_at' => now(),
                'role' => 'admin'
            ],
            [
                'name' => 'Omar Faruk',
                'email' => 'faruk',
                'password' => bcrypt('F@1gunHome@!@#'),
                'created_at' => now(),
                'updated_at' => now(),
                'role' => 'admin'
            ],
            [
                'name' => 'Nasir',
                'email' => 'nasir',
                'password' => bcrypt('nasir'),
                'created_at' => now(),
                'updated_at' => now(),
                'role' => 'staff'
            ],
            [
                'name' => 'Al Amin',
                'email' => 'alamin',
                'password' => bcrypt('alamin'),
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
        Schema::dropIfExists('grocery_issues');
        Schema::dropIfExists('grocery_receives');
        Schema::dropIfExists('grocery_requisitions');
        Schema::dropIfExists('grocery_requisition_items');
        Schema::dropIfExists('item_types');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('users');
    }
};
