<?php

namespace App\Models\ModelsQuery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryModel extends Model
{

    public function getAllCategory($request){
        $query =  Category::query();
        $query->whereNull('deleted_at')->whereNull('deleted_by');
        
        if (!empty($request['name'])){
            $query->where('name', 'like', "%" . $request['name'] . "%");
        }
        if (!empty($request['code'])){
            $query->where('code', $request['code']);
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
    public function saveCategory($req){
        try {
            DB::beginTransaction();
            
            $category = new Post();
            if (!empty($req['code'])){
                $category  = Post::whereNull('deleted_at')->find($req['code']);
                $category->updated_at = auth()->user()->id;
            }
            $category->name = $req['name']??$post->name;
            $caregory->save();
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
            
            $category = new Category();
            if (!empty($req['id'])){
                $category  = Category::whereNull('deleted_at')->find($req['id']);
            }
            $category->deleted_by = Carbon::now();
            $category->deleted_by = auth()->user()->id;
            $category->save();
            DB::commit();
            return $post;
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
        }
    }
   
}
