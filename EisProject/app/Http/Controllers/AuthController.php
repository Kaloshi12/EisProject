<?php

namespace App\Http\Controllers;

use App\Customs\Services\EmailVerificationService;
use App\Models\EmailVerificationToken;
use App\Models\User;
use Dotenv\Validator;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log as FacadesLog;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    public function login(Request $request)
{
    $validator = \Illuminate\Support\Facades\Validator::make($request->all(),[
        'email'=>'required|email',
        'password'=> 'required|min:8'      
    ]);
    
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }
    $inputs = $validator->validated();
   
    if($token = Auth::attempt($inputs)){
        return response()->json([
            'message' => 'success login',
            'token' => $token,
            'user' => Auth::user()
        ]);
    }
    return response()->json([
        'message' => 'Failed to login. Invalid credentials.'
    ], 401);
}

    

public function logout() {

   Auth::logout();
    return response()->json([
    'message'=>'success logout'
   ]);
}


}