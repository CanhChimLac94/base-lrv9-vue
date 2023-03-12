<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class TblRolePermission extends Migration {

  static $tbl = 'tbl_role_permissions';

  public static function up(){
    /**
     * quan hệ giữa vai trò và quyền hạn
     */
    Schema::create(self::$tbl, function (Blueprint $table) {
        $table->increments('id');
        $table->text('name')->nullable(); // Tên
        $table->integer('permission_id')->nullable(); // id quyền hạn
        $table->integer('role_id')->nullable(); // id vai trò
        $table->integer('is_enable')->nullable()->default(1); // 
        $table->text('note')->nullable(); // Ghi chú
        $table->timestamps();
    });
  }

  public static function down(){
    Schema::dropIfExists(self::$tbl);
  }

}