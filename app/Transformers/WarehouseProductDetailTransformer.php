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
            'order_id'      => $warehouse->order_id??null,
            "brand_warehouse"           => mb_strtoupper($warehouse->brand_warehouse),
            "qty"           => $warehouse->qty,
            "price"         => $warehouse->price,
            "price_text"    =>  number_format($warehouse->price,0,',','.') . " đ",
            "total_price_text"    =>  number_format($warehouse->price * $warehouse->qty,0,',','.') . " đ",
            'created_by'    => $warehouse->created_by?$warehouse->create->username:"Không tìm thấy",
            'created_at'    => Carbon::parse($warehouse->created_at)->format('d-m-Y')
        ];
    }

}
