<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblCompanyInfor extends Migration {

  static $tbl = 'tbl_company_infor';

  public static function up(){
    /*
     * Bảng thông tin website (tổ chức)
     */
     Schema::create(self::$tbl, function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->text('name')->nullable(); // Tên website
        $table->text('address')->nullable(); // Địa chỉ
        $table->text('logo_path')->nullable(); // đường dẫn logo web
        $table->text('icon_path')->nullable(); // Đường dẫn icon
        $table->text('_phone', 13)->nullable(); // Số điện thoại
        $table->text('hotline')->nullable(); // Đường dây nóng
        $table->text('fax')->nullable(); // Số fax
        $table->text('_email')->nullable(); // _email liên hệ
        $table->integer('mail_port')->default(25); // Cổng gửi mail
        $table->text('facebook')->nullable(); // Địa chỉ fb
        $table->text('code_tax')->nullable(); // MÃ số thuế
        $table->text("introduct")->nullable(); // Giới thiệu
        $table->timestamps();
    });
  }

  public static function down(){
    Schema::dropIfExists(self::$tbl);
  }
}