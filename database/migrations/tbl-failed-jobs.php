<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblFailedJob extends Migration
{
    static $tbl = 'tbl_failed_jobs';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public static function up()
    {
        Schema::create(self::$tbl, function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public static function down()
    {
        Schema::dropIfExists(self::$tbl);
    }
};
