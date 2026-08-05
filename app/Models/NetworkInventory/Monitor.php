<?php

namespace App\Models\NetworkInventory;

use Illuminate\Database\Eloquent\Model;

class Monitor extends Model
{
  protected $table = "itm_network_inventory_monitors";
  protected $dateFormat = "Y-m-d H:i:s";
  protected $guarded = [];

  protected $fieldsLabels = ['ManufacturerID' => 'Manufacturer ID', 'ManufactureWeek' => 'Manufacture Week', 'VerticalFrequency' => 'Vertical Frequency', 'HorizontalFrequency' => 'Horizontal Frequency', 'ProductID' => 'Product ID', 'DisplayGamma' => 'Display Gamma', 'MaximumImageSize' => 'Maximum Image Size', 'MaximumResolution' => 'Maximum Resolution'];

  public function component() {
    return $this->hasOne('App\Models\Component', 'monitor_id', 'id');
  }

  public function otherDetails($fields = []) {
    if(! $fields) {
      $fields = ['ManufacturerID', 'ManufactureWeek', 'VerticalFrequency', 'HorizontalFrequency', 'ProductID', 'DisplayGamma', 'MaximumImageSize', 'MaximumResolution'];
    }

    $others = json_decode($this->others, true);
    if(! $others) {
      return [];
    }

    $return = [];
    foreach($fields as $field) {
      if(isset($others[$field])) {
        $return[] = [
          'field' => $this->fieldsLabels[$field],
          'value' => $others[$field]         
        ];
      }
    }

    return $return;
  }
}