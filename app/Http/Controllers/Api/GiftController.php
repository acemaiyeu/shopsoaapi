<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GiftCreateValidator;
use App\Http\Requests\GiftUpdateValidator;
use App\Models\ModelsQuery\GiftModel;
use App\Models\ModelsQuery\PromotionModel;
use App\Models\Gift;
use App\Models\GiftDetail;
use App\Models\Theme;
use App\Transformers\GiftTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GiftController extends Controller
{
    protected $giftModel;

    public function __construct(GiftModel $model)
    {
        $this->giftModel = $model;
    }

    public function getGift(Request $req)
    {
        $gift = $this->giftModel->getAllGifts($req);
        return fractal($gift, new GiftTransformer())->respond();
    }

    public function addToGift(Request $req)
    {
        $theme = Theme::whereNull('deleted_at')->find($req['theme_id']);
        $message = 'Không tìm thấy theme';
        $status = 401;
        $gift = null;

        if (!empty($theme)) {
            $message = 'success';
            $status = 200;
            $gift = $this->GiftModel->addToGift($req);
            // $gift = Gift::with('details')->find($gift->id);
        }
        return fractal($gift, new GiftTransformer())->respond();
    }

    public function updateGiftInfo(Request $req)
    {
        $gift = $this->GiftModel->getGift($req);
        $gift = $this->GiftModel->updateGiftInfo($req, $gift);
        return fractal($gift, new GiftTransformer())->respond();
    }

    public function detail(Request $req, $id)
    {
        $req['id'] = $id;
        $req['limit'] = 1;
        $gift = $this->giftModel->getAllGifts($req);
        return fractal($gift, new GiftTransformer())->respond();
    }

    public function create(GiftCreateValidator $req)
    {
        $gift = $this->giftModel->createOrUpdate($req);

        if (is_array($gift)) {
            return response()->json($gift, $gift['status']);
        }
        return fractal($gift, new GiftTransformer())->respond();
    }

    public function update(GiftUpdateValidator $req)
    {
        $gift = $this->giftModel->createOrUpdate($req);

        if (is_array($gift)) {
            return response()->json($gift, $gift['status']);
        }
        return fractal($gift, new GiftTransformer())->respond();
    }

    public function deleteCategory($id)
    {
        $gift = Gift::whereNull('deleted_at')->find($id);
        if (!$gift) {
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy dữ liệu'], 404);
        }
        $gift->update(['deleted_at' => now(), 'deleted_by' => auth()->user()->id]);
        return response()->json(['status' => 200, 'message' => 'Xóa danh sách thành công'], 200);
    }
}
