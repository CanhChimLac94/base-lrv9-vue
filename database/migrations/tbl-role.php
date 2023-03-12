<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class TblRole extends Migration {

  static $tbl = 'tbl_roles';

  public static function up(){
    /*
     * Bảng vai trò
     * eg: Administrator, admin, client ...
     * */
    Schema::create(self::$tbl, function (Blueprint $table) {
        $table->increments('id');
        $table->text('name', 600); // Tên nhóm người dùng
        $table->integer('value')->nullable(); // 
        $table->text('note')->nullable(); // Ghi chú
        $table->timestamps();
    });

    self::setDefaultData();
  }

  public static function down(){
    Schema::dropIfExists(self::$tbl);
  }

  static function default_role(){
    $roles = [];
    $baseRole = [
      "administrator" => "administrator",
      "admin" => "admin",
      "sub_admin" => "sub_admin",
      "client" => "client"
    ];
    foreach($baseRole as $key=>$r){
        $roles[] = [
            "name" => $r,
            "value" => 0
        ];
    }
    return $roles;
  }

  static function setDefaultData(){
    DB::table(self::$tbl)->insert(self::default_role());      
  }
}