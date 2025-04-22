<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyEmailMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user->email_verified_at) {
            return response()->json([
                'status' => 'failed',
                'message' => 'You should verify your email',
            ], 400);
        }

        return $next($request);
    }
}
