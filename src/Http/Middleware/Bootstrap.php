<?php

namespace CherryneChou\Admin\Http\Middleware;

use CherryneChou\Admin\Admin;

class Boostrap
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