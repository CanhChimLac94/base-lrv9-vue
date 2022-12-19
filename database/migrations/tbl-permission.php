<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblPermission extends Migration {

  static $tbl = 'tbl_permissions';

  public static function up(){
     /**
     * Bảng quyền hạn (được phép làm gì)
     * Eg: edit_user, edit_menu
     */
    Schema::create(self::$tbl, function (Blueprint $table) {
        $table->increments('id');
        $table->text('name', 600); // Tên nhóm người dùng
        $table->integer('value')->nullable(); // 
        $table->text('note')->nullable(); // Ghi chú
        $table->timestamps();
    });
  }

  public static function down(){
    Schema::dropIfExists(self::$tbl);
  }
}