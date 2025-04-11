<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SessionLogin;
use App\Transformers\DiscountTransformer;
use App\Models\ModelsQuery\DiscountModel;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $model;
    public function __construct(DiscountModel $model) {
        $this->model = $model;
    }

    public function getDiscounts(Request $request)
    {
        $discounts = $this->model->getDiscounts($request);
        return fractal($discounts, new DiscountTransformer())->respond();
    }

}