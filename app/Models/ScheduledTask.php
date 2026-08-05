<?php

namespace App\Models;

use App\Models\Base;

use Illuminate\Database\Eloquent\Model;

class ScheduledTask extends Base
{
    protected $table = "scheduled_task_list";
    protected $guarded = [];

    public static function taskStatus() {
        return [
            ['id' => '1', 'text' => 'Completed'],
            ['id' => '2', 'text' => 'Not Completed']
        ];
    }

}
