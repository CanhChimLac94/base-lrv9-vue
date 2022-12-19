<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class TblMenu extends Migration {

  static $tbl = 'tbl_menus';

  public static function up(){
    /*
     * Bảng menu
     * */
    Schema::create(self::$tbl, function (Blueprint $table) {
      $table->increments('id');
      $table->integer('parent_id')->nullable(); // default = Id (ko có menu cha)
      $table->boolean('has_sub')->default(false)->nullable(); // kiểm tra có menu con hay không
      $table->boolean('enable')->default(false)->nullable();
      // Loại menu
      // [
      //  0: Trang chủ;
      //  1: Trang quản trị;
      //  2: Khác
      // ]
      $table->integer('type')->default(0);
      $table->text('url')->nullable(); // đường dẫn menu chỉ tới
      $table->text('name', 500)->nullable(); // tên menu
      $table->integer('order')->default(0)->nullable();
      $table->text('display_name')->nullable(); // Tên hiển thị
      $table->text('icon', 300)->nullable(); // icon
      $table->text('comment')->nullable();
      $table->timestamps();
    });

    self::setDefaultData();
  }

  public static function down(){
    Schema::dropIfExists(self::$tbl);
  }

  static function default_admin_menu(){
    return [
        menu_temp("Sản phẩm", "admin/products"),
        menu_temp("Đơn hàng", "admin/orders"),
        menu_temp("Bài viết", "admin/news"),
        menu_temp("Danh mục-Bài viết", "admin/newTopics"),
        menu_temp("Phản hồi", "admin/contact"),
    ];
  }

  static function setDefaultData(){
      DB::table('tbl_menus')->insert(self::default_admin_menu());
      DB::table('tbl_menus')->update([
          "parent_id" => DB::raw("id")
      ]);
  }
}