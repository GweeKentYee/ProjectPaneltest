<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\PersonalAccessToken;


class InactiveTokens
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    // public function handle(Request $request, Closure $next)
    // {

    //     $token = PersonalAccessToken::findToken($request->bearerToken());
    //     if (!empty($token->last_used_at)) {

    //         $test = $token->last_used_at;

    //         if ($test->isBefore(Carbon::now()))
    //         {
    //             return response("hehe");
    //         } else {
    //             return response($token);
    //         }
    //     } else {
    //         return response("xd");
    //     }
    // }
    public function handle(Request $request, Closure $next)
    {
        $token = PersonalAccessToken::findToken($request->bearerToken());
        if (!empty($token->last_used_at) && $token->last_used_at->addMinutes(15)->isBefore(Carbon::now())) {
            $token->delete();
        }
        return $next($request);
    }

}
