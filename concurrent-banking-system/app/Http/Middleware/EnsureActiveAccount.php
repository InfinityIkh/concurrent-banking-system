<?php

namespace App\Http\Middleware;

use App\Enums\AccountStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveAccount
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $account = $user->account;
        if($account->status !== AccountStatus::active){
            return response()->json([
                'message' => 'Your account is currently inactive or closed. Please contact support for assistance'
            ],403);
        }
        return $next($request);
    }
}
