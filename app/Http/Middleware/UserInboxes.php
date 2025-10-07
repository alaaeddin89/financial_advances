<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Auth;

class UserInboxes
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
        
        
        $userInboxMessages= Message::where('receiver_id',  auth::id())
        ->where('status',0)
      //  ->whereNull('parent_id') // Only show top-level messages
        ->latest()
        ->get(); 
            
        
        View::share('userInboxMessages',$userInboxMessages); 
        return $next($request);
    }
}
