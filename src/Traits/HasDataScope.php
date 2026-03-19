<?php
namespace CherryneChou\Admin\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;
use CherryneChou\Admin\Models\Departments;


trait HasDataScope
{
    protected array $dataScopeRelations = [
        'all' => 1,                 // 全部数据
        'custom' => 2,              // 自定义部门
        'department' => 3,          // 本部门数据
        'departmentWithChild' => 4, // 本部门及子部门数据
        'self' => 5,                // 仅本人数据
    ];


	/**
     * 应用数据权限范围
     */
	public function scopeApplyDataScope(Builder $query)
	{
        $user = request()->user();

        //若当前用户是管理员
		if($user->isAdministrator()){
			return $query;
		}

        $userIds = $this->getAllUserIds($user);


        if ($userIds->isEmpty()) {
            return $query;
        }

        return $query->whereIn('created_id',$userIds);
	}


    public function getAllUserIds($currentUser)
    {
        // 收集所有权限条件
        $conditions = [
            'all'  => false,
            'self' => false,      // 本人数据条件
            'dept' => [],      // 部门数据条件
            'custom' => []     // 自定义部门条件
        ];

        //用户权限
        foreach ($currentUser->roles as $role) {
            switch ( $role->data_scope ) {
                case $this->dataScopeRelations['self']:     //本人数据
                    $conditions['self'] = true;
                    break;
                case $this->dataScopeRelations['custom']:     //自定义数据
                    $departmentIds = $role->departments->pluck("id")->toArray();
                    $conditions['custom'] = array_merge($conditions['custom'], $departmentIds);
                    break;
                case $this->dataScopeRelations['department']:   //本部门数据
                    $conditions['dept'] = [$currentUser->department_id];
                    break;
                case $this->dataScopeRelations['departmentWithChild']:  //本部及子部门数据
                    $conditions['dept'] = [$currentUser->department_id];

                    $departmentModel = new Departments();
                    $departmentIds = $departmentModel->findFollowDepartments($departmentsId);

                    $conditions['dept'] = array_merge($conditions['dept'], $departmentIds);

                    break;
                default:
                    $conditions['all'] = true;
                    break;
            }
        }


        // 如果有all权限，直接返回全部数据
        if($conditions['all']){
            return Collection::make();
        }

        $userIds = Collection::make();

        if($conditions['self']){
            $userIds = $userIds->push($currentUser->id);
        }else if($conditions['custom']){
            $userIds = $userIds->merge($this->getUserIdsByDepartmentId($conditions['custom']));
        }else if($conditions['dept']){
            $userIds = $userIds->merge($this->getUserIdsByDepartmentId($conditions['dept']));
        }

        // 如果查出来没有任何用户，说明无法查看数据
        if ($userIds->isEmpty()) {
            $userIds = $userIds->push(0);
        }

        return $userIds->uniqid();

    }


    protected function getUserIdsByDepartmentId(array|Collection $departmentIds): Collection
    {
        $userModel = config('admin.database.users_model');

        return $userModel->whereIn('department_id', $departmentIds)->pluck('id');
    }


}