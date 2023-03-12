<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblProductCategory extends Migration {

  static $tbl = 'tbl_product_category';

  public static function up(){
    /**
     * Loại sản phẩm
     */
    Schema::create(self::$tbl, function (Blueprint $table) {
        $table->increments('id');
        $table->string('code', 20)->nullable(); // Mã loại
        $table->string("name", 150)->nullable(); // Tên loại
        $table->boolean('is_active')->default(1); // trạng thái hoạt động
        $table->text("notes")->nullable(); // chú thích
        $table->timestamps();
    });
  }

  public static function down(){
    Schema::dropIfExists(self::$tbl);
  }
}