<?php

namespace CherryneChou\Admin\Models;

use Illuminate\Database\Seeder;

class AdminTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $createdAt = date('Y-m-d H:i:s');

        // create a user.
        Administrator::truncate();
        Administrator::create([
            'username'   => 'admin',
            'password'   => bcrypt('admin'),
            'name'       => 'Administrator',
            'created_at' => $createdAt,
        ]);

        // create a role.
        Role::truncate();
        Role::create([
            'name'       => 'Administrator',
            'slug'       => Role::ADMINISTRATOR,
            'created_at' => $createdAt,
        ]);

        // add role to user.
        Administrator::first()->roles()->save(Role::first());

        //create a permission
        Permission::truncate();
        Permission::insert([
            [
                'id'          => 1,
                'name'        => 'Auth management',
                'slug'        => 'auth-management',
                'http_method' => '',
                'http_path'   => '',
                'parent_id'   => 0,
                'order'       => 1,
                'created_at'  => $createdAt,
            ],[
                'id'          => 2,
                'name'        => 'Users',
                'slug'        => 'users',
                'http_method' => '',
                'http_path'   => '/auth/users*',
                'parent_id'   => 1,
                'order'       => 2,
                'created_at'  => $createdAt,
            ],[
                'id'          => 3,
                'name'        => 'Roles',
                'slug'        => 'roles',
                'http_method' => '',
                'http_path'   => '/auth/roles*',
                'parent_id'   => 1,
                'order'       => 3,
                'created_at'  => $createdAt,
            ],[
                'id'          => 4,
                'name'        => 'Permissions',
                'slug'        => 'permissions',
                'http_method' => '',
                'http_path'   => '/auth/permissions*',
                'parent_id'   => 1,
                'order'       => 4,
                'created_at'  => $createdAt,
            ],[
                'id'          => 5,
                'name'        => 'Menu',
                'slug'        => 'menu',
                'http_method' => '',
                'http_path'   => '/auth/menu*',
                'parent_id'   => 1,
                'order'       => 5,
                'created_at'  => $createdAt,
            ],
        ]);

//        Role::first()->permissions()->save(Permission::first());

        // add default menus.
        Menu::truncate();
        Menu::insert([
            [
                'parent_id'     => 0,
                'order'         => 1,
                'name'           => 'Dashboard',
                'locale'        => 'menu.dashboard',
                'icon'          => 'DashboardOutlined',
                'uri'           => '/',
                'path'          => '/',
                'status'        => 1,
                'visible'       => 1,
                'created_at'    => $createdAt,
            ],[
                'parent_id'     => 1,
                'order'         => 2,
                'name'           => 'Analysis',
                'locale'        => 'menu.dashboard.analysis',
                'icon'          => '',
                'uri'           => '/',
                'path'          => '/dashboard',
                'status'        => 1,
                'visible'       => 1,
                'created_at'    => $createdAt,
            ],[
                'parent_id'     => 0,
                'order'         => 3,
                'name'          => 'Admin',
                'locale'        => 'menu.admin',
                'icon'          => 'WindowsOutlined',
                'uri'           => '',
                'path'          => '/auth',
                'status'        => 1,
                'visible'       => 1,
                'created_at'    => $createdAt,
            ],[
                'parent_id'     => 3,
                'order'         => 4,
                'name'           => 'Users',
                'locale'        => 'menu.admin.user',
                'icon'          => '',
                'uri'           => '',
                'path'          => '/auth/users',
                'status'        => 1,
                'visible'       => 1,
                'created_at'    => $createdAt,
            ],[
                'parent_id'     => 3,
                'order'         => 5,
                'name'           => 'Roles',
                'locale'        => 'menu.admin.role',
                'icon'          => '',
                'uri'           => '',
                'path'          => '/auth/roles',
                'status'        => 1,
                'visible'       => 1,
                'created_at'    => $createdAt,
            ],[
                'parent_id'     => 3,
                'order'         => 6,
                'name'           => 'Permission',
                'locale'        => 'menu.admin.permission',
                'icon'          => '',
                'uri'           => 'permissions',
                'path'          => '/auth/permissions',
                'status'        => 1,
                'visible'       => 1,
                'created_at'    => $createdAt,
            ],[
                'parent_id'     => 3,
                'order'         => 7,
                'name'           => 'Menu',
                'locale'        => 'menu.admin.menu',
                'icon'          => '',
                'uri'           => '',
                'path'          => '/auth/menu',
                'status'        => 1,
                'visible'       => 1,
                'created_at'    => $createdAt,
            ],
        ]);
    }
}
