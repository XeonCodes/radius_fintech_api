<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\HasApiTokens; // Add this import statement

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // Authenticate user
        $token = $request->bearerToken();
        if ($token === null) {
            return response()->json([
                'status' => 401,
                'message' => 'unauthenticated'
            ], 401);
        }

        $user = $request->user();


        if (!$user) {
            return response()->json([
                "status" => 401,
                "message" => "not unauthorized",
                'data' => [
                    'token' => $token
                ]
            ]);
        }

        return $next($request);


    }
}
