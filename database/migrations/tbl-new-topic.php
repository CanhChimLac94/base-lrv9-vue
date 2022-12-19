<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblNewsTopic extends Migration {

  static $tbl = 'tbl_new_topics';

  public static function up(){
      Schema::create(self::$tbl, function (Blueprint $table) {
        $table->increments('id');
        $table->integer('parent_id'); // 
        $table->boolean('has_sub');
        $table->string('thumbnail');
        $table->string('type');
        $table->string('code', 20)->nullable(); // Mã loại
        $table->string("name", 350)->nullable(); // Tên loại
        $table->boolean('is_active')->default(1); // trạng thái hoạt động
        $table->text("notes")->nullable(); // chú thích
        $table->timestamps();
    });
  }

  public static function down(){
    Schema::dropIfExists(self::$tbl);
  }
}