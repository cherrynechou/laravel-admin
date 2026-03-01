<?php
return [
    //版本一用于后台
    'route'     =>  [
        'prefix' => env('ADMIN_ROUTE_PREFIX', 'admin'),

        'namespace' => 'App\\Admin\\Controllers',

        'middleware' => ['api'],

        'auth_middleware' => ['auth:sanctum','admin'],
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
    | Authentication settings for all admin pages. Include an authentication
    | guard and a user provider setting of authentication driver.
    |
    | You can specify a controller for `login` `logout` and other auth routes.
    |
    */
    'auth' => [
        'enable' => true,

        'controller' => App\Admin\Controllers\AuthController::class,

        'guard' => 'admin',

        'guards' => [
            'admin' => [
                'driver'   => 'sanctum',
                'provider' => 'admin',
            ],
        ],

        'providers' => [
            'admin' => [
                'driver' => 'eloquent',
                'model'  => CherryneChou\Admin\Models\Administrator::class,
            ],
        ],

        // All method to path like: auth/users/*/edit
        // or specific method to path like: get:auth/users.
        'except' => [
            'auth/login',
            'auth/logout',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User operation log setting
    |--------------------------------------------------------------------------
    |
    | By setting this option to open or close operation log in laravel-admin.
    |
    */
    'operation_log' => [

        'enable' => true,

        /*
         * Only logging allowed methods in the list
         */
        'allowed_methods' => ['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'CONNECT', 'OPTIONS', 'TRACE', 'PATCH'],

        'securt_fields' = ['password','password_confirmation'],

        /*
         * Routes that will not log to database.
         *
         * All method to path like: admin/auth/logs
         * or specific method to path like: get:admin/auth/logs.
         */
        'except' => [
            env('ADMIN_ROUTE_PREFIX', 'admin').'/auth/logs*',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User default avatar
    |--------------------------------------------------------------------------
    |
    | Set a default avatar for newly created users.
    |
    */
    'default_avatar' => '/vendor/laravel-admin/dist/images/user2-160x160.png',

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
            'oauth/logout',
            'currentUser',
            'getMenuList',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | menu setting
    |--------------------------------------------------------------------------
    |
    */
    'menu' => [
        'cache' => [
            // enable cache or not
            'enable' => false,
            'store'  => 'file',
        ],

        // Whether enable menu bind to a permission.
        'bind_permission' => true,

        // Whether enable role bind to menu.
        'role_bind_menu' => true,

        // Whether enable permission bind to menu.
        'permission_bind_menu' => true,
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

        // Role Department table and model
        'departments_table' => 'admin_departments',
        'departments_model' => CherryneChou\Admin\Models\Department::class,

        // Permission table and model.
        'permissions_table' => 'admin_permissions',
        'permissions_model' => CherryneChou\Admin\Models\Permission::class,

        // Menu table and model.
        'menu_table' => 'admin_menu',
        'menu_model' => CherryneChou\Admin\Models\Menu::class,

        //Post table and model.
        'posts_table' => 'admin_post',
        'posts_model' => CherryneChou\Admin\Models\Post::class,

        //Config table and model
        'config_table'=> 'admin_system_config',
        'config_model'=> CherryneChou\Admin\Models\SystemConfig::class,

        //Operation log table and model
        'operation_log_table' => 'admin_operation_log',
        'operation_log_model' => CherryneChou\Admin\Models\OperationLog::class,

        //Attachment Category table and model
        'attachment_category_table'=> 'admin_attachment_category',
        'attachment_category_model'=> CherryneChou\Admin\Models\AttachmentCategory::class,

        //Attachment table and model
        'attachment_table'=> 'admin_attachment',
        'attachment_model'=> CherryneChou\Admin\Models\Attachment::class,

        // Pivot table for table above.
        'user_permissions_table' => 'admin_user_permissions',
        'role_users_table'       => 'admin_role_users',
        'role_permissions_table' => 'admin_role_permissions',
        'role_departments_table' => 'admin_role_departments',
        'role_menu_table'        => 'admin_role_menu',
        'permission_menu_table'  => 'admin_permission_menu',
        'dict_table'             => 'admin_dict',
        'dict_data_table'        => 'admin_dict_data',
    ],
    'default_password'      =>      env('DEFAULT_PASSWORD', '123456'),
];
