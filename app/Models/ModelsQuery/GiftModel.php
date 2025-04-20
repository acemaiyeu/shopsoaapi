<?php

namespace App\Models\ModelsQuery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Gift;

class GiftModel extends Model
{
    public function getAllGifts($request){
        $query =  Gift::query();
        $query->whereNull('deleted_at')->whereNull('deleted_by');
        if (!empty($request['id'])){
            $query->where('id', $request['id']);
        }
        if (!empty($request['title'])){
            $query->where('title', 'like', '%' . $request['id'] . '%');
        }
        $query->with('details');
        $limit = $req['limit']??10;
        if ($limit == 1){
            return $query->first();
        }
        if ($limit > 1){
            return $query->paginate($limit);
        }   
    }
   
}