<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('serial_number')->unique()->nullable();
            $table->integer('category_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('instructions')->nullable();
            $table->string('condition')->nullable();
            $table->string('location')->nullable();
            $table->integer('price')->nullable();
            $table->string('image')->nullable();
            $table->integer('qty')->nullable();
            $table->enum('trackable', ['Yes', 'No'])->default('No');
            $table->integer('cabinet_id')->nullable();
            $table->integer('drawer_id')->nullable();
            $table->timestamps();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('items');
    }
}
