<?php

namespace App\Http\Controllers\Admin;

use App\Http\Services\Admin\Systems\SystemServices;
use Illuminate\Http\Request;

class SystemServicesController extends BaseController
{
    protected static array $excludedAuth = [];

    /**
     * 封禁IP列表
     *
     * @param Request $request
     * @param SystemServices $services
     *
     * @return array
     */
    public function blackList(Request $request, SystemServices $services): array
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
     * @return array
     */
    public function blackListSave(Request $request, SystemServices $services): array
    {
        $input = $request->all();
        $savaResult = $services->savaBlackList($input);
        return $this->appResponse::success($savaResult);
    }


}
