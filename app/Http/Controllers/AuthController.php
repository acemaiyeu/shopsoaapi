<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SessionLogin;
use App\Models\User;
use App\Transformers\UserClientTransformer;
use App\Models\ModelsQuery\UserModel;
use App\Models\Mails;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    //
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    protected $model;
    public function __construct(UserModel $model)
    {
        $this->middleware('auth:api', ['except' => ['login','register', 'profile', 'logout','updateProfile','forGotPassword','activePassword']]);
        $this->model = $model;
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }
    public function register(Request $req){
        $user = User::where('email',$req->email)->orwhere('phone', $req->phone)->exists();
        if (!$user){
            $user = User::create([
                'fullname' => $req->fullname,
                'phone' => $req->phone,
                'email' => $req->email,
                'password' => password_hash($req->password, PASSWORD_DEFAULT)
            ]);
            $token = auth()->login($user);
            return $this->respondWithToken($token);
        }else{
            return response()->json(['message' => 'Email hoặc số điện thoại đã tồn tại'], 422);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        // return response()->json(auth()->user());
        if (empty(auth()->user())) {
            return response()->json(['status' => 404,'message' => 'Không tìm thấy người dùng'], 404);
        }
        return fractal(auth()->user(), new UserClientTransformer())->respond();
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out','updateProfile']);
    }
    public function updateProfile(Request $req)
    {
        // return response()->json(auth()->user());
        $user = $this->model->updateProfile($req);
        if(is_array($user)){
            return response()->json($user, $user['status']);
        }
        return fractal(auth()->user(), new UserClientTransformer())->respond();
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }
    
    public function forGotPassword(Request $req) {
        $user = User::whereNull('deleted_at')->where('email', $req->email)->first();
    
        // Tạo mật khẩu mới
        $password_new = $this->generateSecurePassword(8);
    
        // Lấy URL hiện tại
        $currentUrl = request()->url(); // Sử dụng helper `request()`
    
        // Gửi email với mật khẩu mới
        if ($user){
            try{
                Mails::sendMail(
                    $req->email, 
                    "Đây là mật khẩu mới của bạn: " . $password_new . "\n Bấm vào link dưới đây để kích hoạt mật khẩu " . $currentUrl . "/active/" .   $password_new, 
                    'Reset password'
                );
                $user->password_temp = $password_new;
                $user->save();
                return response()->json(['message' => 'Mật khẩu mới đã được gửi đến email của bạn. Vui lòng kiểm tra email của bạn để lấy mật khẩu mới.']);
               }catch(\Exception $e){
                return response()->json(['message' => 'Vui lòng kích hoạt mật khóa', 'data' => $e], 400);
               }
        }else{
            return response()->json(['message' => 'Email không tìm thấy'], 400);
        }
       
    }
    public function activePassword(Request $req,$password_new) {
       
            $user = User::whereNull('deleted_at')->where('password_temp', $password_new)->first();
            if ($user){
                $user->password = password_hash($password_new, PASSWORD_DEFAULT);
                $user->password_temp = null;
                $user->save();
                $status = "success";  // Hoặc lấy giá trị này từ đâu đó, ví dụ như từ cơ sở dữ liệu
                return view('activity', ['status' => $status]);
            }else{
                $status = "failed";  // Hoặc lấy giá trị này từ đâu đó, ví dụ như từ cơ sở dữ liệu
                return view('activity', ['status' => $status]);
                return response()->json(['message' => 'Vui lòng kiểm tra đường dẫn trong email của bạn hoặc mật khẩu đã được kích hoạt.'], 400);
            }
    }
    
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
    function generateSecurePassword($length = 12) {
        if ($length < 8) {
            $length = 8;
        }
    
        $upperChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowerChars = 'abcdefghijklmnopqrstuvwxyz';
        $digits     = '0123456789';
        $specials   = '!@#$^&*()-_?';
    
        // Bắt buộc mỗi loại ký tự xuất hiện ít nhất một lần
        $password = '';
        $password .= $upperChars[random_int(0, strlen($upperChars) - 1)];
        $password .= $lowerChars[random_int(0, strlen($lowerChars) - 1)];
        $password .= $digits[random_int(0, strlen($digits) - 1)];
        $password .= $specials[random_int(0, strlen($specials) - 1)];
    
        // Gộp tất cả ký tự lại để chọn ngẫu nhiên cho phần còn lại
        $allChars = $upperChars . $lowerChars . $digits . $specials;
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }
    
        // Trộn ngẫu nhiên chuỗi để không đoán được vị trí từng loại ký tự
        $password = str_shuffle($password);
    
        return $password;
    }
        

}