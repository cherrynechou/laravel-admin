<?php

namespace CherryneChou\Admin\Http\Middleware;

use CherryneChou\Admin\Admin;

class Application
{
    public function handle($request, \Closure $next, $app = null)
    {
        if ($app) {
            Admin::app()->switch($app);
        }

        return $next($request);
    }
}
