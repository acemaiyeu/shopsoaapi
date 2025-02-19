<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\WarehouseDetail;
use Akaunting\Money\Money;
use Carbon\Carbon;
class WarehouseDetailTransformer extends TransformerAbstract
{
    
    public function transform(WarehouseDetail $warehouse)
    {
        
        return [
            'id' => $warehouse->id,
            'product_id' => $warehouse->product_id,
            'warehouse_id' => $warehouse->warehouse_id,
            'qty' => $warehouse->qty,
            'warehouse' => $warehouse->warehouse,
            'product' => $warehouse->product,
            'created_at' => Carbon::parse($warehouse->created_at)->format('d-m-Y')
        ];
    }

}
