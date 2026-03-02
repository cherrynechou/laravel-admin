<?php

namespace CherryneChou\Admin\Http\Middleware;

use CherryneChou\Admin\Facades\Admin;
use Illuminate\Http\Request;

class Bootstrap
{
	public function handle(Request $request, \Closure $next)
    {
        Admin::bootstrap();
    	return $next($request);
    }
}