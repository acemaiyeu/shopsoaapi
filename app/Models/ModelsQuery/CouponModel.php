<?php

namespace App\Models\ModelsQuery;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CouponCondition;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class CouponModel extends Model
{
    public function getCoupons($request)
    {
        $query = Coupon::query();
        $query->whereNull('deleted_at')->whereNull('deleted_by');
        if (!empty($request['start_date'])) {
            $query->where('start_date', '<=', $request['start_date']);
        }
        if (!empty($request['end_date'])) {
            $query->where('end_date', '>=', $request['end_date']);
        }
        if (!empty($request['code'])) {
            $query->where('end_date', '>=', Carbon::now());
        }
        if (!empty($request['name'])) {
            $query->where('name', 'like', '%' . $request['name'] . '%');
        }
        if (!empty($request['status'])) {
            $query->where('status', $request['status']);
        }
        $query->with('conditions');
        $limit = $request['limit'] ?? 10;
        if ($limit === 1) {
            return $query->first();
        } else {
            return $query->paginate($limit);
        }
    }

    public function addCoupon($req)
    {
        $cart = Cart::whereNull('deleted_at')->with('details')->find($req['id']);
        $cart->discount_code = '';
        for ($i = 0; $i < count($req['promo_codes']); $i++) {
            $cart->discount_code = $cart->discount_code . $req['promo_codes'][$i] . ',';
        }

        // $cart->discount_code = SomeClass::removeLastWord($cart->discount_code);

        $cart->discount_code = (substr($cart->discount_code, 0, strlen($cart->discount_code) - 1));
        $cart->save();
    }

