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
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class UtilsController extends BaseController
{

    /**
     * 生成权限接口
     *
     * 该接口用于生成系统权限，可通过 UtilsServices 服务处理权限逻辑。
     *
     * @param Request $request HTTP 请求对象，包含权限生成所需参数
     * @param UtilsServices $services 权限服务类实例，处理权限生成逻辑
     *
     * @return JsonResponse 返回 JSON 格式的响应，包含生成结果或错误信息
     */
    public function permission(Request $request, UtilsServices $services): JsonResponse
    {
        $params = $request->all();
        $result = $services->generatePermission($params, $this->adminUserInfo);
        return $this->appResponse::success($result);
    }

    /**
     * 文件上传接口（表单上传）
     *
     * 处理客户端通过表单提交的文件上传：
     * 1. 获取请求参数：remark（备注）、expire_date（过期时间）、storage_type（存储类型，默认本地）、file（上传的文件）。
     * 2. 验证上传文件是否合法（类型、大小等）。
     * 3. 调用 File 模型保存文件。
     * 4. 返回上传结果，包括 file_id 或错误信息。
     *
     * @param Request $request HTTP 请求对象，包含上传文件和其他可选参数
     *
     * @return array 返回操作结果数组，包含成功或失败信息
     */
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
    public function fileChunksStart(): array
    {
        $result = (new File())->fileChunksStart();
        return $this->appResponse::successToArray($result);
    }

    /**
     * 文件分片上传接口
     *
     * 接收客户端上传的单个文件分片，并保存到服务器。
     *
     * @param Request $request HTTP 请求对象，包含上传的文件
     * @param string $file_code 唯一文件编码，用于标识整个文件
     * @param int $chunk_index 当前上传的分片序号（从0开始）
     *
     * @return array 返回操作结果，包括状态码、消息及分片上传状态
     */
    public function fileChunksUpload(Request $request, string $file_code, int $chunk_index): array
    {
        $file = $request->file('file');
        if (empty($file_code) || !is_numeric($chunk_index) || empty($file)) {
            return $this->appResponse::errorToArray(code: $this->eMsg::PARAM_REQUIRED);
        }
        try {
            $result = File::fileChunksUpload($file_code, $file, $chunk_index);
        } catch (Exception) {
            return $this->appResponse::errorToArray();
        }
        if (!empty($result) && !is_numeric($result)) {
            return $this->appResponse::errorToArray(msg: $result);
        }
        return $this->appResponse::successToArray();
    }

}
