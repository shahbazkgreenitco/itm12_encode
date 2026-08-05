<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * KEY FIX - MySQL prefix length rules:
     * ✅ VARCHAR  → prefix allowed e.g. column(50)
     * ❌ ENUM     → prefix NOT allowed (causes Error 1089) — use NO prefix
     * ❌ INT      → prefix NOT allowed
     * ❌ TINYINT  → prefix NOT allowed
     * ❌ DATE     → prefix NOT allowed
     * ❌ DECIMAL  → prefix NOT allowed
     */
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {

            /* ===================== COLUMNS ===================== */
            $table->increments('id');

            $table->string('name')->nullable();
            $table->string('asset_tag');
            $table->integer('model_id');
            $table->string('serial')->nullable();
            $table->string('uuid', 100)->nullable()->comment('Unique Device I');

            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 13, 4)->default(0.0000);
            $table->string('order_number')->nullable();

            $table->integer('assigned_to')->nullable();
            $table->tinyInteger('assigned_for')->nullable()->comment('1 - User, 1 - Place');

            $table->integer('chkin_log_id')->nullable()->comment('current checkout log id');
            $table->integer('chkout_log_id')->nullable()->comment('current checkout log id');

            $table->text('notes')->nullable();
            $table->text('image')->nullable();

            $table->integer('user_id');
            $table->timestamps();
            $table->boolean('physical')->default(true);
            $table->softDeletes();

            $table->integer('status_id')->nullable();
            $table->boolean('archived')->default(false);

            $table->integer('warranty_months')->nullable();
            $table->date('warrenty_end_date')->nullable()->comment('Warrenty End Date');
            $table->date('warranty_start_date')->nullable()->comment('Warrenty Start Date');

            $table->boolean('depreciate')->default(false);
            $table->integer('supplier_id')->nullable();
            $table->tinyInteger('requestable')->default(0);

            $table->integer('rtd_location_id')->nullable();
            $table->integer('internal_place_id')->nullable();
            $table->integer('ni_detected_location')->nullable()->comment('To note location of asset by mapped ip');

            $table->enum('accepted', ['pending', 'accepted', 'rejected', 'declined'])
                  ->nullable()
                  ->comment('Hold status about current acceptable checkout');

            $table->dateTime('accept_link_send_at')->nullable()->comment('last accept link sent date & time');
            $table->integer('chkout_acceptance_log_id')->nullable()->comment("user's response log id about accept/rejected");

            $table->dateTime('last_checkout')->nullable();
            $table->date('expected_checkin')->nullable();

            $table->unsignedInteger('company_id')->nullable();
            $table->string('purchase_currency', 10)->nullable();

            $table->integer('amc_supplier_id')->nullable();
            $table->date('amc_expire_date')->nullable();

            $table->tinyInteger('high_pririty')->nullable();
            $table->integer('invoice_id')->nullable()->comment('Record from purchases table');
            $table->tinyInteger('added_from')->nullable()->default(1)->comment('1-Manual,2-Network Inventory');

            $table->string('ip', 50)->nullable();
            $table->string('mac', 60)->nullable();

            $table->tinyInteger('device_occure_type')->nullable()->comment('1-Device collected by lease contract');
            $table->integer('lease_id')->nullable();
            $table->integer('stock_place')->nullable()->comment('Internal Place ID');
            $table->integer('last_checkout_project')->nullable()->comment('ID from Project table');

            $table->integer('asset_owner')->nullable();
            $table->integer('sold_by')->nullable();
            $table->date('sold_at')->nullable();
            $table->decimal('sold_value', 20, 2)->nullable();
            $table->string('sold_value_format', 10)->nullable();
            $table->integer('sold_with_status')->nullable()->comment('It will be Status Label ID');

            $table->tinyInteger('warranty_status')->nullable()->comment('1 - Expired, 2 - Not Expired, 3 - Not Available');
            $table->date('calc_warranty_expire_date')->nullable();
            $table->text('manufacturer_warranty_data')->nullable()->comment('manufacturar warranty data fetched by api');

            $table->tinyInteger('rdp_status')->default(0);
            $table->integer('asset_type_id')->nullable();
            $table->tinyInteger('rdp_type')->default(0)->comment('0 = Background & Interactive, 1 = Background, 2 = Interactive');

            $table->text('resale_notes')->nullable();

            $table->string('device_rfid', 191)->nullable();
            $table->string('scanner_id', 191)->nullable();
            $table->string('scanner_position', 191)->nullable();
            $table->boolean('block_device_movement')->nullable();

            $table->text('in_antenna')->nullable();
            $table->text('out_antenna')->nullable();

            $table->integer('department_id')->nullable();
            $table->integer('sez_device')->nullable();

            $table->decimal('rate_hr', 9, 2)->nullable();
            $table->decimal('rate_day', 9, 2)->nullable();
            $table->decimal('rate_week', 9, 2)->nullable();
            $table->decimal('rate_month', 9, 2)->nullable();
            $table->decimal('rate_quarterly', 9, 2)->nullable();
            $table->decimal('rate_half_yearly', 9, 2)->nullable();
            $table->decimal('rate_yearly', 9, 2)->nullable();

            $table->text('node_id')->nullable();
            $table->bigInteger('node_sync_timestamp')->default(0);
            $table->integer('live_monitor')->default(0);
            $table->dateTime('ship_date')->nullable();

            /* ============= Custom ITM Columns ============= */
            $table->text('_itm_custome_location')->nullable();
            $table->string('_itm_mobile_no')->nullable();
            $table->string('_itm_user_name')->nullable();
            $table->text('_itm_component_predefine')->nullable();
            $table->text('_itm_holiday_date')->nullable();
            $table->text('_itm_hr_dept')->nullable();
            $table->string('_itm_element')->nullable();
            $table->text('_itm_hardwaename')->nullable();
            $table->text('_itm_trial1')->nullable();
            $table->text('_itm_it_dept')->nullable();
            $table->text('_itm_device_test_date')->nullable();
            $table->text('_itm_user_dropdown')->nullable();
            $table->text('_itm_os')->nullable();
            $table->text('_itm_test_box_custom')->nullable();
            $table->string('_itm_date_constome')->nullable();
            $table->text('_itm_user_pre_drodown')->nullable();
            $table->text('_itm_custom_drop')->nullable();
            $table->text('_itm_time_custom')->nullable();
            $table->text('_itm_checkbox_custom')->nullable();
            $table->text('_itm_palak')->nullable();
            $table->text('_itm_it_far_location')->nullable();
            $table->text('_itm_device_colour')->nullable();
            $table->string('product_number')->nullable();
            $table->text('_itm_preuser')->nullable();
            $table->text('_itm_apple')->nullable();
            $table->text('_itm_cdsl')->nullable();
            $table->text('_itm_user_pre_drodown_1')->nullable();
            $table->text('_itm_device_pridrop_user')->nullable();
            $table->text('_itm_device_predrop_project')->nullable();
            $table->text('_itm_device_preddrop_devices')->nullable();
            $table->text('_itm_custom_predrop_department')->nullable();
            $table->text('_itm_textbox_any')->nullable();
            $table->text('_itm_radio_custom')->nullable();
            $table->text('_itm_checkbox_custom_field')->nullable();
            $table->text('_itm_pan_no')->nullable();
            $table->text('_itm_anmol_text_box')->nullable();
            $table->string('_itm_anmol_date')->nullable();
            $table->string('_itm_anmol_time')->nullable();
            $table->string('_itm_anmol_datetime')->nullable();
            $table->string('_itm_anmol_check_box')->nullable();
            $table->string('_itm_anmol_radio')->nullable();
            $table->string('_itm_loca_feild')->nullable();
            $table->string('_itm_loca_feild2')->nullable();
            $table->text('_itm_textboxanyfor')->nullable();
            $table->text('_itm_textboxalphafor')->nullable();
            $table->string('_itm_textboxnumericfor')->nullable();
            $table->string('_itm_dateformat')->nullable();
            $table->string('_itm_timeformat')->nullable();
            $table->string('_itm_datetimeformat')->nullable();
            $table->text('_itm_dropperformatcomponentfild')->nullable();
            $table->text('_itm_radiocustomfield')->nullable();
            $table->string('_itm_testboxfield')->nullable();
            $table->string('_itm_testboxnumerik')->nullable();
            $table->text('_itm_sample_f1')->nullable();
            $table->string('_itm_sample_f2')->nullable();
            $table->string('_itm_sample_f3')->nullable();
            $table->text('_itm_mantainance_user')->nullable();
            $table->text('_itm_license_droppre')->nullable();
            $table->text('_itm_projectdroppre')->nullable();
            $table->text('_itm_radiobutton')->nullable();
            $table->text('_itm_checkbox')->nullable();
            $table->text('_itm_locationcustom123')->nullable();
            $table->text('_itm_customuser12345')->nullable();
            $table->text('_itm_anilfieldsetlocation')->nullable();
            $table->string('_itm_testing1212')->nullable();

        });
        DB::statement('
            ALTER TABLE `assets` ADD INDEX `idx_assets_active` (
                `id`,
                `serial`(50),
                `deleted_at`
            )
        ');

        DB::statement('
            ALTER TABLE `assets` ADD INDEX `idx_assets` (
                `id`,
                `asset_tag`(50),
                `model_id`,
                `serial`(50),
                `order_number`(50),
                `assigned_to`,
                `assigned_for`,
                `chkin_log_id`,
                `chkout_log_id`,
                `user_id`,
                `physical`,
                `status_id`,
                `archived`,
                `warranty_months`,
                `warrenty_end_date`,
                `warranty_start_date`,
                `depreciate`,
                `supplier_id`,
                `requestable`,
                `rtd_location_id`,
                `internal_place_id`,
                `ni_detected_location`,
                `accepted`,
                `chkout_acceptance_log_id`,
                `company_id`,
                `amc_supplier_id`,
                `high_pririty`,
                `invoice_id`,
                `added_from`,
                `ip`(20),
                `mac`(20)
            )
        ');
        DB::statement('
            ALTER TABLE `assets` ADD INDEX `idx1_assets` (
                `device_occure_type`,
                `lease_id`,
                `stock_place`,
                `last_checkout_project`,
                `asset_owner`,
                `sold_by`,
                `sold_value`,
                `sold_with_status`,
                `warranty_status`,
                `rdp_status`,
                `rdp_type`,
                `asset_type_id`,
                `device_rfid`(50),
                `scanner_id`(50),
                `scanner_position`(50),
                `block_device_movement`,
                `department_id`,
                `sez_device`,
                `rate_hr`,
                `rate_day`,
                `rate_week`,
                `rate_month`,
                `rate_quarterly`,
                `rate_half_yearly`,
                `rate_yearly`
            )
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
