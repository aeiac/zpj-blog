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
        $result = [];
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
        if (!empty($fileResult) && !is_numeric($fileResult)) {
            return $this->appResponse::errorToArray(msg: $fileResult);
        }
        $result ['file_id'] = $fileResult;
        return $this->appResponse::successToArray($result);
    }

    /**
     * 文件分片上传 - 发起
     *
     * 用于初始化大文件分片上传任务，生成 file_code
     *
     * @return array
     *  - file_code: string 本次上传的唯一标识
     */
    public function fileChunksStart(Request $request): array
    {
        $result = (new File())->fileChunksStart();
        return $this->appResponse::successToArray($result);
    }

    /**
     * 文件分片 - 发起上传
     */
    public function fileChunksUpload(Request $request,$file_code,$chunk_index): array
    {
        $file = $request->file('file');
        if (empty($file_code) || is_null($chunk_index) || empty($file)) {
            return $this->appResponse::errorToArray(code: $this->eMsg::PARAM_REQUIRED);
        }
        $result = File::fileChunksUpload($file_code, $file, $chunk_index);
        if (!empty($result) && !is_numeric($result)) {
            return $this->appResponse::errorToArray(msg: $result);
        }
        return $this->appResponse::successToArray();
    }

}
