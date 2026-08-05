<?php

namespace App\Models\Rdp;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Auth;
use DB;
use Log;

class Meshcentral extends Model
{
    protected $connection = "meshcentral";
    protected $table = "main";
    protected $keyType = 'string';

    const PREFIX_NODE = "node//";


}
