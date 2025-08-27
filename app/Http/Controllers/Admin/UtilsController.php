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
use Illuminate\Support\Facades\Validator;

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
     * @return array 返回 JSON 格式的响应，包含生成结果或错误信息
     */
    public function permission(Request $request, UtilsServices $services): array
    {
        $params = $request->all();
        $result = $services->generatePermission($params, $this->adminUserInfo);
        return $this->appResponse::successToArray($result);
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
    public function fileChunksStart(Request $request): array
    {
        $fileName = $request->get('file_name');
        $storageType = $request->get('storage_type');
        if (empty($storageType) || empty($fileName)) {
            return $this->appResponse::errorToArray(code: $this->eMsg::PARAM_REQUIRED);
        }
        $result = (new File())->fileChunksStart($fileName, $storageType);
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
            // 验证文件，限制上传大小 30 MB
            $fileValidate = File::validateUploadFile(file: $file, maxSize: 30);
            if (!empty($fileValidate)) {
                return $this->appResponse::errorToArray(msg: $fileValidate);
            }
            $result = File::fileChunksUpload($file_code, $file, $chunk_index);
        } catch (Exception) {
            return $this->appResponse::errorToArray();
        }
        if (!empty($result) && !is_numeric($result)) {
            return $this->appResponse::errorToArray(msg: $result);
        }
        return $this->appResponse::successToArray();
    }

    /**
     * 文件分片上传 - 合并接口
     *
     * 接收客户端请求，将指定文件的所有分片按顺序合并成完整文件。
     * 合并完成后，会更新文件状态为“已完成”，并返回融合后的基础信息。
     *
     * @param string $file_code   唯一文件编码，用于标识整个文件
     * @param int    $chunk_count 文件分片总数，用于校验是否所有分片已上传
     *
     * @return array 返回操作结果，包括状态码、消息，以及融合后的文件基础信息
     */
    public function fileChunksMerge(string $file_code, int $chunk_count): array
    {
        if (empty($file_code) || !is_numeric($chunk_count)) {
            return $this->appResponse::errorToArray(code: $this->eMsg::PARAM_REQUIRED);
        }
        $result = File::fileChunkMerge($file_code, $chunk_count);
        if (!empty($result)) {
            return $this->appResponse::errorToArray(msg: $result);
        }
        return $this->appResponse::successToArray();
    }

    /**
     * 文件分片上传 - 断点续传接口
     *
     * 接口说明：
     * 查询指定文件已上传的分片信息，返回客户端已上传分片的最大序号和累计大小，
     * 用于支持文件上传断点续传。
     *
     *
     * 路径参数：
     * @param string $file_code 文件唯一编码，用于标识整个文件
     *
     * @return array JSON 数组形式返回分片状态
     */
    public function fileChunksResume(string $file_code): array
    {
        if (empty($file_code) ) {
            return $this->appResponse::errorToArray(code: $this->eMsg::PARAM_REQUIRED);
        }
        $result = File::fileChunksResume($file_code);
        return $this->appResponse::successToArray($result);
    }

    /**
     * 文件列表接口
     *
     * 查询上传的文件列表，支持分页、筛选和状态过滤。
     *
     * @param Request $request HTTP 请求对象，包含查询参数：
     *      - 'file_name'   => (string|null) 文件名称模糊搜索
     *      - 'storage'     => (string|null) 存储类型，例如 local、oss
     *      - 'file_type'   => (string|null) 文件类型/扩展名，例如 pdf、doc
     *      - 'uploader_id' => (int|null) 上传人 ID
     *      - 'status'      => (int|null) 文件状态，参考 Files::$status
     *      - 'date_from'   => (string|null) 上传起始时间，格式 YYYY-MM-DD
     *      - 'date_to'     => (string|null) 上传结束时间，格式 YYYY-MM-DD
     *      - 'page'        => (int|null) 当前页码，默认 1
     *      - 'per_page'    => (int|null) 每页条数，默认 10
     *
     * @return array 返回接口响应数组，包括：
     *      - 'data'         => 文件记录列表
     *      - 'current_page' => 当前页码
     *      - 'per_page'     => 每页条数
     *      - 'total'        => 总记录数
     *      - 'last_page'    => 总页数
     */
    public function fileList(Request $request): array
    {
        $params = $request->all();
        $validation = Validator::make($params, [
            'file_name'    => 'nullable|string|max:50',
            'storage'      => 'nullable|string|max:20|in:'.implode(',',Files::$storageType),
            'file_type'    => 'nullable|string|max:20',
            'uploader_id'  => 'nullable|integer',
            'status'       => 'nullable|integer|in:' . implode(',', Files::$status),
            'date_from'    => 'nullable|date',
            'is_deleted'   => 'nullable|integer|max:1|in:1,0',
            'date_to'      => 'nullable|date',
            'page'         => 'nullable|integer|min:1',
            'per_page'     => 'nullable|integer|min:1|max:100',
        ]);
        if ($validation->fails()) {
            return $this->appResponse::errorToArray(msg: $validation->errors()->first());
        }
        $result = File::queryFileList($params);
        return $this->appResponse::successToArray($result);

    }

    /**
     * 文件操作接口 - 更新文件信息
     *
     * 接收前端传来的可修改字段，并更新对应文件记录
     *
     * @param int     $file_id 要更新的文件ID
     * @param Request $request HTTP请求对象
     *
     * @return array 返回操作结果
     */
    public function fileOperate(int $file_id,Request $request): array
    {
        $params = $request->all();
        $validation = Validator::make($params, [
            'file_name'    => 'nullable|string|max:50',
            'is_deleted'   => 'nullable|integer|max:1|in:1,0',
            'file_type'    => 'nullable|string|max:20',
            'business_tag' => 'nullable|string|max:100',
            'status'       => 'nullable|integer|in:' . implode(',', Files::$status),
            'remark'       => 'nullable|string|max:200',
            'expire_at'    => 'nullable|date'
        ]);
        if ($validation->fails()) {
            return $this->appResponse::errorToArray(msg: $validation->errors()->first());
        }
        $result = File::fileOperate($file_id,$params);
        if (!empty($result)) {
            return $this->appResponse::errorToArray(msg: $result);
        }
        return $this->appResponse::successToArray();
    }


}
