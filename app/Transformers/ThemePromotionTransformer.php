<?php

namespace App\Transformers;

use App\Models\theme;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class ThemePromotionTransformer extends TransformerAbstract
{
    public function transform(Theme $theme)
    {
        return [
            'id' => $theme->id,
            'title' => $theme->title,
            'code' => $theme->code,
        ];
    }
}
