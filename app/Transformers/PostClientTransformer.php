<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Post;
use Carbon\Carbon;

class PostClientTransformer extends TransformerAbstract
{
    
    public function transform(Post $post)
    {
        return [
            'id' => $post->id,
            'code' => $post->code,
            'name' => $post->name,
            'createdBy' => $post->createdBy,
            'comments' => json_decode($post->comments)??[],
            'data'  => $post->data,
            'created_at'  => Carbon::parse($post->created_at)->format("d-m-Y"),
        ];
    }
}
