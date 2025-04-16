<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SessionLogin;
use App\Transformers\ThemeTransformer;
use App\Transformers\ThemeAdminTransformer;
use App\Models\ModelsQuery\ThemeModel;
use App\Models\Telegram;
use App\Models\Theme;
use App\Models\Mails;

class ThemeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $model;
    public function __construct(ThemeModel $model) {
        $this->model = $model;
    }
    public function getThemes(Request $request)
    {
        Mails::sendMail();
        $themes = $this->model->getThemes($request);
        return fractal($themes, new ThemeTransformer())->respond();
    }
    public function getThemesAdmin(Request $request)
    {
        $themes = $this->model->getThemes($request);
        return fractal($themes, new ThemeAdminTransformer())->respond();
    }
    public function getThemeDetail($code, Request $request){
        $theme = Theme::whereNull('deleted_at')->where('code', $code)->first();
        return fractal($theme, new ThemeTransformer())->respond();
    }
    public function save(Request $request){
        $theme = $this->model->saveTheme($request);
        return fractal($theme, new ThemeAdminTransformer())->respond();
    }
}