<?php
namespace CherryneChou\Admin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DataScope
{
   	public function handle(Request $request, Closure $next)
    {
		return $next($request);
    }
}