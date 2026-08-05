<?php

namespace App\Models\NetworkInventory;

use Illuminate\Database\Eloquent\Model;
use DB;

class ChangeLog extends Model
{
    protected $table = "itm_network_change_logs";
    protected $dateFormat = "Y-m-d H:i:s";
    protected $guarded = [];

    public function oldData() {
        return $this->hasOne('App\Models\NetworkInventory\ChangeCache','cl_id');
    }

    public function changeTypeHtml() {
        switch($this->cl_type) {
            case 1:
                return '<span class="label-detect">Newly Added</span>';
            case 2:
                return '<span class="label-change">Changes Detected</span>';
            case 3:
                return '<span class="label-missing">Missing</span>';
        }
    }

    public function changeItemHtml() {
        switch($this->cl_item) {
            case 1:
                return 'Disk Drive';
            case 2:
                return 'Internal Memory';
            case 3:
                return 'Software';
        }
    }

    public function isSoftware() {
        return $this->cl_item == 3;
    }

    public static function manufacturerOptions() {
        return DB::table("itm_network_change_logs")->select("Manufacturer")->whereNotNull("Manufacturer")->distinct()->orderBy("Manufacturer")->pluck("Manufacturer")->toArray();
    }

    public static function markAsNotified($basic_id, $user_id) {
        DB::update("update itm_network_change_logs set notified = (case when notified is null then '" . $user_id . "' else concat(notified, '," . $user_id . "') end) where basic_id = '" . $basic_id . "' and (find_in_set('". $user_id . "', notified) = 0 or find_in_set('". $user_id . "', notified) is null)");
    }
}