<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Category;
use Akaunting\Money\Money;


class CategoryTransformer extends TransformerAbstract
{
    
    public function transform(Category $category)
    {
        return [
            'code' => $category->code,
            'name' => $category->name,
            'createdBy' => $category->createdBy,
            'created_at'  => Carbon::parse($category->created_at)->format('d-m-Y H:i:s'),
            'update_at'  => Carbon::parse($category->update_at)->format('d-m-Y H:i:s'),
        ];
    }
}
