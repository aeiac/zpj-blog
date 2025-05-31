<?php

namespace App\Http\Services\Admin;

use App\Utils\Response\AppResponse;

class BaseAdminServices
{
    /**
     * 应用级响应工具
     * @var AppResponse
     */
    public AppResponse $appResponse;

    public function __construct(AppResponse $appResponse)
    {
        $this->appResponse = $appResponse;
    }

    /**
     * 过滤分页参数
     *
     * @param array $params
     *
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
}
