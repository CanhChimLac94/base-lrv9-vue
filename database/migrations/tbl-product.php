<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblProduct extends Migration {

  static $tbl = 'tbl_products';

  public static function up(){
    /**
     * Sản phẩm
     */
    Schema::create(self::$tbl, function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->integer('uid')->default(1)->nullable(); // Mã người tạo
        $table->string('code', 20)->nullable(); // Mã sản phẩm
        $table->integer('category_id')->default(0)->nullable(); // Mã loại
        $table->integer('unit_id')->default(0)->nullable(); // Mã đơn vị
        $table->text('name', 500)->nullable(); // Tên gọi của Sản phẩm
        $table->text('imgs')->nullable(); // Hình ảnh của sản phẩm
        $table->text('properties')->nullable(); // Thuộc tính của sản phẩm

        $table->double('in_price')->nullable(); // giá nhập
        $table->double('out_price')->nullable(); // Gía bán
        $table->double('promote_price')->nullable(); // gía khuyến mại
        $table->double('promote_rate')->nullable(); // khuyến mại (%)
        
        $table->boolean("is_hot")->nullable();
        $table->boolean("is_new")->nullable();
        $table->boolean("is_promote")->nullable(); // khyến mại
        $table->boolean("is_active")->nullable(); // sản phẩm còn bán hay không
        $table->integer('viewed')->default(0)->nullable(); // số lượt đã xem
        $table->integer("curentVisit")->default(0)->nullable(); // Số lượt đang xem
        // $table->integer('CountNumber')->default(0)->nullable();
        $table->integer('quantity_sold')->default(0)->nullable(); // Số lượng đã bán
        $table->integer('inventory')->default(0)->nullable(); // tổng kho

        $table->text("detail")->nullable(); // chi tiết sản phẩm (cấu trúc json)
        $table->text("notes")->nullable(); // mô tả (giới thiệu chung)

        $table->text('keywords')->nullable(); // từ khóa tìm kiếm
        $table->text('tags')->nullable(); // tư khóa để seo
        $table->timestamps();
    });
  }

  public static function down(){
    Schema::dropIfExists(self::$tbl);
  }
}