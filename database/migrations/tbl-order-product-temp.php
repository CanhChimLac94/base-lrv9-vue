<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblOderProductTemp extends Migration {

  static $tbl = 'tbl_order_product_temp';

  public static function up(){
      /**
       * 
       */
      Schema::create(self::$tbl, function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->bigInteger("order_id")->nullable();
        $table->bigInteger("product_id")->nullable();
        $table->string("product_code", 20)->nullable();
        $table->string("product_name", 250)->nullable();
        $table->integer("count")->nullable();
        $table->double("in_price")->nullable(); // giá nhập
        $table->double("promote_rate")->nullable(); // khuyến mại %
        $table->double("promote_price")->nullable(); // giá bán thực tế
        $table->string("imgs")->nullable();
        $table->timestamps();
    });
  }

  public static function down(){
    Schema::dropIfExists(self::$tbl);
  }
}