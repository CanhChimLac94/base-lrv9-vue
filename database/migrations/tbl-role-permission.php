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
        $table->integer('permission_id')->nullable(); // id quyền hạn
        $table->integer('role_id')->nullable(); // id vai trò
        $table->integer('is_enable')->nullable()->default(1); // 
        $table->text('note')->nullable(); // Ghi chú
        $table->timestamps();
    });

    self::setDefaultData();
  }

  public static function down(){
    Schema::dropIfExists(self::$tbl);
  }

  static function default_permission()
  {
      $roles = [];
      $basePermistion = [
        "admin" => "admin",
        "admin_view" => "adminview",
        "admin_view_root_menu" => "admin_view_root_menu",
        "admin_view_system_menu" => "admin_view_system_menu",
        "read_file_server" => "read_file_server",
        "edit_file_server" => "read_file_server",
      ];
      foreach ($basePermistion as $key => $p) {
          $roles[] = [
              "name" => $p,
              "value" => 0
          ];
      }
      return $roles;
  }

  static function setDefaultData(){

    DB::table(self::$tbl)->insert(self::default_permission());
      
  }
}