<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTrackableFieldToItemsTable extends Migration {

    public function up() {
        Schema::table('items', function (Blueprint $blueprint) {
            $blueprint->enum('trackable', ['Yes', 'No'])->default('No');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('items', function (Blueprint $blueprint) {

            $blueprint->dropColumn('trackable');
        });
    }
}
