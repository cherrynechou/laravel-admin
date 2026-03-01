<?php

namespace CherryneChou\Admin\Http\Middleware;

use CherryneChou\Admin\Admin;
use Illuminate\Http\Request;

class Bootstrap
{
	public function handle(Request $request, \Closure $next)
    {
    	$this->fireEvents();
    	return $next($request);
    }


    protected function fireEvents()
    {
        Admin::callBooting();

        Admin::callBooted();
    }
}