<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\theme;
use Carbon\Carbon;

class ThemeAdminTransformer extends TransformerAbstract
{
    
    public function transform(Theme $theme)
    {
        return [
            'id' => $theme->id,
            'thumbnail_img' => $theme->thumbnail_img,
            'title' => $theme->title,
            'short_description' => $theme->short_description,
            'long_description' => $theme->long_description,
            'price' => $theme->price,
            'price_text' => number_format($theme->price, 0, ',', '.') . ' ₫',
            'price_old' =>0,
            'price_old_text' => number_format(0, 0, ',', '.') . ' ₫',
            'framework' => $theme->framework,
            'type' => $theme->type,
            "document" => $theme->document,
            'file' => $theme->file,
            // 'payment' => $theme->payment,
            // 'discount_code' => $theme->discount_code,
            // 'discount_price' => $theme->discount_price,
            // 'session_id' => $theme->session??"",
            // 'payment' => $theme->payment,
            // 'note' => $theme->note,
            // 'details' => $theme->details,
            // 'detailsShort' => $theme->Short,
            // 'user' => $theme->user,
            // 'address' => $theme->address,
            // 'promo_code'  => $theme->promo_code,
            // 'total_price' => $theme->total_price,
            // 'info_payment' => json_decode($theme->info_payment),
            // 'gifts' => json_decode($theme->gifts),
            // // 'gifts' => [],
            // 'total_pay' => $theme->total_pay,
            'created_at'  => $theme->created_at,
        ];
    }
}