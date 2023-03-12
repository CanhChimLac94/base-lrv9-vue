<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblCart extends Migration {

  static $tbl = 'tbl_cart';

  public static function up(){
      /**
       * Giỏ hàng
       */
      Schema::create(self::$tbl, function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->string('cart_c ode', 20)->nullable(); // mã đơn hàng
        $table->integer('user_id')->nullable(); // id của khách hàng
        $table->string('secssion_id', 50)->nullable(); // id phiên đăng nhập
        // $table->text("status")->nullable(); // trạng thái đơn hàng (đang chuyển, đã nhận, hoàn thành)
        // $table->boolean('is_shipping')->nullable(); // đang chuyển hàng
        $table->double("can_handsel")->nullable(); // đặt cọc

        $table->timestamps();
    });
  }

  public static function down(){
    Schema::dropIfExists(self::$tbl);
  }
}