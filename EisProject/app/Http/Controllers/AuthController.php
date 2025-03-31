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
   
    $user = User::where('email', $inputs['email'])->first();

    if ($user && Hash::check($inputs['password'], $user->password)) {
        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'message' => 'success',
            'user'=>$user,
            'token' => $token,
            'token_type' => 'bearer',
           
        ]);
    }

    return response()->json([
        'message' => 'Failed to login. Invalid credentials.'
    ], 401);
}

    

public function logout(Request $request) {

    $request->user()->currentAccessToken()->delete();
    return response()->json([
    'message'=>'success logout'
   ]);
}


}