<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSerialNumberToItemsTable extends Migration {

    public function up() {
        Schema::table('items', function (Blueprint $blueprint) {
            $blueprint->string('serial_number')->unique()->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('items', function (Blueprint $blueprint) {
            $blueprint->dropColumn('serial_number');
        });
    }
}
