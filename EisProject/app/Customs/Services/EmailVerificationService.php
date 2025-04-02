<?php
namespace App\Customs\Services;

use App\Models\EmailVerificationToken;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class EmailVerificationService{

    public function sendVerificationLink(Object $user){
        Notification::send($user,new EmailVerificationNotification($this->genrateVrerificationLink($user->email,$user->verification_code),$user->password));
    }
    public function checkIfEmailIsVerified($user){
        if($user->email_verified_at){
            return response()->json([
                'status'=>'failed',
                'message'=>'Email has been already verified'
            ])->send();
            exit;
        }
    }
    public function verifyEmail(string $email,string $verification_code,string $token){
        $user = User::where('email',$email)->where('verification_code',$verification_code);
        if(!$user){
            if($user->email_verified_at){
                return response()->json([
                    'status'=>'failed',
                    'message'=>'User not found'
                ])->send();
                exit;
        }
        $this->checkIfEmailIsVerified($user);
        $verifiedToken = $this->verifyToken($email,$token);
        if($user->markEmailAsVerified()){
            $verifiedToken->delete();
            return response()->json([
                'status'=>'success',
                'message'=>'Email has been verified succsessfully'
            ])->send();
        }else{
            return response()->json([
                'status'=>'failed',
                'message'=>'Email verification failed'
            ])->send();
        }
    }
}
                        

    public function verifyToken(string $email , string $token){
        $token = EmailVerificationToken::where('email',$email)->where('token',$token)->first();
        if($token){
            if($token->expired_at>=now()){
                return $token;
            }else{
                return response()->json([
                    'status'=>'failed',
                    'message'=>'Token expired'
                ])->send();
                exit;
            }
            }else{
                return response()->json([
                    'status'=>'failed',
                    'message'=>'Invalid token'
                ])->send();
                exit;
        }
    }
    public function genrateVrerificationLink(string $email)  {
        $checkIfTokenExists = EmailVerificationToken::where('email',$email)->first();
        if($checkIfTokenExists) $checkIfTokenExists->delete();
        $token = Str::uuid();
        $url = config('app.url')."?token".$token."&email".$email;
        $saveToken = EmailVerificationToken::create([
            'email'=>$email,
            'token'=>$token,
            'expired_at'=>now()->addDays(7)
        ]);
        if($saveToken){
            return $url;
        }
    }
}