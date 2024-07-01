<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserOrAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $currentUser = Auth::user();
        $requestedUserId = $request->route('user')->id;

        if ($currentUser->id != $requestedUserId && !$currentUser->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Autorisation insuffisante.',
            ], 403);
        }

        return $next($request);
    }
}
