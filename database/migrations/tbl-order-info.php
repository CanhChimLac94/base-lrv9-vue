<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TblOederInfo extends Migration {

  static $tbl = 'tbl_order_info';

  public static function up(){
      /**
       * (Đơn hàng-hóa đơn thực tế)
       */
      Schema::create(self::$tbl, function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->string("oder_code", 20)->nullable(); // mã đơn hàng = ctm_code
        $table->bigInteger("user_id")->nullable(); // id khác hàng (== null --> khác vãng lai)
        $table->string("client_name", 200)->default(false); // tên khách hàng
        $table->boolean("order_status")->default(false); // trạng thái đơn hàng
        $table->boolean("shipping_status")->default(false); // 
        $table->boolean("pay_status")->default(false);
        $table->string("consignee", 200)->default(false); // người nhận hàng
        $table->integer("country")->nullable(); // Mã quốc gia
        $table->integer("province")->nullable(); // mã tỉnh thành
        $table->integer("city")->nullable(); // mã thành phố
        $table->integer("district")->nullable(); // mã huyện
        $table->string("address", 500)->nullable(); // địa chỉ nhà (địa chỉ giao hàng)
        $table->integer("zipcode")->nullable(); // mã bưu điện
        $table->string("tel", 20)->nullable(); // điện thoại cố định
        $table->string("mobile", 20)->nullable(); // điện thoại di động khách hàng
        $table->string("email", 20)->nullable(); // email
        $table->string("best_time", 150)->nullable(); 
        $table->integer("shipper_id")->nullable(); // người giao hàng
        $table->string("shipper_name", 300)->nullable(); // tên người giao hàng
        $table->integer("pay_id")->nullable(); // phương thức thanh toán
        $table->string("pay_name", 200)->nullable(); // tên thương thức thanh toán
        $table->string("pack_name", 150)->nullable(); // tên gói
        $table->string("card_name", 150)->nullable(); // tên thẻ
        $table->string("card_message", 500)->nullable(); // thông tin thẻ
        $table->string("inv_payee", 150)->nullable(); // mời người dc trả tiền
        $table->string("inv_content", 250)->nullable(); // nội dung mời
        $table->double("product_amount")->nullable(); // giá trị hàng hóa
        $table->double("shipping_fee")->nullable(); // hố trợ phí ship
        $table->double("insure_fee")->nullable(); // phí bảo hiểm (đặt cọc)
        $table->double("pay_fee")->nullable(); // trả phí (khoản khuyến mại)
        $table->double("pack_fee")->nullable(); // phí đóng gói
        $table->double("card_fee")->nullable(); // phí thẻ
        $table->double("money_paid")->nullable(); // tiền đã trả
        $table->double("surplus")->nullable(); // số dư
        $table->double("bonus")->nullable(); // tiền thưởng
        $table->double("order_amount")->nullable(); // giá trị đơn hàng (đã bao gồm thuế)
        $table->double("discount")->nullable(); // giảm giá
        $table->double("tax")->nullable(); // tiền thuế
        $table->string("referer", 300)->nullable(); // người giới thiệu
        $table->integer("confirm_time")->nullable(); // thời gian xác thực đơn hàng (đơn hàng đã hoàn thành)
        $table->integer("pay_time")->nullable(); // thời gian thanh toán
        $table->integer("shipping_time")->nullable(); // thời gian giao hàng (trong bao lâu)
        $table->string("invoice_no", 300)->nullable(); // số hóa đơn
        $table->string("invoice_type", 300)->nullable(); // loại hóa đơn
        $table->string("to_buyer", 300)->nullable(); // cho người mua
        $table->string("pay_note", 300)->nullable(); // ghi chú thanh toán
        $table->integer("agency_id")->nullable(); // mã cơ quan
        $table->boolean("is_separate")->nullable(); // là riêng biệt
        $table->timestamps();
    });
  }

  public static function down(){
    Schema::dropIfExists(self::$tbl);
  }
}