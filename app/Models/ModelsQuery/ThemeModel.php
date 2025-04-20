<?php

namespace App\Models\ModelsQuery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Theme;

class ThemeModel extends Model
{
    public function getThemes($request){
        $query =  Theme::query();
        $query->whereNull('deleted_at')->whereNull('deleted_by');
        if (!empty($request['id'])){
            $query->where('id', $request['id']);
        }
        if (!empty($request['title'])){
            $query->where('title', 'like', "%". $request['title'] . "%");
        }
        $query->with('gifts');
        $limit = $request['limit']??30;
        if ($limit == 1){
            return $query->first();
        }
        if ($limit > 1){
            return $query->paginate($limit);
        }   
    }
    // public function updateProfile($req){
    //     try {   
    //         $user = auth()->user();
    //         if ($user){
    //             $user->fullname = $req->fullname??$user->fullname;
    //             $user->email = $req->email??$user->email;
    //             $user->phone = $req->phone??$user->phone;
    //             $user->ward_id = $req->ward_id??$user->ward_id;
    //             $user->district_id = $req->district_id??$user->district_id;
    //             $user->city_id = $req->city_id??$user->city_id;
    //             $user->save();
    //             return $user;
    //         }else{
    //             return  ["status" => 404, "message" => "Không tìm thấy người dùng"];
    //         }
    //     }catch(Exception $e){
    //         return  ["status" => 500, "message" => $e];
    //     }
    // }
    public function saveTheme($request){
        $theme = Theme::whereNull('deleted_at')->find($request['id']??0);
        if (empty($theme)){
            $theme = new Theme();
            $theme->created_by = auth()->user()->id;
        }
        $theme->code = $request['code']??$theme->code;
        $theme->title = $request['title']??$theme->title;
        $theme->thumbnail_img = $request['thumbnail_img']??$theme->thumbnail_img;
        $theme->img_slider = !empty($request['slider'])? json_encode($request['slider']): $theme->img_slider;
        $theme->framework = $request['framework']??$theme->framework;
        $theme->gift = $request['gift']??$theme->gift;
        $theme->short_description = $request['short_description']??$theme->short_description;
        $theme->file = $request['file']??$theme->file;
        $theme->category_id = $request['category_id']??$theme->category_id;
        $theme->long_description = $request['long_description']??$theme->long_description;
        $theme->document = $request['document']??$theme->document; //Yêu cầu 
        $theme->price = $request['price']??$theme->price; //Yêu cầu 
        $theme->price_text = Number_format($theme->price, 0, ',', '.') . ' ₫';
        $theme->save();
        return $theme;
    }

}