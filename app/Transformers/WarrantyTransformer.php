<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Warranty;
use Akaunting\Money\Money;
use App\Models\Warehouse;

class WarrantyTransformer extends TransformerAbstract
{
    
    public function getWarehouse($id){
        return Warehouse::whereNull("deleted_at")->find($id)->select('id','name');
    }

    public function transform(Warranty $warranty)
    {
        
        return [
            'id' => $warranty->id,
            'customer_id' => $warranty->customer_id,
            'customer_name' => $warranty->customer_name,
            'customer_email' => $warranty->customer_email,
            'customer_phone' => $warranty->customer_phone,
            'address' => $warranty->address,
            'created_at' => $warranty->created_at,
            'warehouse_name' => $this->getWarehouse($warranty->warehouse_id)
        ];
    }

}
