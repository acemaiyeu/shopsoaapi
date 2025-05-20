<?php

namespace App\Models\ModelsQuery;

use App\Models\Cart;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class DiscountModel extends Model
{
    public $promotionModel;

    public function __construct(PromotionModel $promotionModel)
    {
        $this->promotionModel = $promotionModel;
    }

    public function getDiscounts($request)
    {
        $query = Discount::query();
        $query->whereNull('deleted_at')->whereNull('deleted_by');
        if (!empty($request['id'])) {
            $query->where('id', $request['id']);
        }
        $query->where('active', 1)->where('show', 1);
        $limit = $req['limit'] ?? 30;
        $query->with('conditions');
        if ($limit == 1) {
            return $query->first();
        }

        if ($limit > 1) {
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
    public function addDiscountCart($req)
    {
        if (!empty($req['session_id'])) {
            $cart = Cart::whereNull('deleted_at')->whereNull('deleted_by')->where('session', $req['session_id'])->first();

            $discount = Coupon::whereNull('deleted_at')->whereNull('deleted_by')->where('code', $req['discount_code'])->where('active', 1)->where('start_date', '<=', Carbon::now('Asia/Ho_Chi_Minh'))->where('end_date', '>=', Carbon::now('Asia/Ho_Chi_Minh'))->first();

            if ($cart && $discount) {
                $apply = $this->promotionModel->checkConditionDiscount($cart, $discount);
                if ($apply) {
                    $cart->discount_code = $req['discount_code'];
                    $cart->discount_price = $req['discount_price'];
                    $cart->save();
                    return $cart;
                }

                return $apply;
            }
            return null;
        } else {
            return null;
        }
    }
}
