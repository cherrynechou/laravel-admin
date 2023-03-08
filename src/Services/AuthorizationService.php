<?php
namespace CherryneChou\Admin\Services;

use CherryneChou\Admin\Models\Menu;

class AuthorizationService
{
    /**
     * 根据用户过滤菜单
     * @return \Illuminate\Support\Collection
     */
    public function filterAuthMenus()
    {
        $user = request()->user();

        //是否为超级管理员
        $is_administrator = $user->isAdministrator();
        //用户权限

        //用户角色

        $resources = Menu::query()->orderBy('order','ASC')->scopeQuery(function($query){
            return $query->where('status',1);
        })->all();

        $permissionIds = $user->allPermissions()->pluck('id')->toArray();
        $userRolesIds = $user->roles()->pluck('id')->toArray();

        $filter_resources = collect($resources)->filter(function($item) use ($permissionIds, $userRolesIds, $is_administrator){
            if($is_administrator){
                return true;
            }

            $roles = $item->roles->pluck('id')->toArray();
            foreach ($userRolesIds as $role) {
                if (in_array($role, $roles)) {
                    return true;
                }
            }
            $permissions = (array) $item->permission;
            foreach ($permissions as $permissionId) {
                if (in_array($permissionId, $permissionIds)) {
                    return true;
                }
            }

            return false;
        });

        return $filter_resources;
    }
}