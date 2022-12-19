<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

require 'tbl-banner.php';
require 'tbl-company-infor.php';
require 'tbl-cart-product.php';
require 'tbl-cart.php';
require 'tbl-contact.php';
require 'tbl-menu.php';
require 'tbl-new-topic.php';
require 'tbl-news.php';
require 'tbl-order-action.php';
require 'tbl-order-info.php';
require 'tbl-order-product-temp.php';
require 'tbl-order-product.php';
require 'tbl-order-temp.php';
require 'tbl-password-reset.php';
require 'tbl-permission.php';
require 'tbl-product-category.php';
require 'tbl-product.php';
require 'tbl-role-menu.php';
require 'tbl-role-permission.php';
require 'tbl-role.php';
require 'tbl-user-support.php';
require 'tbl-user.php';

class CreateTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        TblCompanyInfor::up();
        TblBanner::up();
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        TblCompanyInfor::down();
    }
}
