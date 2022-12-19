<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblOrderAction extends Migration {

  static $tbl = 'tbl_order_action';

  public static function up(){
      /**
       * (tình trạng đơn hàng)
       */
      Schema::create(self::$tbl, function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->bigInteger("action_id")->nullable();
        $table->string("action_user", 30)->nullable();
        $table->boolean("order_status")->nullable();
        $table->boolean("shipping_status")->nullable();
        $table->boolean("pay_status")->nullable();
        $table->boolean("action_place")->nullable();
        $table->text("action_note")->nullable();
        $table->timestamps();
    });
  }

  public static function down(){
    Schema::dropIfExists(self::$tbl);
  }
}