<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SessionLogin;
use App\Transformers\userTransformer;
use App\Models\ModelsQuery\UserModel;
use Illuminate\Support\Facades\File;
use App\Models\User;
// use App\Http\Requests\RegisterValidator;
use App\Http\Requests\RegisterValidator;
use Illuminate\Support\Facades\Hash;

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
        if (!empty($user_id) != null){
            $session = SessionLogin::where('user_id', $user_id)->whereNull('deleted_at')->first();
            if (!empty($session)){
                $status_code = 200;
            }
        }
        if (!empty($req['device'])){
            $session = SessionLogin::where('device', $req['device'])->whereNull('deleted_at')->first();
            $status_code = 200;
        }
        return ["data"=> ["session_id" => !empty($session)?$session->session:"", "status_code" => $status_code]];
    }
    public function addSession(Request $request){
        $session = new SessionLogin();
        SessionLogin::where('device',$request['device'])->delete();
        $session->session = $request['session_id'];
        $session->device = $request['device'] ?? null;
        if (!empty(auth()->user())){
            $session->user_id = auth()->user()->id;
        }
        $session->save();
        return ['status' => 200];
    }
    public function uploadImage(Request $request)
    {
        
        // Kiểm tra xem có file được gửi lên không
        if (!$request->hasFile('image')) {
            return response()->json(['message' => 'Không có file nào được tải lên'], 400);
        }

        $file = $request->file('image');

        // Định dạng tên file
        $fileName = $file->getClientOriginalName();
        if(File::exists(public_path('img/' . $fileName))){
            return response()->json([
                'message' => 'Tải lên thất bại. Tên file đã có trên server',
                'filePath' => url('img/' . $fileName)
            ],400);
        }

        // Lưu file vào thư mục public/img
        $file->move(public_path('img'), $fileName);

        // Trả về đường dẫn file sau khi upload
        return response()->json([
            'message' => 'Tải lên thành công',
            'filePath' => url('img/' . $fileName)
        ]);
    }
    public function getImage(Request $req)
    {
        $fileName = $req->input('file_name'); // Lấy tên file từ request
        $imagePath = public_path('img/' . $fileName); // Đường dẫn file ảnh
    
        if (File::exists($imagePath)) {
            return url('img/' . $fileName); // Trả về URL đầy đủ
         
        }
    
        return response()->json([
            'message' => 'Ảnh không tồn tại',
            'image' => null
        ], 404);
    }
    public function register(RegisterValidator $request)
    {

        $user  = User::whereNull('deleted_at')->where('email', $request['email'])->first();
        if (!empty($user)){
            return response()->json(['message' => "Tài khoản đã tồn tại!"], 400);
        }
        $user = New User();
        $user->email = $request['email'];
        $user->password = Hash::make($request['password']);
        $user->role_code = "GUEST";
        $user->address = !empty($request['address'])?$request['address']:"";
        $user->username = !empty($request['username'])?$request['username']:"";
        $user->save();
        return response()->json(['username' => $user->email]);
    }

}
