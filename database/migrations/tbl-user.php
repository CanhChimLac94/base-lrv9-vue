<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TblUser extends Migration {

  static $tbl = 'tbl_users';

  public static function up(){
    /*
     * Tài khoản của người dùng
     */
    Schema::create(self::$tbl, function (Blueprint $table) {
        $table->string('name');
        
        $table->bigIncrements('id');
        $table->integer('role_id')->nullable(); // id nhóm quyền
        $table->text('user_name'); // tên đăng nhập
        $table->text('email', 500)->nullable(); // email
        $table->text('password')->nullable(); // Mật khẩu
        $table->text('phone')->nullable(); // Điện thoại
        
        $table->timestamp('email_verified_at')->nullable();
        $table->foreignId('current_team_id')->nullable();
        $table->string('profile_photo_path', 2048)->nullable();
        /*
         * user infor
         * */
        $table->text('full_name')->nullable(); // họ tên
        $table->text('birthday')->nullable(); // Ngày sinh
        $table->binary('sex')->nullable(); // giới tính
        $table->text('address')->nullable(); // Địa chỉ
        $table->text('avatar_path')->nullable(); // Đường dẫn ảnh đại diện
        $table->text('active_code')->nullable(); // Mã kíc hoạt tài khoản
        $table->text('pass_port')->nullable(); // CMT/Hộ chiếu
        $table->string('country', 500)->default('Việt Nam')->nullable(); // Quốc tịch

        $table->double('coins')->nullable(); // Số dư tài khoản
        $table->text('infor')->nullable(); // Giới thiệu
        $table->binary('status')->nullable(); // Trạng thái kích hoạt (xác định trạng thái xác thực tài khoản)
        $table->dateTimeTz('last_visited')->nullable(); // Lần đăng nhập cuối

        $table->boolean("is_edit")->default(true);

        $table->rememberToken();
        $table->timestamps();
    });

    self::setDefaultData();
  }

  public static function down(){
    Schema::dropIfExists(self::$tbl);
  }

  static function user_temp($uname, $pass, $role_id, $full_name = "Trần Văn Dũng"){
      return [
        "name" => $uname,
        "user_name" => $uname,
        "email" => "canhchimlac.94@gmail.com",
        "password" => bcrypt($pass),
        "full_name" => $full_name,
        "avatar_path" => "public/upload/images/avatar/user_icon_default.png",
        "sex" => 1,
        "role_id" => $role_id,
        "is_edit" => 0,
        "created_at" => new Carbon,
        "updated_at" => new Carbon,
      ];
  }

  static function setDefaultData(){
      $default_user = [
        self::user_temp("administrator", "canhchimlac@94", 1, "Trần Văn Dũng-administrator"),
        self::user_temp("admin", "canhchimlac@94", 2, "Trần Văn Dũng-admin"),
      ];
      
      DB::table(self::$tbl)->insert($default_user);
  }

}