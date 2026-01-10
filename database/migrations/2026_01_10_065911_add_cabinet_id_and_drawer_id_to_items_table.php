<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCabinetIdAndDrawerIdToItemsTable extends Migration {

    public function up() {
        Schema::table('items', function (Blueprint $blueprint) {
            $blueprint->string('cabinet_id')->nullable()->after('qty');
            $blueprint->string('drawer_id')->nullable()->after('cabinet_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('items', function (Blueprint $blueprint) {
            $blueprint->dropColumn('cabinet_id');
            $blueprint->dropColumn('drawer_id');
        });
    }
}
