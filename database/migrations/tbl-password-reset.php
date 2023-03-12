<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblPasswordReset extends Migration
{

  static $tbl = 'tbl_password_reset';

  public static function up()
  {
    Schema::create(self::$tbl, function (Blueprint $table) {
      $table->string('email')->index();
      $table->string('token');
      $table->timestamp('created_at')->nullable();
    });
  }

  public static function down()
  {
    Schema::dropIfExists(self::$tbl);
  }
}