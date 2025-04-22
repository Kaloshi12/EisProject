<?php

namespace App\Http\Controllers;

use App\Customs\Services\EmailVerificationService;
use App\Models\EmailVerificationToken;
use App\Models\User;
use Dotenv\Validator;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log as FacadesLog;
use Tymon\JWTAuth\Facades\JWTAuth;

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
   /**
 * User Login
 *
 * @operationId Login
 * @tags Authentication
 */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            /**
             * The user's credentials.
             * @var array{email: string, password: string}
             * @example {"email": "user@epoka.edu.al", "password": "password123"}
             */
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($token = Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => Auth::user(),
            ]);
        }

        return response()->json([
            'message' => 'Invalid credentials',
        ], 401);
    }

    /**
     * User Logout
     *
     * @operationId Logout
     * @tags Authentication
     */
    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout user",
     *     tags={"Authentication"},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out"
     *     ),
     * )
     */
    
    public function logout(Request $request)
    {
        Auth::logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
}
