<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Category;
use Carbon\Carbon;

class CategoryTransformer extends TransformerAbstract
{

    public function transform(Category $category)
    {
        
       

        return [
            'id'            => $category->id,
            'code'          => $category->code,
            'name'          => $category->name,
            'created_at'    => Carbon::parse($category->created_at)->format('d-m-Y'),
            'created_by'    => $category->createdBy->fullname??null,
        ];
    }
}