<?php

namespace CherryneChou\Admin\Http\Controllers;

use CherryneChou\Admin\Models\Administrator;
use CherryneChou\Admin\Serializer\DataArraySerializer;
use CherryneChou\Admin\Services\AuthorizationService;
use CherryneChou\Admin\Transformers\AdministratorTransformer;
use CherryneChou\Admin\Transformers\MenuTransformer;
use CherryneChou\Admin\Support\Helper;
use CherryneChou\Admin\Admin;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use CherryneChou\Admin\Events\UserLogined;
use CherryneChou\Admin\Contracts\AuthorizationServiceInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Event;

class AuthController extends BaseController
{
    /**
     * @var AuthorizationServiceInterface
     */
    protected AuthorizationServiceInterface  $authorizationService;

    /**
     * @param AuthorizationServiceInterface $authorizationService
     */
    public function __construct(AuthorizationServiceInterface $authorizationService)
    {
        $this->authorizationService = $authorizationService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function postLogin()
    {
        $credentials = request()->only([$this->username(), 'password']);

        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($credentials, [
            $this->username()   => 'required',
            'password'          => 'required',
        ]);

        if ($validator->fails()) {
            return $this->failed($validator);
        }

        try {

            $admin = Administrator::query()->where($this->username(), request()->username)->first();

            //判断用户名密码
            if (!$admin || !Hash::check(request()->password, $admin->password)) {
                return $this->failed(trans('admin.username_or_password_is_wrong'));
            }

            if($admin->status == 1){
                return $this->failed(trans('admin.user_forbidden_login'));
            }

            //保存登录 状态
            $admin->increment('login_count');
            $admin->last_login_ip = request()->ip();
            $admin->last_login_time = Carbon::now();

            $admin->save();

            $access_token = $admin->createToken(request()->username)->plainTextToken;

            // 登录成功事件
            Event::dispatch(new UserLogined($admin));

            return $this->success([
                'access_token' => $access_token,
                'token_type' => 'Bearer'
            ]);

        }catch (ModelNotFoundException $exception){
            return $this->failed(trans('admin.user_not_exists'));
        }catch (\Exception $exception){
            return $this->failed($exception->getMessage());
        }
    }


    /**
     * User logout.
     *
     * @return void
     */
    public function getLogout()
    {
        // 撤销用于认证当前请求的令牌...
        request()->user()->currentAccessToken()->delete();

        return $this->success();
    }


    /**
     * 当前用户
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function currentUser()
    {
        $user =  [
            'userid'            =>  request()->user()->id,
            'username'          =>  request()->user()->username,
            'name'              =>  request()->user()->name,
            'avatarUrl'         =>  request()->user()->getAvatar(),
            'roles'             =>  request()->user()->roles->pluck('slug'),
            'allPermissions'    =>  request()->user()->getAllPermissions(),
        ];

        return $this->success($user);
    }


    /**
     * 获取用户菜单列表
     */
    public function getMenuList()
    {
        $filter_resources = $this->authorizationService->filterAuthMenus();

        $menuResources = fractal()
                         ->collection($filter_resources)
                         ->transformWith(new MenuTransformer())
                         ->serializeWith(new DataArraySerializer())
                         ->toArray();

        $menus = Helper::listToTree($menuResources);

        return $this->success($menus);
    }


    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    protected function username()
    {
        return 'username';
    }

}