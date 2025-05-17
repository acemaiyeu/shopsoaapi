<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCouponValidator;
use App\Http\Requests\UpdateCouponValidator;
use App\Models\ModelsQuery\CartModel;
use App\Models\ModelsQuery\CouponModel;
use App\Models\Coupon;
use App\Transformers\CouponTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    protected $CouponModel;
    protected $cartModel;

    public function __construct(CouponModel $model, CartModel $cartModel)
    {
        $this->couponModel = $model;
        $this->cartModel = $cartModel;
    }

    public function getCoupons(Request $req)
    {
        $coupons = $this->couponModel->getCoupons($req);
        return fractal($coupons, new CouponTransformer())->respond();
    }

    public function getCouponClient(Request $req)
    {
        $req['start_date'] = Carbon::now();
        $req['end_date'] = Carbon::now();
        $req['status'] = 1;
        $coupons = $this->couponModel->getCoupon($req);
        return fractal($coupons, new CouponTransformer())->respond();
    }

    public function addCoupon(Request $req)
    {
        $cart = $this->cartModel->getCart($req);
        $this->couponModel->addCoupon($req, $cart);
        return ['data' => ['message' => 'success', 'status' => 200]];
    }

    public function detail($code)
    {
        $coupon = Coupon::whereNull('deleted_at')->with('conditions')->where('code', $code)->first();
        if (empty($coupon)) {
            return response(['message' => 'Không tìm thấy mã giảm giá'], 400);
        }
        return fractal($coupon, new CouponTransformer())->respond();
    }

    public function update(Request $req)
    {
        $promo = Coupon::whereNull('deleted_at')->find($req['id']);
        $promo->discount = json_encode($req['discount']);
        $promo->save();
    }

    public function createCoupon(CreateCouponValidator $req)
    {
        $coupon = $this->couponModel->createCoupon($req);
        return $coupon;
        return fractal($coupon, new CouponTransformer())->respond();
    }

    public function updateCoupon(UpdateCouponValidator $req)
    {
        $coupon = $this->couponModel->updateCoupon($req);
        return fractal($coupon, new CouponTransformer())->respond();
    }

    public function deleteCoupon($code)
    {
        Coupon::whereNull('deleted_at')->where('code', $code)->update(['deleted_at' => Carbon::now(), 'deleted_by' => auth()->user()->id]);
        return response()->json(['message' => 'Xóa mã giảm giá thành công'], 200);
    }
}
