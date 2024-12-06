<?php

namespace App\Http\Services\Admin;

use App\Utils\Response\AppResponse;

class BaseAdminServices
{
    /**
     * 应用级响应工具
     * @var AppResponse
     */
    public AppResponse $appResponseUtils;

    public function __construct(AppResponse $appResponse)
    {
        $this->appResponseUtils = $appResponse;
    }
}
