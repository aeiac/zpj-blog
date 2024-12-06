<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Http\Services\Admin\Systems\SystemAdminServices;
use Illuminate\Http\Request;

class SystemBaseAdminServicesController extends BaseAdminController
{
    protected static array $excludedAuth = [];

    /**
     * 封禁IP列表
     * @param Request $request
     * @param SystemAdminServices $services
     * @return array
     */
    public function blackList(Request $request, SystemAdminServices $services): array
    {
        $input = $request->all();
        $paginateResult = $services->getSelectBlackList($input);
        return $this->success($this->paginateToArray($paginateResult));
    }

    /**
     * 封禁IP操作
     * @param Request $request
     * @param SystemAdminServices $services
     * @return array
     */
    public function blackListSave(Request $request, SystemAdminServices $services): array
    {
        $input = $request->all();
        $savaResult = $services->savaBlackList($input);
        return $this->success($savaResult);
    }


}
