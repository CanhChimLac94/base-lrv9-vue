<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblRoleMenu extends Migration {

  static $tbl = 'tbl_role_menu';

  public static function up(){
    /*
     *  Bảng Menu và Nhóm quyền --> xác định các menu mà nhóm quyền dc phép
     * */
    Schema::create(self::$tbl, function (Blueprint $table) {
        $table->increments('id');
        $table->integer('role_id'); // id của nhóm quyền
        $table->integer('menu_id'); // id của menu
        $table->text('note')->nullable(); // Ghi chú
        $table->bigInteger('create_by_id')->nullable(); // id người tạo
        $table->bigInteger('update_by_id')->nullable(); // id của người sửa cuối
        $table->timestamps();
    });
  }

  public static function down(){
    Schema::dropIfExists(self::$tbl);
  }
}