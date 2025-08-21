<?php
/**
 * @file   - UtilsController
 * @refer  - 作用于编写后台系统功能模块。
 * -
 * @author - Mr.raycake
 * @date   - 2024-12-07 17:51:29
 */

namespace App\Http\Controllers\Admin;

use App\Http\Services\Admin\Utils\UtilsServices;
use App\Models\Utils\Files;
use App\Utils\File;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class UtilsController extends BaseController
{

    /**
     * 生成权限
     *
     * @param Request $request
     * @param UtilsServices $services
     *
     * @return JsonResponse
     */
    public function permission(Request $request, UtilsServices $services): JsonResponse
    {
        $params = $request->all();
        $result = $services->generatePermission($params, $this->adminUserInfo);
        return $this->appResponse::success($result);
    }

    // 文件上传-FilesTo1
    public function fileUpload(Request $request): array
    {
        $remark = $request->get('remark');
        $expireAt = $request->get('expire_date');
        $file = $request->file('file');
        $storageType = $request->get('storage_type', Files::$storageType[0]);
        // 验证文件
        $fileValidate = File::validateUploadFile(file: $file);
        if (!empty($fileValidate)) {
            return $this->appResponse::errorToArray(msg: $fileValidate);
        }
        // 上传文件
        $fileResult = (new File())->uploadFile(name: '博客', file: $file, storageType: $storageType, remark: $remark, expireAt: $expireAt);
        if (!empty($fileResult)) {
            return $this->appResponse::errorToArray(msg: $fileResult);
        }
        return $this->appResponse::successToArray();
    }

}
