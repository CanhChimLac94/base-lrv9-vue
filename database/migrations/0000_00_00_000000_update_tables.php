<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// use Illuminate\Support\Carbon;
// use Illuminate\Support\Facades\DB;

require 'add-factor-columns-users.php';

class UpdateTables extends Migration
{
    public function up()
    {

        // Schema::table('tbl_news', function (Blueprint $table) {
        //     //$table->dropColumn('uid');
        //     $table->integer('uid')->default(1)->nullable(); // Mã người tạo
        // });

        // Schema::table('tbl_products', function (Blueprint $table) {
        //     //$table->dropColumn('uid');
        //     $table->integer('uid')->default(1)->nullable(); // Mã người tạo
        // });
        AddFactorColumnsUser::up();
    }

    public function down()
    {
        AddFactorColumnsUser::down();
    }
}
