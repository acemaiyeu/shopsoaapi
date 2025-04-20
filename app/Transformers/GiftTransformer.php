<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Gift;

class GiftTransformer extends TransformerAbstract
{
    
    public function transform(Gift $gift)
    {
        return [
            'id' => $gift->id,
            'title' => $gift->title,
            'details' => $gift->details??[],
            'created_at'  => $gift->created_at,
        ];
    }
}