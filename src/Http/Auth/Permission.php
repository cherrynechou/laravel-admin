<?php

namespace CherryneChou\Admin\Http\Auth;

use Illuminate\Contracts\Support\Arrayable;
use Symfony\Component\HttpFoundation\Response;

class Permission
{
    protected static $errorHandler;

    /**
     * Check permission.
     *
     * @param  string|array|Arrayable  $permission
     * @return true|void
     */
    public static function check($permission)
    {
        if (static::isAdministrator()) {
            return true;
        }

        if (is_array($permission) || $permission instanceof Arrayable) {
            collect($permission)->each(function ($permission) {
                call_user_func([self::class, 'check'], $permission);
            });

            return false;
        }

        if (request()->user()->cannot($permission)) {
            static::error();
        }
    }

    /**
     * Roles allowed to access.
     *
     * @param  string|array|Arrayable  $roles
     * @return true|void
     */
    public static function allow($roles)
    {
        if (static::isAdministrator()) {
            return true;
        }

        if (! request()->user()->inRoles($roles)) {
            static::error();
        }
    }

    public static function error()
    {
        abort(403, '没有权限');
    }


    /**
     * Don't check permission.
     *
     * @return bool
     */
    public static function free()
    {
        return true;
    }

    /**
     * Roles denied to access.
     *
     * @param  string|array|Arrayable  $roles
     * @return true|void
     */
    public static function deny($roles)
    {
        if (static::isAdministrator()) {
            return true;
        }

        if (request()->user()->inRoles($roles)) {
            static::error();
        }
    }


    /**
     * If current user is administrator.
     *
     * @return mixed
     */
    public static function isAdministrator()
    {
        $roleModel = config('admin.database.roles_model');

        return ! config('admin.permission.enable') || request()->user()->isRole($roleModel::ADMINISTRATOR);
    }

    /**
     * @param  \Closure  $callback
     * @return void
     */
    public static function registerErrorHandler(\Closure $callback)
    {
        static::$errorHandler = $callback;
    }
}
