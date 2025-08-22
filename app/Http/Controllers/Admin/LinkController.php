<?php

namespace App\Http\Controllers\Admin;

use App\Http\Services\Admin\Link\LinkServices;
use App\Models\Link\Links;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LinkController extends BaseController
{
    /**
     * 添加链接接口
     *
     * 用于向 blog_links 表中新增一条链接记录。
     * 请求参数通过 Request 注入，包含：
     * - name   string  链接名称/显示名称（必填）
     * - url    string  链接地址，必须为合法 URL（必填）
     * - type   int     链接类型：1=业务链接、2=友链、3=普通链接（可选，默认=3）
     *
     * @param Request $request 请求对象，包含新增链接所需的参数
     *
     * @return array 返回成功数组
     */
    public function add(Request $request): array
    {
        $params = $request->all();
        $validation = Validator::make($params, [
            'name'   => 'required|string|max:100',
            'type' => 'nullable|integer|in:' . implode(',', Links::$type),
            'url'    => 'required|url|max:255',
        ]);
        if ($validation->fails()) {
            return $this->appResponse::errorToArray(msg: $validation->errors()->first());
        }
        $result = LinkServices::addLink($params);
        if (!empty($result)) {
            return $this->appResponse::errorToArray(msg: $result);
        }
        return $this->appResponse::successToArray();
    }

    /**
     * 链接列表接口
     *
     * 用于获取 blog_links 表的链接列表，支持条件筛选和分页。
     * 可以通过请求参数过滤数据：
     * - name        string  链接名称，模糊匹配（可选）
     * - status      int     链接状态：0=草稿，1=启用，2=禁用，3=已删除（可选）
     * - type        int     链接类型：1=业务链接、2=友链、3=普通链接（可选）
     * - url         string  链接地址，模糊匹配（可选）
     * - is_deleted  int     是否删除：0=未删，1=已删（可选）
     * - created_by  int     创建人ID（可选）
     * - per_page    int     每页条数，默认10（可选）
     *
     * @param Request $request 请求对象，包含筛选和分页参数
     * @return array 返回接口结果数组
     */
    public function list(Request $request): array
    {
        $params = $request->all();
        $validation = Validator::make($params, [
            'name'         => 'nullable|string|max:100',
            'type'         => 'nullable|integer|in:' . implode(',', Links::$type),
            'url'          => 'nullable|string|max:255',
            'status'       => 'nullable|integer|in:' . implode(',', Links::$status),
            'page'         => 'nullable|integer|min:1',
            'per_page'     => 'nullable|integer|min:1|max:100'
        ]);
        if ($validation->fails()) {
            return $this->appResponse::errorToArray(msg: $validation->errors()->first());
        }
        $result = LinkServices::queryList($params);
        return $this->appResponse::successToArray($result);
    }

    // 操作
    public function operate()
    {

    }


}
