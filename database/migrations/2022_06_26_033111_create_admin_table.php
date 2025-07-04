<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function getConnection()
    {
        return $this->config('database.connection') ?: config('database.default');
    }

    public function config($key)
    {
        return config('admin.'.$key);
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->config('database.users_table'), function (Blueprint $table) {
            $table->id();
            $table->string('username', 120)->unique();
            $table->string('password', 80);
            $table->string('name')->nullable()->default('');
            $table->string('email',50)->nullable()->default('');
            $table->timestamp('email_verified_at')->nullable();

            $table->string('telephone',11)->nullable()->unique();
            $table->string('avatar')->nullable();

            $table->unsignedInteger('login_count')->nullable()->default(0)->comment('登录次数');
            $table->string('last_login_ip')->nullable()->default('');
            $table->timestamp('last_login_time')->nullable();

            $table->boolean('status')->nullable()->default(0)->comment('是否禁用');
            $table->integer('order')->default(0);
        
            $table->timestamps();
            $table->softDeletes();

            $table->comment('管理员表');

        });

        Schema::create($this->config('database.roles_table'), function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('slug', 50)->unique();
            $table->boolean('status')->nullable()->default(0)->comment('是否禁用');
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->comment('角色表');
        });

        Schema::create($this->config('database.permissions_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50);
            $table->string('locale',50)->nullable()->default('')->comment('国际化标识');
            $table->tinyInteger('type')->nullable()->default(1)->comment('类型1目录、2子目录、3权限');
            $table->string('slug', 50)->unique();
            $table->string('http_method')->nullable();
            $table->text('http_path')->nullable();
            $table->integer('order')->default(0);
            $table->bigInteger('parent_id')->default(0);
            $table->timestamps();

             $table->comment('权限表');
        });

        Schema::create($this->config('database.menu_table'), function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('菜单关键字');
            $table->tinyInteger('type')->nullable()->default(1)->comment('类型1菜单、2功能');
            $table->string('locale')->comment('菜单国际化标识');
            $table->string('path')->nullable()->default('');
            $table->bigInteger('parent_id')->default(0);
            $table->string('target')->nullable()->default('')->comment('浏览器跳转类型');
            $table->string('uri')->nullable()->default('')->comment('浏览器跳转地址');
            $table->string('icon', 50)->default('')->nullable();
            $table->integer('order')->default(0)->nullable();
            $table->boolean('status')->default(1)->comment('菜单状态（0正常 1停用）');
            $table->boolean('visible')->default(1)->comment('菜单状态（0显示 1隐藏）');

            $table->timestamps();
            $table->comment('菜单表');
        });

        Schema::create($this->config('database.role_users_table'), function (Blueprint $table) {
            $table->bigInteger('role_id');
            $table->bigInteger('user_id');
            $table->unique(['role_id', 'user_id']);
            $table->timestamps();
        });

        Schema::create($this->config('database.role_permissions_table'), function (Blueprint $table) {
            $table->bigInteger('role_id');
            $table->bigInteger('permission_id');
            $table->unique(['role_id', 'permission_id']);
            $table->timestamps();
        });


        Schema::create($this->config('database.user_permissions_table'), function (Blueprint $table) {
            $table->integer('user_id');
            $table->integer('permission_id');
            $table->index(['user_id', 'permission_id']);
            $table->timestamps();
        });


        Schema::create($this->config('database.role_menu_table'), function (Blueprint $table) {
            $table->bigInteger('role_id');
            $table->bigInteger('menu_id');
            $table->unique(['role_id', 'menu_id']);
            $table->timestamps();
        });

        Schema::create($this->config('database.permission_menu_table'), function (Blueprint $table) {
            $table->bigInteger('permission_id');
            $table->bigInteger('menu_id');
            $table->unique(['permission_id', 'menu_id']);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->config('database.users_table'));
        Schema::dropIfExists($this->config('database.roles_table'));
        Schema::dropIfExists($this->config('database.permissions_table'));
        Schema::dropIfExists($this->config('database.menu_table'));
        Schema::dropIfExists($this->config('database.role_users_table'));
        Schema::dropIfExists($this->config('database.role_permissions_table'));
        Schema::dropIfExists($this->config('database.user_permissions_table'));
        Schema::dropIfExists($this->config('database.role_menu_table'));
        Schema::dropIfExists($this->config('database.permission_menu_table'));
    }
};
