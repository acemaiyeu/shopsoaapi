<?php

namespace App\Models\ModelsQuery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategoryModel extends Model
{
    protected $model;
    public function __construct(Category $model) {
        $this->model = $model;
    }
    public function getCategory($request){
        $query =  Category::query();
        $query->whereNull('deleted_at')->whereNull('deleted_by');
        if(!empty($request['id'])){
            $query->where('id', $request['id']);
        }
        if(!empty($request['name'])){
            $query->where('name', 'like', '%' . $request['name'] . '%');
        }
        if(!empty($request['code'])){
            $query->where('code', 'like', '%' . $request['code'] . '%');
        } 
       

        $limit = $request['limit'] ?? 10;
        if($limit == 1){
            return $query->first();
        }
        if($limit > 1){
            return $query->paginate($limit);
        }
    }
    
    public function createOrUpdate($req){
        try {
            DB::beginTransaction();
            if (!empty($req['id'])){
                $category = Category::whereNull('deleted_at')->find($req['id']);
            }
            if (empty($req['id'])){
                $category = new Category();
                $category->created_by = auth()->user()->id;
                
            }
            $category->updated_at = Carbon::now();
            $category->updated_by = auth()->user()->id;
            $category->code = $req['code']??$category->code;
            $category->name = $req['name']??$category->name;
            $category->save();
            
            DB::commit();
            return $category;
    } catch (\Exception $e) {
        DB::rollBack();
        // throw $e;
        return ['status' => 500, 'message' => $e->getMessage()];
    }
    }
}