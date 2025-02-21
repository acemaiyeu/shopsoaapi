<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Warranty;
use Akaunting\Money\Money;
use App\Models\Warehouse;
use Carbon\Carbon;

class WarrantyTransformer extends TransformerAbstract
{
    
    public function getWarehouse($id){
        return Warehouse::whereNull("deleted_at")->select('id','name','address')->find($id);
    }

    public function transform(Warranty $warranty)
    {
        
        return [
            'id' => $warranty->id,
            'customer_id' => $warranty->customer_id,
            'customer_name' => $warranty->customer_name,
            'customer_email' => $warranty->customer_email,
            'customer_phone' => $warranty->customer_phone,
            'customer_address' => $warranty->customer_address,
            'created_at' => Carbon::parse($warranty->created_at)->format('d-m-Y'),
            'warehouse' => $this->getWarehouse($warranty->warehouse_id),
            'createdby' => $warranty->createdby,
            'details' => $warranty->details
        ];
    }

}
