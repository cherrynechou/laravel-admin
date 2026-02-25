<?php
namespace CherryneChou\Admin\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;


trait DataScope
{
	/**
     * 应用数据权限范围
     */
	public function scopeApplyDataScope(Builder $query, ?string $tableAlias = null)
	{
		if(request()->user()->isAdministrator()){
			return $query;
		}

		$role = $user->roles->first();
        if (!$role) {
            return $query->whereRaw('1 = 0'); // 无角色则无数据
        }


        $dataScope = $role->data_scope ?? 'self';
	}
}