<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ModelsQuery\ThemeModel;
use App\Models\Mails;
use App\Models\SessionLogin;
use App\Models\Telegram;
use App\Models\Theme;
use App\Transformers\ThemeAdminTransformer;
use App\Transformers\ThemePromotionTransformer;
use App\Transformers\ThemeTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $model;

    public function __construct(ThemeModel $model)
    {
        $this->model = $model;
    }

    public function getThemes(Request $request)
    {
        $themes = $this->model->getThemes($request);
        return fractal($themes, new ThemeTransformer())->respond();
    }

    public function getThemeForPromotion(Request $request)
    {
        $themes = $this->model->getThemes($request);
        return fractal($themes, new ThemePromotionTransformer())->respond();
    }

    public function getThemesAdmin(Request $request)
    {
        $themes = $this->model->getThemes($request);
        return fractal($themes, new ThemeAdminTransformer())->respond();
    }

    public function getThemeDetail($code, Request $request)
    {
        $theme = Theme::whereNull('deleted_at')->where('code', $code)->first();
        return fractal($theme, new ThemeTransformer())->respond();
    }

    public function save(Request $request)
    {
        $theme = $this->model->saveTheme($request);
        return fractal($theme, new ThemeAdminTransformer())->respond();
    }

    public function deleteById($id)
    {
        Theme::where('id', $id)->update(['deleted_at' => Carbon::now('Asia/Ho_Chi_Minh'), 'deleted_by' => auth()->user()->id]);

        return response()->json(['status' => 'success'], 200);
    }
}
