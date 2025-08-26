<?php

namespace App\Http\Controllers\Admin;

use App\Const\Admin\CodeConst;
use App\Http\Services\Admin\Permission\PermissionServices;
use App\Models\AdminUsersLog;
use App\Utils\Response\AppResponse;
use App\Utils\Response\HttpResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use App\Utils\Admin\TokensUtils;

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

    protected CodeConst $eMsg;

    /**
     * 应用级响应工具
     * @var AppResponse
     */
    public AppResponse $appResponse;

    public $tokensUtils;

    public function __construct(HttpResponse $httpResponse, AppResponse $appResponse, TokensUtils $tokensUtils, CodeConst $msg)
    {
        $this->appResponse = $appResponse;
        $this->httpResponse = $httpResponse;
        $this->tokensUtils = $tokensUtils;
        $this->accessToken = $tokensUtils::getBearerToken();
        $this->eMsg = $msg;
    }


    /**
     * @param $method
     * @param $parameters
     * @return array
     */
    public function callAction($method, $parameters): array
    {
        $response = $this->initial($method);

        if (!empty($response)) {
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
     * @return array|void|null
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

        // 超级管理员跳过鉴权
        if (!in_array('Administrator', $this->adminUserInfo->role->name)) {
            if ($this->systemAdminAuthPermission()) {
                return httpResponse::error(code: CodeConst::AUTH_FORBIDDEN);
            }
        }
    }

    /**
     * 添加日志
     * @return void
     */
    public function addAdminUsersLog(): void
    {
        AdminUsersLog::create([
            'admin_users_id' => $this->adminUserInfo->info->id ?? 0,
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
}
