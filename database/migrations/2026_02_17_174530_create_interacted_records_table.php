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
     * RULES for prefix lengths:
     * ✅ VARCHAR  → prefix allowed e.g. column(50)
     * ❌ ENUM     → prefix NOT allowed (MySQL Error 1089)
     * ❌ INT / TINYINT / DATE / DECIMAL → no prefix allowed
     */
    public function up(): void
    {
        Schema::create('interacted_records', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('interact_log_id');
            $table->timestamps();
            $table->string('name')->nullable();
            $table->string('asset_tag');
            $table->integer('model_id');
            $table->string('serial')->nullable();
            $table->string('uuid', 100)->nullable()->comment('Unique Device I');
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 13, 4)->default(0);
            $table->string('order_number')->nullable();
            $table->integer('assigned_to')->nullable();
            $table->tinyInteger('assigned_for')->nullable()->comment('1 - User, 1 - Place');
            $table->text('notes')->nullable();
            $table->text('image')->nullable();
            $table->integer('user_id');
            $table->boolean('physical')->nullable()->default(true);
            $table->softDeletes();
            $table->integer('status_id')->nullable();
            $table->boolean('archived')->nullable()->default(false);
            $table->integer('warranty_months')->nullable();
            $table->boolean('depreciate')->nullable()->default(false);
            $table->integer('supplier_id')->nullable();
            $table->tinyInteger('requestable')->nullable()->default(0);
            $table->integer('rtd_location_id')->nullable();
            $table->integer('internal_place_id')->nullable();
            $table->enum('accepted', ['pending', 'accepted', 'rejected', 'declined'])->nullable();
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
            $table->integer('asset_type_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->unsignedBigInteger('asset_owner')->nullable();
            $table->date('warranty_start_date')->nullable();
            $table->date('warrenty_end_date')->nullable();
            $table->text('_itm_drop1')->nullable();
            $table->text('_itm_drop2')->nullable();
            $table->text('_itm_drop3')->nullable();
            $table->text('_itm_date')->nullable();
            $table->string('_itm_text')->nullable();
            $table->integer('stock_place')->nullable();
            $table->string('device_rfid')->nullable();
            $table->integer('sez_device')->nullable();
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

            // Small index — only int + timestamp, safe inside Blueprint
            $table->index(['serial', 'status_id', 'created_at'], 'idx_interacted_records_report');

        }); // end Schema::create


        /*
         * ================================================================
         * idx_interacted_records — raw SQL with correct prefix lengths
         *
         * Column               Type           Prefix
         * ───────────────────  ─────────────  ──────
         * interact_log_id      int unsigned   none
         * asset_tag            varchar(255)   (50) ✅
         * model_id             int            none
         * serial               varchar(255)   (50) ✅
         * uuid                 varchar(100)   (50) ✅
         * order_number         varchar(255)   (50) ✅
         * assigned_to          int            none
         * assigned_for         tinyint        none
         * user_id              int            none
         * physical             tinyint        none
         * status_id            int            none
         * archived             tinyint        none
         * warranty_months      int            none
         * depreciate           tinyint        none
         * supplier_id          int            none
         * requestable          tinyint        none
         * rtd_location_id      int            none
         * internal_place_id    int            none
         * accepted             ENUM           none ❌ ENUM = no prefix ever!
         * company_id           int unsigned   none
         * purchase_currency    varchar(10)    none (too short to matter)
         * amc_supplier_id      int            none
         * high_pririty         tinyint        none
         * invoice_id           int            none
         * added_from           tinyint        none
         * ip                   varchar(50)    (20) ✅
         * mac                  varchar(60)    (20) ✅
         * device_occure_type   tinyint        none
         * lease_id             int            none
         * asset_type_id        int            none
         * department_id        int            none
         * ================================================================
         */
        DB::statement('
            ALTER TABLE `interacted_records` ADD INDEX `idx_interacted_records` (
                `interact_log_id`,
                `asset_tag`(50),
                `model_id`,
                `serial`(50),
                `uuid`(50),
                `order_number`(50),
                `assigned_to`,
                `assigned_for`,
                `user_id`,
                `physical`,
                `status_id`,
                `archived`,
                `warranty_months`,
                `depreciate`,
                `supplier_id`,
                `requestable`,
                `rtd_location_id`,
                `internal_place_id`,
                `accepted`,
                `company_id`,
                `purchase_currency`,
                `amc_supplier_id`,
                `high_pririty`,
                `invoice_id`,
                `added_from`,
                `ip`(20),
                `mac`(20),
                `device_occure_type`,
                `lease_id`,
                `asset_type_id`,
                `department_id`
            )
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interacted_records');
    }
};
