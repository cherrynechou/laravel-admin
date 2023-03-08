<?php

namespace CherryneChou\Admin\Http\Controllers;

use CherryneChou\Admin\Models\Administrator;
use CherryneChou\Admin\Traits\RestfulResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use RestfulResponse;


    public function postLogin()
    {
        $username = request()->username;
        $password = request()->password;

        try {

            $admin = Administrator::query()->where('username',$username)->first();

            //判断用户名密码
            if (!$admin || !Hash::check(request()->password, $admin->password)) {
                return $this->failed('用户名或者密码不正确');
            }

            if($admin->status == 1){
                return $this->failed('用户禁止登录');
            }

            //保存登录 状态
            $admin->increment('login_count');
            $admin->last_login_ip = request()->ip();
            $admin->last_login_time = Carbon::now();

            $admin->save();

            $access_token = $admin->createToken(request()->username)->plainTextToken;

            return $this->success([
                'token_type' => 'Bearer',
                'access_token' => $access_token,
            ]);

        }catch (ModelNotFoundException $exception){

            return $this->failed('用户不存在');

        }catch (\Exception $exception){

            return $this->failed($exception->getMessage());

        }

    }


}