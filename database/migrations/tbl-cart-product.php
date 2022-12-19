<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblCartProduct extends Migration {

  static $tbl = 'tbl_cart_product';

  public static function up(){
    Schema::create(self::$tbl, function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger("cart_id")->nullable(); // id gior hàng
      $table->integer('product_id')->nullable(); // id sản phẩm
      $table->string('product_code')->nullable(); // mã sản phẩm
      $table->text("product_name", 500)->nullable(); // tên sản phẩm
      $table->double("market_price")->nullable(); // giá bán
      $table->boolean("is_gift")->nullable(); // là hàng khuyến mại

      $table->timestamps();
  });
  }

  public static function down(){
    Schema::dropIfExists(self::$tbl);
  }
}