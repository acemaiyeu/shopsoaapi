<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Warranty;
use Akaunting\Money\Money;

class WarrantyTransformer extends TransformerAbstract
{
    
    public function getWarehouse($id){
        return Warhouse::whereNull("deleted_at")->find($id)->select('id','name');
    }

    public function transform(Warranty $warranty)
    {
        
        return [
            'id' => $warranty->id,
            'code' => $warranty->code,
            'name' => $warranty->name,
            'lat' => $warranty->lat,
            'lon' => $warranty->lon,
            'address' => $warranty->address,
            'created_at' => $warranty->created_at,
            'warehouse_name' => $this->getWarehouse($warranty->warehouse_id??0)
        ];
    }

}
