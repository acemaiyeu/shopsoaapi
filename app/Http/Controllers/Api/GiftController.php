<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ModelsQuery\GiftModel;
use App\Models\ModelsQuery\PromotionModel;
use App\Transformers\GiftTransformer;
use App\Models\Theme;
use App\Models\Gift;
use App\Models\GiftDetail;
use Illuminate\Support\Facades\Http;

class GiftController extends Controller
{
    protected $giftModel;
    public function __construct(GiftModel $model) {
        $this->giftModel = $model;
       
    }
    public function getGift(Request $req){
       $gift =  $this->giftModel->getAllGifts($req);
       return fractal($gift, new GiftTransformer())->respond();
    }

    public function addToGift(Request $req){
       
        $theme = Theme::whereNull('deleted_at')->find($req['theme_id']);
        $message = "Không tìm thấy theme";
        $status = 401;
        $gift = null;
        
        if (!empty($theme)){
            $message = "success";
            $status = 200;
            $gift = $this->GiftModel->addToGift($req);
            // $gift = Gift::with('details')->find($gift->id);
        }
        return  fractal($gift, new GiftTransformer())->respond(); 
    }   
    public function updateGiftInfo(Request $req){
        $gift = $this->GiftModel->getGift($req);
        $gift = $this->GiftModel->updateGiftInfo($req, $gift);
        return  fractal($gift, new GiftTransformer())->respond(); 
    }
   
}