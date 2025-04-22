<?php

namespace App\Http\Controllers;

use App\Models\EmailVerificationToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EmailVerifyController extends Controller
{
    /**
     * Show the email verification form.
     */
    public function showVerificationForm()
    {
        return view('auth.verify-code');
    }

    /**
     * Verify User Email.
     */
    public function verifyUserEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'verification_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $inputs = $validator->validated();
        $user = User::where('email', $inputs['email'])
                    ->where('verification_code', $inputs['verification_code'])
                    ->first();

        $token = EmailVerificationToken::where('email', $inputs['email'])->first();

        if (!$user) {
            return back()->withErrors(['verification_code' => 'Invalid verification code.'])->withInput();
        }

        if (!$token || $token->expired_at < now()) {
            return back()->withErrors(['verification_code' => 'Verification code has expired.'])->withInput();
        }

        // Mark email as verified
        $user->markEmailAsVerified();
        $user->update(['verification_code' => null]);

        // Remove used token
        $token->delete();

        return redirect()->route('email.verified')->with('success', 'Email verified successfully.');
    }

    /**
     * Set new password.
     */
    public function setPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'current_password' => 'required',
            'new_password' => [
                'required', 
                'string', 
                'min:8', 
                'regex:/[A-Z]/', 
                'regex:/[a-z]/', 
                'regex:/[0-9]/', 
                'regex:/[@$!%*?&]/'
            ],
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $inputs = $validator->validated();
        $user = User::where('email', $inputs['email'])->first();

        if (!$user || !Hash::check($inputs['current_password'], $user->password)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Current password is incorrect.',
            ], 401);
        }

        // Update password
        $user->update([
            'password' => Hash::make($inputs['new_password']),
        ]);

        // Generate a new token for the user
        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Password updated successfully.',
            'user' => $user,
            'token' => $token,
        ], 200);
    }
}