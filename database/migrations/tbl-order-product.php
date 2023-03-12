<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblOrderProduct extends Migration {

  static $tbl = 'tbl_order_product';

  public static function up(){
      /**
       * 
       */
      Schema::create(self::$tbl, function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->bigInteger('rec_id')->nullable();
        $table->bigInteger('order_id')->nullable(); // id hóa đơn
        $table->bigInteger('product_id')->nullable(); // id sản phẩm
        $table->string('product_code', 20)->nullable(); // mã sản phẩm
        $table->string('product_name', 500)->nullable(); // tên sản phẩm
        $table->integer('product_count')->nullable(); // số lượng bán theo đơn hàng
        $table->double("in_price")->nullable(); // giá nhập thực tế
        $table->double("promote_rate")->nullable(); // tỉ lệ khuyến mãi %
        $table->double("promote_price")->nullable(); // giá bán thực tế tại thời điểm bán
        $table->boolean("is_gift")->nullable()->default(false); // sản phẩm là quà tặng
        $table->timestamps();
    });
  }

  public static function down(){
    Schema::dropIfExists(self::$tbl);
  }
}