    public function updateCoupon($request)
    {
        try {
            DB::beginTransaction();
            $coupon = Coupon::whereNull('deleted_at')->find($request['id']);

            $coupon->name = $request['name'] ?? $coupon->name;
            $coupon->active = isset($request['active']) ? $request['active'] : $coupon->active;
            $coupon->start_date = !empty($request['start_date']) ? Carbon::parse($request['start_date'])->format('Y-m-d H:i:s') : $coupon->start_date;
            $coupon->end_date = $request['end_date'] ?? $coupon->end_date;
            $coupon->data = !empty($request['data']) ? ($request['data']) : $coupon->data;
            $coupon->save();

            if (!empty($request['conditions'])) {
                CouponCondition::whereNull('deleted_at')->where('coupon_id', $request['id'])->update([
                    'deleted_by' => auth()->user()->id,
                    'deleted_at' => Carbon::now(),
                ]);
                foreach ($request['conditions'] as $condition) {
                    $condition_new = new CouponCondition();
                    $condition_new->coupon_id = $coupon->id;
                    $condition_new->condition_apply = $condition['condition_apply'];
                    $condition_new->condition_data = ($condition['condition_data']);
                    $condition_new->created_by = auth()->user()->id;
                    $condition_new->save();
                }
            }

            DB::commit();
            return $coupon;
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 400, 'message' => $e];
        }
    }

    public function createCoupon($request)
    {
        try {
            DB::beginTransaction();
            $coupon = new Coupon();
            $coupon->code = $request['code'];
            $coupon->name = $request['name'] ?? $coupon->name;
            $coupon->active = isset($request['active']) ? $request['active'] : $coupon->active;
            $coupon->start_date = Carbon::parse($request['start_date'])->format('Y-m-d H:i:s');
            $coupon->end_date = Carbon::parse($request['end_date'])->format('Y-m-d H:i:s');
            $coupon->data = !empty($request['data']) ? json_encode($request['data']) : $coupon->data;
            $coupon->condition_apply = $request['condition_apply'] ?? $coupon->condition_apply;
            $coupon->condition_info = $request['condition_info'] ?? $coupon->condition_info;
            $coupon->data = $request['data'];
            $coupon->created_by = auth()->user()->id;
            $coupon->save();
            foreach ($request['conditions'] as $condition) {
                $condition_new = new CouponCondition();
                $condition_new->coupon_id = $coupon->id;
                $condition_new->condition_apply = $condition['condition_apply'];
                $condition_new->condition_data = $condition['condition_data'];
                $condition_new->created_by = auth()->user()->id;
                $condition_new->save();
            }
            DB::commit();
            return $coupon;
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 400, 'message' => $e];
        }
    }

    public function applyCoupon($cart)
    {
        $Coupons = Coupon::whereNull('deleted_at')
            ->where('start_date', '<=', Carbon::now())
            ->where('end_date', '>=', Carbon::now())
            ->whereIn('code', explode(',', $cart->discount_code))
            ->get();
        $info_payment = json_decode($cart->info_payment);

        if (!empty($Coupons)) {
            foreach ($Coupons as $key => $item) {
                $next = false;

                // conditions
                $item->condition_data = json_decode($item->condition_data);
                if ($item->condition_data) {
                    if ($item->condition_data[0]->all_condition_apply == 'ANY') {
                        foreach ($item->condition_data as $condition) {
                            if ($condition->type_condition == 'product') {
                                if ($condition->condition_apply == 'price') {
                                    $found = Arr::first($cart->details, function ($detail) use ($condition) {
                                        return $detail->product->code === $condition->product_code && $this->comparative($detail->total, $condition->condition, $condition->number_product);
                                    });
                                }
                                if ($condition->condition_apply == 'number') {
                                    foreach ($cart->details as $detail) {
                                        if ($detail->product->code == $condition->product_code && $this->comparative($detail->qty, $condition->condition, $condition->number_product)) {
                                            $next = true;
                                            break;
                                        }
                                    }
                                }
                            }
                            if ($condition->type_condition == 'cart') {
                                if ($this->comparative($cart->details->sum('total'), $condition->condition, $condition->number_cart)) {
                                    $next = true;
                                }
                            }

                            if ($next == true) {
                                break;
                            }
                        }
                    }

                    if ($item->condition_data[0]->all_condition_apply == 'TOGETHER') {
                        $next = true;  // set = true khi ko thoa man = false
                        foreach ($item->condition_data as $condition) {
                            if ($condition->type_condition == 'product') {
                                if ($condition->condition_apply == 'price') {
                                    $found = Arr::first($cart->details, function ($detail) use ($condition) {
                                        return $detail->product->code === $condition->product_code && comparative($detail->total, $condition->condition, $condition->number_product);
                                    });
                                    if ($found == null) {
                                        $next = false;
                                    }
                                }
                                if ($condition->condition_apply == 'number') {
                                    $found = Arr::first($cart->details, function ($detail) use ($condition) {
                                        return $detail->product->code === $condition->product_code && comparative($detail->qty, $condition->condition, $condition->number_product);
                                    });
                                    if ($found == null) {
                                        $next = false;
                                    }
                                }
                            }
                            if ($condition->type_condition == 'cart') {
                                if (!comparative($cart->details->sum('total'), $condition->condition, $condition->number_cart)) {
                                    $next = false;
                                }
                            }

                            if ($next == false) {
                                break;
                            }
                        }
                    }
                }

                // da thong qua
                $item->discount = json_decode($item->discount);
                if ($next == true && $item->discount) {
                    // dd($item->discount);
                    foreach ($item->discount as $discount) {
                        if ($discount->type_apply == 'product') {
                            if ($discount->discount_type == 'price') {
                                foreach ($cart->details as $detail) {
                                    if ($detail->product->code == $discount->product_code) {
                                        $detail->discount_price = $discount->number_product;
                                        $detail->total = ($detail->price * $detail->qty) - $discount->number_product;
                                        if ($discount->number_product > ($detail->price * $detail->qty)) {
                                            $detail->discount_price = ($detail->price * $detail->qty);
                                            $detail->total = 0;
                                        }
                                        if ($detail->total < 0) {
                                            $detail->total = 0;
                                        }
                                        $detail->save();
                                        $info_payment[count($info_payment)] = [
                                            'total_price' => $detail->discount_price,
                                            'total_price_text' => '-' . number_format($detail->discount_price, 0, ',', '.') . ' đ',
                                            'name_show' => $item->name
                                        ];
                                        break;
                                    }
                                }
                            }
                            if ($discount->discount_type == 'percent') {
                                foreach ($cart->details as $detail) {
                                    if ($detail->product->code == $discount->product_code) {
                                        $detail->discount_price = (($detail->price * $detail->qty) * $discount->number_product) / 100;
                                        $detail->total = ($detail->price * $detail->qty) - $detail->discount_price;
                                        if ($detail->discount_price > ($detail->price * $detail->qty)) {
                                            $detail->discount_price = ($detail->price * $detail->qty);
                                            $detail->total = 0;
                                        }
                                        if ($detail->total < 0) {
                                            $detail->total = 0;
                                        }
                                        $detail->save();
                                        $info_payment[count($info_payment)] = [
                                            'total_price' => $detail->discount_price,
                                            'total_price_text' => number_format($detail->discount_price, 0, ',', '.') . ' đ',
                                            'name_show' => $item->name
                                        ];
                                        break;
                                    }
                                }
                            }
                        }
                        if ($discount->type_apply == 'cart') {
                            $total_price = $cart->details->sum('total');

                            if ($discount->number_cart >= $total_price) {
                                $cart->discount_price += $total_price;

                                $info_payment[count($info_payment)] = [
                                    'total_price' => $total_price,
                                    'total_price_text' => number_format($total_price, 0, ',', '.') . ' đ',
                                    'name_show' => $item->name
                                ];
                            }
                            if ($discount->number_cart < $total_price) {
                                $cart->discount_price += $discount->number_cart;

                                $info_payment[count($info_payment)] = [
                                    'total_price' => $discount->number_cart,
                                    'total_price_text' => number_format($discount->number_cart, 0, ',', '.') . ' đ',
                                    'name_show' => $item->name
                                ];
                            }
                        }
                    }
                }
            }
        }
        $cart->info_payment = json_encode($info_payment);
        return $cart;
    }

    public function comparative($value, $method, $value2)
    {
        if ($method == '<') {
            return $value < $value2;
        }
        if ($method == '<=') {
            return $value <= $value2;
        }
        if ($method == '=') {
            return $value == $value2;
        }
        if ($method == '>') {
            return $value > $value2;
        }
        if ($method == '>=') {
            return $value >= $value2;
        }
    }
}
