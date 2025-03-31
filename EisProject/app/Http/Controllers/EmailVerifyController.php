<?php

namespace App\Http\Controllers;

use App\Models\EmailVerificationToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EmailVerifyController extends Controller
{
    public function verifyUserEmail(Request $request){
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(),[
            'email'=>'required|email',
            'verification_code'=>'required',
        ]);
        $inputs = $validator->validated();
        $user = User::where('email',$inputs['email'])->where('verification_code',$inputs['verification_code'])->first();
        $token = EmailVerificationToken::where('email',$inputs['email'])->first();

        if($user){
            if($token->expired_at>=now()){
                if($user->markEmailAsVerified()){
                    $token->delete();
                    $user->update(['verification_code'=>'']);                
                    return response()->json([
                        'status'=>'success',
                        'message'=>'Email has been verified succsessfully'
                    ]);
                }else{
                    return response()->json([
                        'status'=>'failed',
                        'message'=>'Email verification failed'
                    ]);
                }
            }else{
                return response()->json([
                    'status'=>'failed',
                    'message'=>'Token expired'
                ]);
            }

            }else{
            return response()->json([
                'status'=>'failed',
                'message'=>'User not found'
            ]);
        }
       
}

    public function setPassword(Request $request){
        $validator = Validator::make($request->all(),[
            'current_password'=>'required',
            'new_password'=>'required|string|min:8|regex:/[A-Z]/|regex:/[a-z]/|regex:/[0-9]/|regex:/[@$!%*?&]/',
            'confirm_password'=>'required'
        ]);
        $inputs = $validator->validated();
        $user = User::where('password',Hash::make($inputs['current_password']));
        if($user){
                if(password_verify($inputs['new_password'],$inputs['confirm_password'])){
                     $inputs['new_password'] = bcrypt($inputs['new_password']);
                    $user->update([
                        'password'=>$inputs['new_password']
                         ]);
                         $token = $user->createToken()->plainTextToken;
                         return response()->json([
                            'status'=>'success',
                            'message'=>'Password is updated successfully',
                            'user'=>$user,
                            'token'=>$token
                         ]);
            }else{
                return response()->json([
                    'status'=>'failed',
                    'message'=>'Confirm password does not match with new password',
                 ]);
            }
        }else{
            return response()->json([
                'status'=>'failed',
                'message'=>'User not found',
             ]);
        }
    }
}

