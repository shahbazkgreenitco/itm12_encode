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
     * FIX: idx_users contains varchar(255) columns (employee_num, first_name,
     * last_name) with no prefix — each consumes 255 × 4 = 1020 bytes in
     * utf8mb4, pushing total over the 3072 byte limit.
     * Move index to DB::statement() with prefix(20) on all varchar columns.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->nullable();
            $table->string('seat_no', 191)->nullable();
            $table->longText('outlook_refresh_token')->nullable();
            $table->string('password');
            $table->text('permission')->nullable();
            $table->boolean('activated')->default(false);
            $table->string('activation_code')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->string('persist_code')->nullable();
            $table->string('reset_password_code')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->timestamps();
            $table->softDeletes();
            $table->string('website')->nullable();
            $table->string('country')->nullable();
            $table->string('gravatar')->nullable();
            $table->integer('location_id')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('jobtitle', 100)->nullable();
            $table->string('job_grade', 100)->nullable()->comment('Job Grade');
            $table->integer('manager_id')->nullable();
            $table->string('employee_num')->nullable();
            $table->string('avatar')->nullable();
            $table->string('username')->nullable();
            $table->string('notes')->nullable();
            $table->unsignedInteger('company_id')->nullable();
            $table->string('remember_token', 65)->nullable();
            $table->date('last_working_date')->nullable()->comment('User Last Working Date');
            $table->tinyInteger('archived')->nullable()->default(0)->comment('For Archived User');
            $table->string('access_token', 36)->nullable();
            $table->integer('department_id')->nullable();
            $table->string('session_id', 80)->nullable();
            $table->tinyInteger('job_type')->nullable()->default(0)->comment('0 - Company Staff, 1 - Contract Staff');
            $table->string('ex_user_company')->nullable()->comment('to note the external users company name');
            $table->string('phone2', 20)->nullable()->comment('Alternative Contact Number');
            $table->string('work_phone', 20)->nullable()->comment('Work Phone Number');
            $table->string('address', 500)->nullable()->comment('Communication/Permanent Address of User');
            $table->date('doj')->nullable()->comment('To store when employee joined the company');
            $table->string('fcm_token')->nullable();
            $table->string('permitted_locations')->nullable();
            $table->integer('created_by')->nullable()->comment('User id who create this user');
            $table->tinyInteger('create_mode')->nullable()->default(1)->comment('1 - Manual, 2 - Bulk Import, 3 - System');
            $table->string('merged_ids', 555)->nullable()->comment('User IDs of which are involved merge');
            $table->tinyInteger('is_merge_primary')->nullable()->comment('1 - Yes. It is primary on merged users, NULL - Noting');
            $table->integer('merge_primary')->nullable()->comment('Merge Ticket ID');
            $table->integer('merged_by')->nullable()->comment('Who merged the tickets');
            $table->timestamp('merged_at')->nullable()->comment('Merge Datetime');
            $table->string('merged_tkt_creators', 191)->nullable()->comment('Merged Different Creators Ids');
            $table->dateTime('tnc_accepted')->nullable();
            $table->integer('base_location_id')->nullable();
            $table->string('business_unit', 256)->nullable();
            $table->string('delivery_unit', 256)->nullable();
            $table->integer('is_vip_user')->default(0);
            $table->text('asset_departments_id')->nullable();
            $table->string('qrcode', 191)->nullable()->unique();
            $table->integer('internal_place_id')->nullable();
            $table->string('displayName', 191)->nullable();
            $table->bigInteger('ticket_handler_delegated_user')->nullable();
            $table->bigInteger('request_approval_delegated_user')->nullable();
            $table->bigInteger('change_approver_delegated_user')->nullable();
            $table->bigInteger('procurement_approver_delegated_user')->nullable();

        }); // end Schema::create


        /*
         * ================================================================
         * idx_users — raw SQL with prefix lengths
         *
         * Column             Type           Prefix
         * ────────────────   ─────────────  ──────
         * location_id        int            none   (4 bytes)
         * manager_id         int            none   (4 bytes)
         * company_id         int unsigned   none   (4 bytes)
         * department_id      int            none   (4 bytes)
         * created_by         int            none   (4 bytes)
         * create_mode        tinyint        none   (1 byte)
         * base_location_id   int            none   (4 bytes)
         * is_merge_primary   tinyint        none   (1 byte)
         * merge_primary      int            none   (4 bytes)
         * merged_by          int            none   (4 bytes)
         * job_type           tinyint        none   (1 byte)
         * is_vip_user        int            none   (4 bytes)
         * employee_num       varchar(255)   (20) ✅ (was 1020 bytes without prefix!)
         * first_name         varchar(255)   (20) ✅ (was 1020 bytes without prefix!)
         * last_name          varchar(255)   (20) ✅ (was 1020 bytes without prefix!)
         *
         * Total ≈ 279 bytes ✅ well under 3072 limit
         * ================================================================
         */
        DB::statement('
            ALTER TABLE `users` ADD INDEX `idx_users` (
                `location_id`,
                `manager_id`,
                `company_id`,
                `department_id`,
                `created_by`,
                `create_mode`,
                `base_location_id`,
                `is_merge_primary`,
                `merge_primary`,
                `merged_by`,
                `job_type`,
                `is_vip_user`,
                `employee_num`(20),
                `first_name`(20),
                `last_name`(20)
            )
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
