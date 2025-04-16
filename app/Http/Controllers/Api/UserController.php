<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SessionLogin;
use App\Transformers\userTransformer;
use App\Models\ModelsQuery\UserModel;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $userModel;
    public function __construct(UserModel $model) {
        $this->userModel = $model;
    }
    public function index()
    {
        //
    }

    public function getProfile(Request $request)
    {
       
        
    }
    public function profile(Request $request)
    {
       $user = auth()->user();
       return fractal($user, new userTransformer())->respond();
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function getSession(Request $req){
        $user_id = auth()->user();
        $session = "";
        $status_code = 204;
        if ($user_id != null){
            $session = SessionLogin::where('user_id', $user_id)->whereNull('deleted_at')->first();
            if ($session){
                $status_code = 200;
                $session = $session->session_id;
            }
        }
        if (empty($user_id)){
            $userAgent = $req->header('User-Agent');
            $session = SessionLogin::where('device', $userAgent)->whereNull('deleted_at')->first();
            if ($session){
                $session = $session->session_id;
                $status_code = 200;
            }
            
            
        }
        return ["data"=> ["session_id" => $session, "status_code" => $status_code]];
    }
    public function addSession(Request $request){
        $session = new SessionLogin();
        SessionLogin::where('device',$request->header('User-Agent'))->delete();
        $session->session_id = $request['session_id'];
        $session->device =  $request->header('User-Agent');
        
        if (!empty(auth()->user())){
            $session->user_id = auth()->user()->id;
        }
        $session->save();
        return ['status' => 200];
    }
}