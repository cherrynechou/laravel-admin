<?php
namespace CherryneChou\Admin\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;


trait DataScope
{
    protected $dataScopeRelations = [
        'all' => 1,                 // 全部数据
        'custom' => 2,              // 自定义部门
        'department' => 3,          // 本部门数据
        'departmentWithChild' => 4, // 本部门及子部门数据
        'self' => 5,                // 仅本人数据
    ];


	/**
     * 应用数据权限范围
     */
	public function scopeApplyDataScope(Builder $query, ?string $tableAlias = null)
	{
        $user = request()->user();

        //若当前用户是管理员
		if($user->isAdministrator()){
			return $query;
		}

        // 收集所有权限条件
        $conditions = [
            'self' => [],      // 本人数据条件
            'dept' => [],      // 部门数据条件
            'custom' => []     // 自定义部门条件
        ];

        //用户权限
        foreach ($user->roles as $role) {
            switch ($role->data_scope ) {
                case $this->dataScopeRelations['self']:
                    break;
                case $this->dataScopeRelations['dept']:
                    break;
                case $this->dataScopeRelations['department']:
                    break;
                case $this->dataScopeRelations['departmentWithChild']:
                    break;
                default:
                    break;
            }

        }

        return $query;
	}
}