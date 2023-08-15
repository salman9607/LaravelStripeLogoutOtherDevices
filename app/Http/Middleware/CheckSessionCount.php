<?php

namespace App\Http\Middleware;

use App\Events\NewNotificationEvent;
use App\Models\UserSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Crypt;

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
        if(count($allSession) >= 2) {
            $decrypt = Crypt::decryptString(session()->get('user_pas'));
            auth()->logoutOtherDevices($decrypt);

            UserSession::where([['user_id', '=', auth()->id()], ['session_id', '!=', session()->getId()]])->delete();

            event(new NewNotificationEvent('Test Done'));
        }

        return $next($request);
    }
}
