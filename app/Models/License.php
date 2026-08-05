<?php

namespace App\Models;

use App\Helpers\Common as CommonHelper;
use App\Models\Base;
use App\Models\Country;
use App\Models\Settings;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use StdClass;

class License extends Base
{
    protected $table = 'licenses';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = [];
    protected $assigned_for_options = [['id' => 1, 'text' => 'Device'], ['id' => 2, 'text' => 'User']];

    use SoftDeletes;

    public function licenseseats()
    {
        return $this->hasMany('App\Models\LicenseSeat', 'license_id');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id')->withDefault();
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id')->withDefault();
    }

    public function purchaseReference()
    {
        return $this->belongsTo('App\Models\Purchase', 'invoice_id');
    }

    public function purchaseAttachment()
    {
        return $this->belongsTo('App\Models\PurchaseAttachment', 'invoice_id');
    }

    public function manufacture()
    {
        return $this->belongsTo('App\Models\Manufacture', 'manufacturer_id')->withDefault();
    }

    public function depreciation()
    {
        return $this->belongsTo('App\Models\Depreciation', 'depreciation_id')->withDefault();
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier', 'supplier_id');
    }

    public function assetlog()
    {
        return $this
            ->hasMany('App\Models\Actionlog', 'asset_id')
            ->where('asset_type', '=', 'software')
            ->orderBy('created_at', 'desc');
    }

    protected function getavailableLicense($id)
    {
        return DB::select("SELECT COUNT(license_seats.id) as tot_available
        FROM `license_seats` LEFT JOIN licenses ON license_seats.license_id = licenses.id 
        WHERE license_seats.deleted_at is null and licenses.deleted_at is null
        and license_seats.assigned_to is null and license_seats.asset_id is null and licenses.category_id = {$id}");
    }

    protected function checkexpired()
    {
        return DB::select('SELECT count(expiration_date) as total_expire FROM `licenses` where `expiration_date` <  curdate() and deleted_at is null');
    }

    public function expiredDate()
    {
        if ($this->expiration_date) {
            try {
                $lwd = Carbon::createFromFormat('Y-m-d', $this->expiration_date, config('app.timezone'));
                $now = new Carbon(config('app.timezone'));
                if ($lwd->lte($now)) {
                    return true;
                }
            } catch (\Carbon\Exception\InvalidDateException $e) {
                $return = '';
            }
        }
    }

    public function requireAcceptance()
    {
        return $this->category ? $this->category->require_acceptance : false;
    }

    public function showEula()
    {
        return $this->category ? $this->category->use_default_eula : false;
    }

    public function isNeedCheckinMail()
    {
        return $this->category ? $this->category->checkin_email : false;
    }

    public function getEula()
    {
        if ($this->category->eula_text) {
            return e($this->category->eula_text);
        } elseif (Settings::getSettings()->default_eula_text) {
            return e(Settings::getSettings()->default_eula_text);
        } else {
            return null;
        }
    }

    public static function getAssignedForOptions()
    {
        return (new License)->assigned_for_options;
    }

    public function generateUniqueTag()
    {
        $catName = Category::where('id', $this->category_id)->value('name');
        $manName = Manufacture::where('id', $this->manufacturer_id)->value('name');
        $cat = !empty($catName) ? substr(preg_replace('/\s+/', '', $catName), 0, 3) : '';
        $man = !empty($manName) ? substr(preg_replace('/\s+/', '', $manName), 0, 3) : '';
        $name = $this->name ? substr(preg_replace('/\s+/', '', $this->name), 0, 3) : '';

        $parts = array_filter([$cat, $man, $name]);
        $uniquetag = implode('/', $parts) . '/' . $this->id;

        $this->unique_tag = $uniquetag;
        $this->save();
    }

    public function swTracking()
    {
        return $this->hasOne('App\Models\NetworkInventory\SWTracking\SWTracking', 'license_id', 'id');
    }

    public function LicenseDataForCache()
    {
        $data = (array) $this->only(
            'name',
            'serial',
            'purchase_date',
            'currency',
            'purchase_cost',
            'order_number',
            'seats',
            'notes',
            'user_id',
            'is_tracked',
            'depreciation_id',
            'license_name',
            'license_email',
            'depreciate',
            'supplier_id',
            'expiration_date',
            'purchase_order',
            'termination_date',
            'maintained',
            'reassignable',
            'company_id',
            'support',
            'manufacturer_id',
            'agreement_no',
            'invoice_id',
            'category_id',
            'added_via',
            'product_id',
            'Version',
            'license_type',
            'publisher_name',
            'licence_custom_fields',
            'department_id',
            'location_id',
            'internal_place_id',
            'image',
            '_itm_licencecustome',
            '_itm_licencenumber',
            'requestable_license',
            'unique_tag',
            '_itm_sbztest',
            'check_all_versions'
        );

        return $data;
    }
}
