<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HandleCors
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        return $response->header('Access-Control-Allow-Origin', 'http://localhost:3000')
                        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                        ->header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Authorization')
                        ->header('Access-Control-Allow-Credentials', 'true');
    }
}
// This middleware handles CORS (Cross-Origin Resource Sharing) for the application.
// It allows requests from a specific origin (http://localhost:3000) and specifies the allowed methods, headers, and credentials.
// It is typically used to enable cross-origin requests from a frontend application running on a different domain or port.