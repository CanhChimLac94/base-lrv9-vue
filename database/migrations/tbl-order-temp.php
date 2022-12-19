<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblOrderTemp extends Migration {

  static $tbl = 'tbl_order_temp';

  public static function up(){
      /**
       * 
       */
      Schema::create(self::$tbl, function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->string("ctm_code", 250)->nullable();
        $table->string("ctm_name", 250)->nullable();
        $table->string("ctm_email", 250)->nullable();
        $table->string("ctm_phone", 15)->nullable();
        $table->string("ctm_address", 250)->nullable();
        $table->string("ctm_city", 250)->nullable();
        $table->string("ctm_district", 250)->nullable();
        $table->string("ctm_note", 250)->nullable();
        $table->double("ship_price")->nullable(); // giá nhập
        $table->string("temp")->nullable();
        $table->string("status")->nullable();
        $table->boolean("processed")->default(false)->nullable(); 
        $table->timestamps();
    });
  }

  public static function down(){
    Schema::dropIfExists(self::$tbl);
  }
}