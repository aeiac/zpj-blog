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

    // 链接列表

    public function link()
    {

    }

    // 操作
    public function operate()
    {

    }


}
