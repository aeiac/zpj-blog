<?php

namespace App\Http\Controllers\Admin;

use App\Const\Admin\CommonConst;
use App\Http\Services\Admin\Auth\AuthAdminServices;
use App\Http\Services\Admin\Permission\PermissionServices;
use App\Models\AdminUsers;
use App\Models\AdminUsersLog;
use App\Utils\Response\AppResponse;
use App\Utils\Response\HttpResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use App\Utils\Admin\TokensUtils;
use Symfony\Component\HttpFoundation\JsonResponse;

class BaseController extends Controller
{
    /**
     * 存储管理员基本信息
     * @var object
     */
    protected object $adminUserInfo;

    /**
     * 无需校验登录方法
     * @var array
     */
    protected static array $excludedAuth = [];

    /**
     * Token键值
     * @var string
     */
    public string $accessToken = '';

    /**
     * 网络级响应工具
     * @var HttpResponse
     */
    protected HttpResponse $httpResponse;

    /**
     * 应用级响应工具
     * @var AppResponse
     */
    public AppResponse $appResponse;

    public $tokensUtils;

    public function __construct(HttpResponse $httpResponse, AppResponse $appResponse, TokensUtils $tokensUtils)
    {
        $this->appResponse = $appResponse;
        $this->httpResponse = $httpResponse;
        $this->tokensUtils = $tokensUtils;
        $this->accessToken = $this->getBearerToken();
    }


    /**
     * @param $method
     * @param $parameters
     * @return JsonResponse
     */
    public function callAction($method, $parameters): JsonResponse
    {
        $response = $this->initial($method);
        if ($response instanceof JsonResponse) {
            return $response;
        }
        if (!method_exists($this, $method)) {
            return httpResponse::error('未知请求', 404);
        }
        $this->addAdminUsersLog();
        $result = parent::callAction($method, $parameters);
        return httpResponse::success($result);
    }

    /**
     * 鉴权初始化
     *
     * @param $method
     *
     * @return JsonResponse|void|null
     */
    private function initial($method)
    {
        if (!empty(array_intersect(['*', $method], static::$excludedAuth))) {
            return null;
        }
        if (!$this->accessToken || !$this->tokensUtils::validateCache($this->accessToken, 'session')) {
            return $this->httpResponse::unauthorized();
        }
        $adminUserInfo = $this->tokensUtils::getCache($this->accessToken, 'session');
        $this->adminUserInfo = json_decode($adminUserInfo);
        // TODO 因未配置权限所以暂时关闭，超管无视所有权限
//        if ($this->systemAdminAuthPermission()) {
//            return $this->errorJson(400, '无此权限！');
//        }
    }

    /**
     * 获取Bearer中的Token
     * @return string
     */
    public function getBearerToken(): string
    {
        $authorizationHeader = Request::header('Authorization', '');
        if ($authorizationHeader && str_starts_with($authorizationHeader, 'Bearer')) {
            return trim(str_replace('Bearer', '', $authorizationHeader));
        }
        $token = Request::cookie(CommonConst::ADMIN_TOKEN_COOKIE_KEY);
        if ($token) {
            return trim($token);
        }
        return '';
    }

    /**
     * 添加日志
     * @return void
     */
    public function addAdminUsersLog(): void
    {
        AdminUsersLog::create([
            'admin_users_id' => $this->adminUserInfo->id ?? 0,
            'path' => Request::path(),
            'request' => json_encode(Request::all(), JSON_UNESCAPED_UNICODE),
            'ip' => Request::ip(),
            'ua' => Request::header('User-Agent', ''),
            'created_at' => date('Y-m-d H:i:s', time())
        ]);
    }

    /**
     * 动态验证权限
     * @return bool
     */
    public function systemAdminAuthPermission(): bool
    {
        return PermissionServices::getSelectRoleAndPermissionInner($this->adminUserInfo, Request::path());
    }

    /**
     * 验证逻辑数据结构
     * @param array $data
     * @return JsonResponse
     */
    public function validationServicesData(array $data): JsonResponse
    {
        if ($data['code'] == 200) {
            $result = $this->appResponse::success($data);
        } else {
            $result = $this->appResponse::error($data);
        }
        return $result;
    }
}
