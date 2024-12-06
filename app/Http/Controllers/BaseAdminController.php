<?php

namespace App\Http\Controllers;

use App\Const\Admin\CommonConst;
use App\Http\Services\Admin\Login\LoginAdminUsersServices;
use App\Http\Services\Admin\Systems\SystemAdminPermissionAuthServices;
use App\Models\AdminUsers;
use App\Models\AdminUsersLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use RuntimeException;

class BaseAdminController extends Controller
{
    /**
     * 存储管理员基本信息
     * @var
     */
    protected $adminUserInfo;

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

    public function __construct()
    {
        $this->accessToken = $this->getBearerToken();
    }

    public function callAction($method, $parameters)
    {
        $response = $this->initial($method);
        if ($response instanceof JsonResponse) {
            return $response;
        }
        $this->addAdminUsersLog();
        return parent::callAction($method, $parameters);
    }

    /**
     * 鉴权初始化
     * @param $method
     * @return array|bool|JsonResponse|void|null
     */
    private function initial($method)
    {
        if (!empty(array_intersect(['*', $method], static::$excludedAuth))) {
            return null;
        }
        if (!$this->accessToken || !LoginAdminUsersServices::validateToken($this->accessToken)) {
            return $this->errorJson(400, '请求未授权');
        }
        $adminUserId = LoginAdminUsersServices::getToken($this->accessToken);
        if ($adminUserId) {
            $this->adminUserInfo = AdminUsers::find($adminUserId);
        }
//        if ($this->systemAdminAuthPermission()) {
//            return $this->errorJson(400, '无此权限！');
//        }
    }

    /**
     * 输出成功结果
     * @param array|mixed $data
     * @param string $message
     * @param int $code
     * @return array
     */
    public function success(mixed $data = [], string $message = 'success', int $code = 200): array
    {
        if ($code < 200 || $code >= 300) {
            throw new RuntimeException('success code should between 200 and 300, ' . $code . ' given');
        }
        return [
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];
    }

    /**
     * 输出失败结果
     * @param int $code
     * @param string $message
     * @return array
     */
    public function error(int $code, string $message): array
    {
        return [
            'code' => $code,
            'message' => $message
        ];
    }

    public function errorJson(int $code, string $message)
    {
        return response()->json([
            'code' => $code,
            'message' => $message
        ]);
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
        if (Request::isMethod('post')) {
            AdminUsersLog::create([
                'admin_users_id' => $this->adminUserInfo->id ?? 0,
                'path' => Request::path(),
                'request' => json_encode(Request::all(), JSON_UNESCAPED_UNICODE),
                'ip' => Request::ip(),
                'ua' => Request::header('User-Agent', ''),
                'created_at' => date('Y-m-d H:i:s', time())
            ]);
        }
    }

    /**
     * 过滤分页参数
     * @param array $params
     * @return array
     */
    public function paginateToArray(array $params): array
    {
        return [
            'data' => $params['data'],                // 当前页数据
            'total' => $params['total'],              // 总记录数
            'per_page' => $params['per_page'],        // 每页记录数
            'current_page' => $params['current_page'],// 当前页码
            'last_page' => $params['last_page']       // 最后一页
        ];
    }

    /**
     * 动态验证权限
     * @return bool
     */
    public function systemAdminAuthPermission(): bool
    {
        return SystemAdminPermissionAuthServices::getSelectRoleAndPermissionInner($this->adminUserInfo, Request::path());
    }
}
