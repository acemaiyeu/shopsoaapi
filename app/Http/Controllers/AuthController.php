<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SessionLogin;
use App\Models\User;
use App\Transformers\UserClientTransformer;
use App\Models\ModelsQuery\UserModel;

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
        $this->middleware('auth:api', ['except' => ['login','register', 'profile', 'logout','updateProfile']]);
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
        $user = User::where('email',$req->email)->where('phone', $req->phone)->exists();
        if (!$user){
            $user = User::create([
                'fullname' => $req->fullname,
                'phone' => $req->phone,
                'email' => $req->email,
                'password' => bcrypt($req->password)
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
}