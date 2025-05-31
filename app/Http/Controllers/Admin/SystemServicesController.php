<?php

namespace App\Http\Controllers\Admin;

use App\Http\Services\Admin\Systems\SystemServices;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class SystemServicesController extends BaseController
{
    protected static array $excludedAuth = [];

    /**
     * 封禁IP列表
     *
     * @param Request $request
     * @param SystemServices $services
     *
     * @return JsonResponse
     */
    public function blackList(Request $request, SystemServices $services): JsonResponse
    {
        $input = $request->all();
        $paginateResult = $services->getSelectBlackList($input);
        return $this->appResponse::success($paginateResult);
    }

    /**
     * 封禁IP操作
     *
     * @param Request $request
     * @param SystemServices $services
     *
     * @return JsonResponse
     */
    public function blackListSave(Request $request, SystemServices $services): JsonResponse
    {
        $input = $request->all();
        $savaResult = $services->savaBlackList($input);
        return $this->appResponse::success($savaResult);
    }


}
