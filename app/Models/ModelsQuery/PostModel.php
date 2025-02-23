<?php

namespace App\Models\ModelsQuery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostModel extends Model
{

    public function getAllPost($request){
        $query =  Post::query();
        $query->whereNull('deleted_at')->whereNull('deleted_by');
        
        if (!empty($request['name'])){
            $query->where('name', 'like', "%" . $request['name'] . "%");
        }
        if (!empty($request['id'])){
            $query->where('id', $request['id']);
        }
        if (!empty($request['createdby'])){
            $query->whereHas('createdBy', function($query) use($request){
                $query->where('name', 'like', "%". $request['createdby'] . "%");
            });
        }
        $query->with('createdBy');        
        $limit = $request['limit'] ?? 10;
        
        if($limit == 1){
            return $query->first();
        }
        if($limit > 1){
            return $query->paginate($limit);
        }
    }
    public function savePost($req){
        try {
            DB::beginTransaction();
            
            $post = new Post();
            if (!empty($req['id'])){
                $post  = Post::whereNull('deleted_at')->find($req['id']);
                $post->updated_at = auth()->user()->id;
            }
            $post->code = $req['code']??$post->code;
            $post->name = $req['name']??$post->name;
            $post->data = $req['data']??$post->data;
            $post->save();
            DB::commit();
            return $post;
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
        }
    }
    public function deleteById($id){
        try {
            DB::beginTransaction();
            
            $post = new Post();
            if (!empty($req['id'])){
                $post  = Post::whereNull('deleted_at')->find($req['id']);
            }
            $post->deleted_by = Carbon::now();
            $post->deleted_by = auth()->user()->id;
            $post->save();
            DB::commit();
            return $post;
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
        }
    }
    public function saveComment($id, $comment){
        try {
            DB::beginTransaction();
                $post  = Post::whereNull('deleted_at')->find($id);
    
                $comments = json_decode($post->comments)??[];
                $user = !empty(auth()->user())?auth()->user()->username:"Không đăng nhập";
                $comments[] = [
                    "username" => $user,
                    "comment" => $comment
                ];
                $post->comments = json_encode($comments);
                $post->save();
            DB::commit();
            return $post;
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
        }
    }
   
}
