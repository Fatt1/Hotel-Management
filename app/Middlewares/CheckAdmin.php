<?php

declare(strict_types=1);

namespace App\Middlewares;

use Closure;
use Illuminate\Http\Request;

class CheckAdmin
{
    public function handle(Request $request, Closure $next){
       if(auth()->guard('staff')->check()) {
            return $next($request);
       } else {
           return redirect()->route('/');
       }
    }
}
