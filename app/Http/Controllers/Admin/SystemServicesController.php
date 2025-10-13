<?php

namespace App\Http\Controllers\Admin;

use App\Http\Services\Admin\Systems\SystemServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
     * @return array
     */
    public function blackList(Request $request, SystemServices $services): array
    {
        $input = $request->all();
        $paginateResult = $services->getSelectBlackList($input);
        return $this->appResponse::successToArray($paginateResult);
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
        $result = $services->savaBlackList($input);
        if (!empty($result)) {
            return $this->appResponse::errorToArray(msg: $result);
        }
        return $this->appResponse::successToArray();
    }

    /**
     * 获取分页列表
     *
     *  - name        string  模糊搜索业务名称
     *  - status      int     状态过滤
     *  - page_name   string  页面名称
     *  - area        string  区域
     *  - is_deleted  int     逻辑删除状态（0=未删除，1=已删除）
     *  - per_page    int     每页数量，默认 15
     *
     * @param Request $request
     * @param SystemServices $services
     * @return array
     */
    public function pageLayoutList(Request $request, SystemServices $services): array
    {
        $params = $request->all();
        $result = $services->getPageLayoutList($params);
        return $this->appResponse::successToArray($result);

    }

    public function addPageLayOut(Request $request, SystemServices $services): array
    {
        $params = $request->all();
        $validation = Validator::make($params, [
            'name' => 'nullable|string|max:200',
            'icon_url' => 'nullable|string|',
            'page_name' => 'nullable|string|max:200',
            'area' => 'nullable|string|max:50',
            'function_desc' => 'nullable|string',
            'components' => 'nullable|string',
            'size' => 'nullable|string|max:50',
            'interaction' => 'nullable|string|max:50',
            'remarks' => 'nullable|string|max:1000',
        ]);
        if ($validation->fails()) {
            return $this->appResponse::errorToArray(msg: $validation->errors()->first());
        }
        $result = $services->addPageLayout($params);
        if (!empty($result)) {
            return $this->appResponse::errorToArray();
        }
        return $this->appResponse::successToArray();
    }
}
