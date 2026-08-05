<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsLivePhotoRelatedColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('tkt_attachments', function (Blueprint $table) {
            $table->boolean('is_motion_photo')->default(0);
            $table->text("motion_photo")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('tkt_attachments', function (Blueprint $table) {
            $table->dropColumn('is_motion_photo');
            $table->dropColumn('motion_photo');
        });
    }
}
