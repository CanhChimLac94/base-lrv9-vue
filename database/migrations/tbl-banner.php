<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblBanner extends Migration {

  static $tbl = 'tbl_banner';

  public static function up(){
     /*
     * Banner
     * */
      Schema::create(self::$tbl, function (Blueprint $table) {
        $table->increments('id');
        $table->text('title')->nullable(); // Tiêu đề
        $table->text('img_path')->nullable(); // Đường dẫn ảnh
        $table->text('Url')->nullable(); // Link hướng tới
        $table->boolean('active')->nullable();
        $table->text('notes')->nullable(); // Ghi chú
        $table->timestamps();
    });
  }

  public static function down(){
    Schema::dropIfExists(self::$tbl);
  }
}