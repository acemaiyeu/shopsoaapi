<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ModelsQuery\PostModel;
use App\Transformers\PostTransformer;
use App\Transformers\PostClientTransformer;


use Illuminate\Support\Facades\Http;
class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $postModel;
    public function __construct(PostModel $model) {
       $this->postModel = $model;
    }
    public function getAllPost(Request $req){
       $posts =  $this->postModel->getAllPost($req);
       return fractal($posts, new PostClientTransformer())->respond();
    }
    public function getAllPostForAdmin(Request $req){
           $posts =  $this->postModel->getAllPost($req);
           return fractal($posts, new PostTransformer())->respond();
    }
    public function getDetailPost(Request $req){
        $req['limit']  = 1;
        $post =  $this->postModel->getAllPost($req);
        return fractal($post, new PostClientTransformer())->respond();
    }
    public function getDetailPostForAdmin(Request $req){
        $req['limit']  = 1;
        $post =  $this->postModel->getAllPost($req);
        return fractal($post, new PostTransformer())->respond();
    }
    public function deleteById(Request $req){
       $post =  $this->postModel->getWarrantyByCode($req);
        return $post;
    }
    public function comment($id, $comment){
        $post =  $this->postModel->saveComment($id, $comment);
        return fractal($post, new PostClientTransformer())->respond();
     }
     public function savePost(Request $req){
        $post = $this->postModel->savePost($req);
        return fractal($post, new PostTransformer())->respond();
     }
}
