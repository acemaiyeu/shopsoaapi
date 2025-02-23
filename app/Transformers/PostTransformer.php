<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Post;
use Carbon\Carbon;

class PostTransformer extends TransformerAbstract
{
    
    public function transform(Post $post)
    {
        return [
            'id' => $post->id,
            'code' => $post->code,
            'name' => $post->name,
            'data' => $post->data,
            'createdBy' => $post->createdBy,
            'view'      => $post->view??0,
            'created_at'  => Carbon::parse($post->created_at)->format("d-m-Y"),
        ];
    }
}
