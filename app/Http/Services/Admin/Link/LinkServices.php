<?php

namespace App\Http\Services\Admin\Link;

use App\Const\Admin\CodeConst;
use App\Http\Services\Admin\BaseAdminServices;
use App\Models\Link\Links;

class LinkServices extends BaseAdminServices
{

    /**
     * 添加链逻辑
     *
     * 用于向 blog_links 表中新增一条链接记录。
     * 请求参数通过 Request 注入，包含：
     * - name   string  链接名称/显示名称（必填）
     * - url    string  链接地址，必须为合法 URL（必填）
     * - type   int     链接类型：1=业务链接、2=友链、3=普通链接（可选，默认=3）
     *
     *
     * @return string 返回' '
     */
    public static function addLink(array $params): string
    {
        $result = '';
        $link = Links::where('url', $params['url'])->where('is_delete', 0)->first();
        if (!empty($link)) {
            return CodeConst::getErrorCodeConstMessages(CodeConst::DATA_DUPLICATE);
        }
        $addResult = (new links())->addLinks($params['name'], $params['url'], $params['type']);
        if (empty($addResult)) {
            return CodeConst::getErrorCodeConstMessages(CodeConst::DATA_SAVE_FAILED);
        }
        return $result;
    }


}
