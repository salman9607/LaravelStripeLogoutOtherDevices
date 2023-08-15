<?php

namespace App\Http\Middleware;

use App\Events\NewNotificationEvent;
use App\Models\UserSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionCount
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allSession = UserSession::where([['user_id', '=', auth()->id()], ['session_id', '!=', session()->getId()]])->get();

        if(!empty($allSession) && count($allSession) >= 2) {
            auth()->logoutOtherDevices(auth()->user()->password);

            UserSession::where([['user_id', '=', auth()->id()], ['session_id', '!=', session()->getId()]])->delete();

            event(new NewNotificationEvent('Test Done'));
        }

        return $next($request);
    }
}
