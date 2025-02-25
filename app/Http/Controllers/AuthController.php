<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SessionLogin;
use Illuminate\Support\Facades\Hash;
use App\Models\User;    
use Carbon\Carbon;

class AuthController extends Controller
{
    //
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
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
        if (empty(request('type'))){
            return response()->json(['data' => ["message" => "Tài khoản đăng nhập phải là admin !"]], 401);
        }
        SessionLogin::where('user_id', auth()->user()->id)->where('deleted_at', null)->update(['deleted_at' => Carbon::now()]);
        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        return response()->json(auth()->user());
    }

    public function update(Request $req)
    {
        $user = auth()->user();
        if (!empty($user)){
            if (!empty($req['username'])){
                $user->username = $req['username'];
            }
            if (!empty($req['email'])){
                if (!empty(User::whereNull('deleted_at')->where('email', $req['email'])->first()) && $req['email'] != $user->email){
                    return response(["data" => ["message" => "Email đã tồn tại!"]],400);
                }else{
                   
                    $user->email = $req['email'];
                }   
            }
            if (!empty($req['password'])){
                $user->password = Hash::make($req['password']);
            }
            if (!empty($req['address'])){
                $user->address = $req['address'];
            } 
            if (!empty($req['phone'])){
                $user->phone = $req['phone'];
            }
            if (!empty($req['avatar'])){
                $user->avatar = $req['avatar'];
            }
            $user->save();
        }
        return response()->json($user);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
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
