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
            $table->integer('department_id')->nullable()->default(0)->comment('部门ID');
            $table->string('name')->nullable()->default('');
            $table->string('email',50)->nullable()->default('');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('telephone',11)->nullable()->unique();
            $table->string('avatar')->nullable();
            $table->unsignedInteger('login_count')->nullable()->default(0)->comment('登录次数');
            $table->string('last_login_ip')->nullable()->default('');
            $table->timestamp('last_login_time')->nullable();
            $table->boolean('status')->nullable()->default(0)->comment('是否禁用');
            $table->integer('sort')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->comment('管理员表');

        });

        Schema::create($this->config('database.roles_table'), function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('slug', 50)->unique();
            $table->boolean('status')->nullable()->default(0)->comment('是否禁用');
            $table->boolean('data_scope')->nullable()->default(1)->comment('数据访问权限 1 全部数据 2 自定义数据 3 部门数据 4 部门及以下数据 5 仅本人数据');
            $table->integer('sort')->default(0);
            $table->timestamps();
            $table->comment('角色表');
        });

        Schema::create($this->config('database.departments_table'), function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->integer('parent_id')->default(0)->comment('父级ID');    
            $table->string('principal')->nullable()->comment('负责人');    
            $table->string('email',50)->nullable()->default('');
            $table->string('telephone',11)->nullable()->unique();
            $table->boolean('status')->default(1)->comment('1 正常 2 停用');
            $table->integer('sort')->default(0);    
            $table->timestamps();
            $table->comment('部门表');
        });


        Schema::create($this->config('database.permissions_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50);
            $table->string('locale',50)->nullable()->default('')->comment('国际化标识');
            $table->tinyInteger('type')->nullable()->default(1)->comment('类型1目录、2子目录、3权限');
            $table->string('slug', 50)->unique();
            $table->string('http_method')->nullable();
            $table->text('http_path')->nullable();
            $table->integer('sort')->default(0);
            $table->bigInteger('parent_id')->default(0);
            $table->timestamps();
            $table->comment('权限表');
        });

        Schema::create($this->config('database.menu_table'), function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('菜单名称');
            $table->string('key')->unique()->comment('唯一标识');;
            $table->tinyInteger('type')->nullable()->default(1)->comment('类型1菜单menu、2功能 router');
            $table->string('locale')->comment('菜单国际化标识');
            $table->string('path')->nullable()->default('');
            $table->bigInteger('parent_id')->default(0);
            $table->tinyInteger('is_back_link')->default(0)->comment('是否外链：1、是，0、否');
            $table->string('target')->nullable()->default('')->comment('浏览器跳转类型');
            $table->string('uri')->nullable()->default('')->comment('浏览器跳转地址');
            $table->string('icon', 50)->default('')->nullable();
            $table->integer('sort')->default(0)->nullable();
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

        Schema::create($this->config('database.role_departments_table'), function (Blueprint $table) {
            $table->bigInteger('role_id');
            $table->bigInteger('department_id');
            $table->unique(['role_id', 'department_id']);
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

        Schema::create($this->config('database.dict_table'), function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('');
            $table->string('code')->default('');
            $table->smallInteger('type')->nullable()->default(0);
            $table->tinyInteger('status')->nullable()->default(0);
            $table->tinyInteger("sort")->nullable()->default(0);
            $table->string('remark')->nullable()->default('');
            $table->timestamps();
        });

        Schema::create($this->config('database.dict_data_table'), function (Blueprint $table) {
            $table->id();
            $table->bigInteger("dict_id");
            $table->string("code")->default("");
            $table->string("label")->default("");
            $table->string("value")->default("");
            $table->tinyInteger("is_default")->nullable()->default(0);
            $table->tinyInteger("status")->nullable()->default(0);
            $table->tinyInteger("sort")->nullable()->default(0);
            $table->string("remark")->nullable()->default('');
            $table->timestamps();
        });

        Schema::create($this->config('database.config_table'), function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('配置的key');
            $table->text('value')->nullable()->comment('配置设置的值');
            $table->string('remark')->nullable()->default('');
            $table->timestamps();
        });

        Schema::create($this->config('database.attachment_category_table'), function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('分类名称');
            $table->integer('parent_id')->default(0)->comment('父级ID');
            $table->string('remark')->nullable()->default('');
            $table->timestamps();
        });

        Schema::create($this->config('database.attachment_table'), function (Blueprint $table) {
            $table->id();
            $table->integer('cat_id')->default(0)->comment('分类ID');
            $table->string('filename')->comment('文件名');
            $table->string('path')->comment('文件路径');
            $table->string('extension')->comment('文件后缀');
            $table->string('filesize')->comment('文件大小');
            $table->string('mimetype')->comment('文件mimetype');
            $table->string('driver')->comment('上传方式');
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
        Schema::dropIfExists($this->config('database.departments_table'));
        Schema::dropIfExists($this->config('database.permissions_table'));
        Schema::dropIfExists($this->config('database.menu_table'));
        Schema::dropIfExists($this->config('database.role_users_table'));
        Schema::dropIfExists($this->config('database.role_permissions_table'));
        Schema::dropIfExists($this->config('database.role_department_table'));
        Schema::dropIfExists($this->config('database.user_permissions_table'));
        Schema::dropIfExists($this->config('database.role_menu_table'));
        Schema::dropIfExists($this->config('database.permission_menu_table'));
        Schema::dropIfExists($this->config('database.dict_table'));
        Schema::dropIfExists($this->config('database.dict_data_table'));
        Schema::dropIfExists($this->config('database.config_table'));
        Schema::dropIfExists($this->config('database.attachment_category_table'));
        Schema::dropIfExists($this->config('database.attachment_table'));
    }
};
