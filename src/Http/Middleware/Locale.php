<?php

namespace CherryneChou\Admin\Http\Middleware;

class Locale
{
	public function handle(Request $request, Closure $next)
    {
  		$lang = $request->header('Accept-Language');
	    app()->setLocale($lang);

        return $next($request);
    }
}