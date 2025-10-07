<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tool;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class UserMenu
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
        $main=array();  
        if(auth()->user()){  
            //dd(auth()->user());
            $main=Tool::where('i_type','1')->where('i_show_menu',1)->get();
            foreach($main as $row)
                
            $row['sub']=DB::table('tools')
            ->join('tool_groups', 'tools.id', '=', 'tool_groups.tool_id')
            ->join('user_groups', 'user_groups.group_id', '=', 'tool_groups.group_id')
            ->select('tools.*' )
            ->where('user_groups.user_id',auth()->user()->id)
            ->where('tools.i_parent_id',$row->id)
            ->orderby('tools.i_order','asc')
            ->get() ;
        }
      //  return response()->json([ 'menu' => $main]);
        
        View::share('main',$main); 
        return $next($request);
    }
}
