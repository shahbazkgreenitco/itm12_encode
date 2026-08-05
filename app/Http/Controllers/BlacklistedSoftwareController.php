<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Validator;
// use App\Models\Blacklisted;
// use App\Models\BlacklistedSoftwareStatus;
use App\Models\NetworkInventory\Product;
// use App\Models\NetworkInventory\Basic;
// use App\Jobs\BlacklistedSoftwareFetchStatus;

class BlacklistedSoftwareController extends Controller
{
    public function getVersionBySoftwares(Request $request)
    {
        $return = array();
        $search = $request->input('search', '');
        $page = $request->input('page', 1);
        $skip = (($page * 20) - 20);

        $ids = explode(',', $request->product_id);
        $captionIds = [];
        if (!empty($ids)) {
            foreach ($ids as $id) {
                $captionCheck = Product::where('id', $id)->first();
                if (isset($captionCheck) && $captionCheck->count() > 0) {
                    array_push($captionIds, $captionCheck->Caption);
                }
            }
        }
        $db = DB::table('itm_network_inventory_products')->select(DB::raw('MIN(id) as id'), 'Version as text')->groupBy('Version');
        $db->whereIn('Caption', $captionIds);

        if ($search) {
            $db->where('Version', 'like', '%' . $search . '%');
        }
        $count = (clone $db)->get()->count();
        $db->skip($skip)->take(20);
        $result = $db->get();

        $return['pagination'] = array('more' => ($count - ($page * 20)) > 0 ? true : false);
        $return['results'] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }

    public function getSoftwareByAjax(Request $request)
    {
        $return = array();
        $search = $request->input('search', '');
        $page = $request->input('page', 1);
        $skip = (($page * 20) - 20);

        $db = DB::table('itm_network_inventory_products')->select('id', 'Caption as text');
        if ($search) {
            $db->where('Caption', 'like', '%' . $search . '%');
        }

        $db->orderBy('id', 'Asc');
        $count = count($db->get());
        $db->skip($skip)->take(20);
        $result = $db->get()->unique('text')->values();
        $return['total'] = count($result);
        $return['pagination'] = array('more' => ($count - ($page * 20)) > 0 ? true : false);
        $return['results'] = count($result) ? $result->toArray() : [];
        return response()->json($return);
    }
}
