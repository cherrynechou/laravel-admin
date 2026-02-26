<?php
namespace CherryneChou\Admin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DataScope
{
   	public function handle(Request $request, Closure $next)
    {
        // 将用户权限信息注入请求，方便调试
        $request->merge([
            'admin_user_id' => $request->user()->id,
            'admin_department_id' => $request->user()->department_id,
        ]);

		return $next($request);
    }
}