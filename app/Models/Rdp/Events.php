<?php

namespace App\Models\Rdp;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Auth;
use DB;
use stdClass;
use Log;

class Events extends Model
{
    protected $connection = "meshcentral";
    protected $table = "events";


}
