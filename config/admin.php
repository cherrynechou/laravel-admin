<?php
return [
    //版本一用于后台
    'route'     =>  [
        'prefix' => env('ADMIN_ROUTE_PREFIX', 'v1'),

        'namespace' => 'App\\Admin\\Controllers',

        'middleware' => [ 'auth:sanctum' ],
    ],
    /*
    |--------------------------------------------------------------------------
    |
    | The installation directory of the controller and routing configuration
    | files of the administration page. The default is `app/Admin`, which must
    | be set before running `artisan admin::install` to take effect.
    |
    */
    'directory' => app_path('Admin'),


    /*
    |--------------------------------------------------------------------------
    | Access via `https`
    |--------------------------------------------------------------------------
    |
    | If your page is going to be accessed via https, set it to `true`.
    |
    */
    'https' => env('ADMIN_HTTPS', false),

    /*
    |--------------------------------------------------------------------------
    | User default avatar
    |--------------------------------------------------------------------------
    |
    | Set a default avatar for newly created users.
    |
    */
    'default_avatar' => '/vendor/dist/images/user2-160x160.png',

    /*
    |
    | Permission settings for all admin pages.
    |
    */
    'permission' => [
        // Whether enable permission.
        'enable' => true,

        // All method to path like: auth/users/*/edit
        // or specific method to path like: get:auth/users.
        'except' => [
            '/',
            'oauth/login',
            'oauth/logout',
            'currentUser',
            'getMenuList',
        ],
    ],
    /*
    | File system configuration for form upload files and images, including
    | disk and upload path.
    |
    */
    'upload'    =>  [

        // Disk in `config/filesystem.php`.
        'disk' => 'admin',

        // Image and file upload path under the disk above.
        'directory' => [
            'image' => 'images',
            'file'  => 'files',
        ],
    ],
    /*
    |
    | Here are database settings for dcat-admin builtin model & tables.
    |
    */
    'database'  =>  [
        // Database connection for following tables.
        'connection' => '',

        //Database database table prefix name
        'prefix' => env('DB_PREFIX', ''),    //项目表前缀

        // User tables and model.
        'users_table' => 'admin_users',
        'users_model' => CherryneChou\Admin\Models\Administrator::class,

        // Role table and model.
        'roles_table' => 'admin_roles',
        'roles_model' => CherryneChou\Admin\Models\Role::class,

        // Permission table and model.
        'permissions_table' => 'admin_permissions',
        'permissions_model' => CherryneChou\Admin\Models\Permission::class,

        // Menu table and model.
        'menu_table' => 'admin_menu',
        'menu_model' => CherryneChou\Admin\Models\Menu::class,

        // Pivot table for table above.
        'role_users_table'       => 'admin_role_users',
        'role_permissions_table' => 'admin_role_permissions',
        'role_menu_table'        => 'admin_role_menu',
        'permission_menu_table'  => 'admin_permission_menu',
    ],
    'default_password'      =>      env('DEFAULT_PASSWORD', '123456'),
];
