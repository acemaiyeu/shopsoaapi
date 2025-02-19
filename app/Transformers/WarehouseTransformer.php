<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Warehouse;
use Akaunting\Money\Money;

class WarehouseTransformer extends TransformerAbstract
{
    
    public function transform(Warehouse $warehouse)
    {
        
        return [
            'id' => $warehouse->id,
            'code' => $warehouse->code,
            'name' => $warehouse->name,
            'lat' => $warehouse->lat,
            'lon' => $warehouse->lon,
            'address' => $warehouse->address,
            'created_at' => $warehouse->created_at
        ];
    }

}
