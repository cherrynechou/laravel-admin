<?php
namespace CherryneChou\Admin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DataScope
{
   	public function handle(Request $request, Closure $next)
    {
        if (! Admin::guard()->guest()) {
            return $next($request);
        }

        if($request->isMethod('POST')){   //添加
            $request->merge([
                'created_id' => $request->user()->id,
            ]);

        }else if($request->isMethod('PUT') || $request->isMethod('PATCH')){  //修改
            $request->merge([
                'updated_id' => $request->user()->id,
            ]);
        }
        
        // 将用户权限信息注入请求，方便调试
		return $next($request);
    }
}