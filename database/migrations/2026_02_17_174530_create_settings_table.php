<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('user_id');
            $table->integer('per_page')->default(20);
            $table->string('site_name', 100)->default('IT Asset Management');
            $table->integer('qr_code')->nullable();
            $table->string('qr_text', 32)->nullable();
            $table->integer('display_asset_name')->nullable();
            $table->integer('display_checkout_date')->nullable();
            $table->integer('display_eol')->nullable();
            $table->integer('auto_increment_assets')->default(0);
            $table->string('auto_increment_prefix')->default('0');
            $table->boolean('load_remote')->default(true);
            $table->string('logo')->nullable();
            $table->string('logo_thumbnail')->nullable();
            $table->string('header_color')->nullable();
            $table->string('alert_email')->nullable();
            $table->boolean('alerts_enabled')->default(true);
            $table->integer('alerts_type')->nullable()->comment('1-RoleBased, 2-DeviceRead Permission, 3-Email');
            $table->string('alerts_value', 191)->nullable();
            $table->longText('alerts_users_email')->nullable();
            $table->longText('default_eula_text')->nullable();
            $table->string('barcode_type')->nullable()->default('QRCODE');
            $table->string('slack_endpoint')->nullable();
            $table->string('slack_channel')->nullable();
            $table->string('slack_botname')->nullable();
            $table->string('default_currency', 10)->nullable();
            $table->text('custom_css')->nullable();
            $table->tinyInteger('brand')->default(1);
            $table->string('ldap_enabled')->nullable();
            $table->string('ldap_server')->nullable();
            $table->string('ldap_uname')->nullable();
            $table->longText('ldap_pword')->nullable();
            $table->string('ldap_basedn')->nullable();
            $table->string('ldap_filter')->nullable()->default('cn=*');
            $table->string('ldap_username_field')->nullable()->default('samaccountname');
            $table->string('ldap_lname_field')->nullable()->default('sn');
            $table->string('ldap_fname_field')->nullable()->default('givenname');
            $table->string('ldap_auth_filter_query')->nullable()->default('uid=samaccountname');
            $table->integer('ldap_version')->nullable()->default(3);
            $table->string('ldap_active_flag')->nullable();
            $table->string('ldap_emp_num')->nullable();
            $table->string('ldap_email')->nullable();
            $table->boolean('full_multiple_companies_support')->default(false);
            $table->boolean('ldap_server_cert_ignore')->default(false);
            $table->tinyInteger('css_theme')->nullable();
            $table->tinyInteger('accessory_block_checkin')->nullable()->default(0)->comment('To allow/prevent the accessory checkin precess');
            $table->tinyInteger('ni_add_device_flag')->nullable()->comment('Null - No Job Running, 1 - One Job in Run');
            $table->tinyInteger('ni_device_auto_creation')->nullable()->comment('1 - auto creation; null - manual adding');
            $table->string('ni_windows_agent')->nullable();
            $table->tinyInteger('print_label_title')->nullable()->comment('null - Username, 1 - Location');
            $table->tinyInteger('prevent_multi_session')->nullable();
            $table->string('email_thankuby', 100)->nullable();
            $table->tinyInteger('hideDeviceViaLicenses')->nullable()->default(2)->comment('1-hide the license which are checked out to devices, 2-default');
            $table->string('dell_warranty_token', 191)->nullable()->comment('dell warranty token');
            $table->timestamp('dell_warranty_token_expire')->nullable()->comment('dell warranty token expire');
            $table->string('color', 191);
            $table->string('sidebar_color', 191);
            $table->string('light_color', 191);
            $table->string('primary_color', 191);
            $table->string('secondary_color', 191);
            $table->text('ni_client')->nullable();
            $table->integer('custom_fieldset_id')->nullable();
            $table->integer('ni_not_detected')->nullable()->default(30)->comment('How many days NI not detected report');
            $table->integer('location_config')->nullable();
            $table->integer('department_config')->nullable();
            $table->boolean('enable_2fa_authentication')->nullable();
            $table->text('enable_2fa_authentication_with')->nullable();
            $table->boolean('tnc_accept')->default(false);
            $table->longText('tnc_content')->nullable();
            $table->string('history_id', 191);
            $table->integer('licence_custom_fieldset_id')->nullable();
            $table->integer('accessories_custom_fieldset_id')->nullable();
            $table->integer('component_custom_fieldset_id')->nullable();
            $table->integer('consumable_custom_fieldset_id')->nullable();
            $table->text('block_device_movement_intimation_users')->nullable();
            $table->integer('cost_earned')->nullable();
            $table->integer('sez_custom_fieldset_id')->nullable();
            $table->boolean('compliance_dashboard')->default(false);
            $table->text('compliance_application')->nullable();
            $table->boolean('bitLocker_enabled')->default(false);
            $table->boolean('dlp_enabled')->default(false);
            $table->boolean('greenit_agent_enabled')->default(false);
            $table->boolean('admin_rights_enabled')->default(false);
            $table->boolean('usb_access_enabled')->default(false);
            $table->integer('announcement_speed')->default(5);
            $table->bigInteger('bot_resolved_issue_without_ticket')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
