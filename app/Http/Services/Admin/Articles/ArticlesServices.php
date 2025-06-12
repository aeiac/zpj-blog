<?php

namespace App\Http\Services\Admin\Articles;

use App\Models\Articles\Articlesr;
use App\Http\Services\Admin\BaseAdminServices;

class ArticlesServices extends BaseAdminServices
{
    /**
     * 获取文章列表（支持多条件筛选和分页）
     *
     * @param array $input 查询参数，支持字段：
     *                     - title       (string) 标题模糊搜索
     *                     - content     (string) 内容模糊搜索
     *                     - type_id     (int)    文章类型ID
     *                     - author_id   (int)    作者ID
     *                     - status      (int)    状态
     *                     - start_time  (string) 创建时间起始（格式如：YYYY-MM-DD）
     *                     - end_time    (string) 创建时间结束（格式如：YYYY-MM-DD）
     *                     - per_page    (int)    每页条数，默认10
     *
     * @return array 经过分页处理的文章列表数据
     */
    public function getSelectArticlesTable(array $input): array
    {
        $data = [];
        $where = [];

        // 标题模糊搜索
        if (!empty($input['title'])) {
            $where[] = ['title', 'like', '%' . $input['title'] . '%'];
        }

        // 内容模糊搜索
        if (!empty($input['content'])) {
            $where[] = ['content', 'like', '%' . $input['content'] . '%'];
        }

        // 文章类型过滤
        if (!empty($input['type_id'])) {
            $where[] = ['type_id', '=', $input['type_id']];
        }

        // 作者ID过滤
        if (!empty($input['author_id'])) {
            $where[] = ['author_id', '=', $input['author_id']];
        }

        // 状态过滤
        if (isset($input['status']) && $input['status'] !== '') {
            $where[] = ['status', '=', $input['status']];
        }

        // 创建时间起始
        if (!empty($input['start_time'])) {
            $where[] = ['created_at', '>=', $input['start_time']];
        }

        // 创建时间结束
        if (!empty($input['end_time'])) {
            $where[] = ['created_at', '<=', $input['end_time']];
        }

        // 查询并分页
        $query = Articlesr::where($where)
            ->orderByDesc('created_at');

        $perPage = isset($input['per_page']) && is_numeric($input['per_page']) ? (int)$input['per_page'] : 10;

        $paginatedData = $query->paginate($perPage)->toArray();

        $data['articles_info'] = $this->paginateToArray($paginatedData);

        return $this->appResponse::successToArray($data);
    }
}
