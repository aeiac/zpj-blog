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
            return CodeConst::getCodeMsg(CodeConst::DATA_DUPLICATE);
        }
        $addResult = (new links())->addLinks($params['name'], $params['url'], $params['type']);
        if (empty($addResult)) {
            return CodeConst::getCodeMsg(CodeConst::DATA_SAVE_FAILED);
        }
        return $result;
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
     * @return array 返回接口结果数组
     */
    public static function queryList(array $params): array
    {
        $data = [];
        $query = Links::query();
        if (!empty($params['name'])) {
            $query->where('name', 'like', '%' . $params['name'] . '%');
        }

        if (isset($params['status'])) {
            $query->where('status', $params['status']);
        }

        if (isset($params['type'])) {
            $query->where('type', $params['type']);
        }

        if (!empty($params['url'])) {
            $query->where('url', 'like', '%' . $params['url'] . '%');
        }

        if (isset($params['is_deleted'])) {
            $query->where('is_deleted', $params['is_deleted']);
        }

        if (isset($params['created_by'])) {
            $query->where('created_by', $params['created_by']);
        }

        // 分页参数
        $perPage = isset($input['per_page']) && is_numeric($input['per_page']) ? (int)$input['per_page'] : 10;

        // 查询并分页
        $query = $query->orderByDesc('created_at')->paginate($perPage)->toArray();
        $data['files_info'] = BaseAdminServices::paginateToArray($query);
        return $data;
    }


}
