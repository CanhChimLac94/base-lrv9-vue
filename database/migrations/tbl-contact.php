<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblContact extends Migration {

  static $tbl = 'tbl_contact';

  public static function up(){
      /*
       * ý kiến của người dùng gửi tới hệ thống
       */
      Schema::create(self::$tbl, function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->text('full_name')->nullable(); // họ tên người dùng
        $table->text('email')->nullable(); // email người dùng
        $table->text('phone')->nullable(); // số điện thoại của người dùng
        $table->text('contents')->nullable(); // Nội dung
        $table->boolean('is_show')->nullable(); // Cho phép hiển thị trên trang chủ
        $table->boolean('is_checked')->default(false)->nullable(); // 
        $table->text('temp')->nullable(); // 
        $table->timestamps();
    });
  }

  public static function down(){
    Schema::dropIfExists(self::$tbl);
  }
}