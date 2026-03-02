<?php

namespace CherryneChou\Admin\Http\Controllers;

use CherryneChou\Admin\Models\Administrator;
use CherryneChou\Admin\Serializer\DataArraySerializer;
use CherryneChou\Admin\Services\AuthorizationService;
use CherryneChou\Admin\Traits\RestfulResponse;
use CherryneChou\Admin\Transformers\AdministratorTransformer;
use CherryneChou\Admin\Transformers\MenuTransformer;
use CherryneChou\Admin\Support\Helper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use CherryneChou\Admin\Events\Login;
use Mews\Captcha\Facades\Captcha;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    use RestfulResponse;

    /**
     * @var AuthorizationService
     */
    protected $authorizationService;

    /**
     * @param AuthorizationService $authorizationService
     */
    public function __construct(AuthorizationService $authorizationService)
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
            Event::dispatch(new Login());

            return $this->success([
                'access_token' => $access_token,
            ]);

        }catch (ModelNotFoundException $exception){
            return $this->failed(trans('admin.user_not_exists'));
        }catch (\Exception $exception){
            return $this->failed($exception->getMessage());
        }
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
     * 获取验证码
     */
    public function getCaptcha()
    {
        $captchaData = Captcha::create('default', true); // 第二个参数 true 表示以数组形式返回

        return $this->success([
            'key' => $captchaData['key'],
            'img' => $captchaData['img'], // base64 图片
        ]);
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