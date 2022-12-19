<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblNews extends Migration {

  static $tbl = 'tbl_news';

  public static function up(){
      /*
       * Bảng tin
       * */
      Schema::create(self::$tbl, function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->integer('uid')->default(1);
        $table->integer('topic_id'); // id chủ đề (category)
        $table->text('title', 300)->nullable();
        $table->text('img_path')->nullable(); // ảnh đại diện cho bài viết
        $table->text('description')->nullable(); // miêu tả (giới thiệu ngắn)
        $table->text('content')->nullable(); // nội dung
        $table->text('key_words')->nullable(); // từ khóa để seo
        $table->integer('viewed')->default(0)->nullable(); // Số lượt xem bảng tin
        $table->integer('curent_visit')->default(0)->nullable(); // Số người đang xem bảng tin
        $table->boolean('is_pin')->default(false); // có ghim vào trang chủ hay không
        $table->boolean('is_hot')->default(false); // 
        $table->boolean('is_new')->default(false); // 
        $table->text('type')->nullable();
        $table->text('integration_id')->nullable();
        $table->text('channel_name')->nullable();
        
        $table->bigInteger('create_by_id'); // id người tạo
        $table->bigInteger('update_by_id')->nullable(); // id Người sửa cuối
        $table->timestamps();
    });
  }

  public static function down(){
    Schema::dropIfExists(self::$tbl);
  }
}