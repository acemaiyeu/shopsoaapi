<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\WarehouseProductDetailStatus;
use Akaunting\Money\Money;
use Carbon\Carbon;
class WarehouseProductDetailTransformer extends TransformerAbstract
{
    
    public function transform(WarehouseProductDetailStatus $warehouse)
    {
        
        return [
            'id'                    => $warehouse->id,
            'warehouse_detail_id'  => $warehouse->warehouse_detail_id,
            'warehouse_detail'      => $warehouse->warehousedetail,
            'status'        => $warehouse->status=="IMPORT"?"Nhập":"Xuất",
            "qty"           => $warehouse->qty,
            'created_by'    => $warehouse->created_by?$warehouse->create->username:"Không tìm thấy",
            'created_at'    => Carbon::parse($warehouse->created_at)->format('d-m-Y')
        ];
    }

}
