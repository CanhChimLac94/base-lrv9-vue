<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblUserSupport extends Migration {

  static $tbl = 'tbl_user_support';

  public static function up(){
    /*
     * Bảng đăng ký trở thành cộng tác viên của người dùng
     * (User suoport)
     */
    Schema::create(self::$tbl, function (Blueprint $table) {
        $table->increments('id');
        $table->integer('user_id');
        /*
         * 0: lock
         * 1: cờ kích hoạt
         * 2: đã kích hoạt thành công
         */
        $table->integer('status')->default(1)->nullable();
        $table->string('avatar_path', 600)->nullable(); // ảnh lấy từ camera
        $table->string('img_pass_post', 600)->nullable(); // ảnh cmnd
        /*
         * Cấu trúc
         * [
         *      {degree: "đường dẫn tới ảnh chúng chỉ 1"},
         *      {degree: "đường dẫn tới ảnh chứng chỉ 2"},
         *      ...
         * ]
         */
        $table->text('img_degrees')->nullable(); // các ảnh bằng cấp, chứng chỉ
        $table->text('introduc')->nullable(); // Giới thiệu
        $table->timestamps();
    });
  }

  public static function down(){
    Schema::dropIfExists(self::$tbl);
  }
}