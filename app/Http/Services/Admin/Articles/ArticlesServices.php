<?php

namespace App\Http\Services\Admin\Articles;

use App\Const\Admin\CodeConst;
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


    /**
     * 新增一篇文章
     *
     * 说明：
     * 1. 会检查 slug 是否重复，如果重复则返回提示信息。
     * 2. 调用 Articlesr::addArticlesr 方法进行文章新增。
     * 3. 如果新增失败，返回失败提示；否则返回空字符串表示成功。
     *
     * @param array $params 文章参数数组，包含以下键：
     *                      - 'title'        : string, 文章标题
     *                      - 'content'      : string, 文章内容
     *                      - 'slug'         : string, 唯一标识
     *                      - 'type_id'      : string, 文章分类ID
     *                      - 'published_at' : string|null, 发布时间，可选
     *                      - 'secret'       : string|null, 密码或加密标识，可选
     *                      - 'status'       : int, 文章状态（参考 Articlesr::$status）
     *
     * @return string 返回结果：
     *                - 如果 slug 重复，返回 "slug，数据重复" 提示
     *                - 如果新增失败，返回 "数据保存失败" 提示
     *                - 如果成功，返回空字符串 ''
     */
    public function addArticle(array $params): string
    {
        $data = '';
        if(Articlesr::where('slug', $params['slug'])->exists()){
            return 'slug，'.CodeConst::getCodeMsg(CodeConst::DATA_DUPLICATE);
        };
        $addResult = Articlesr::addArticlesr(
            title: $params['title'],
            content: $params['content'],
            slug: $params['slug'],
            typeId: $params['type_id'],
            publishedAt: $params['published_at'] ?? '',
            secret: $params['secret'] ?? '',
            status: $params['status']
        );
        if (empty($addResult)) {
            return CodeConst::getCodeMsg(CodeConst::DATA_SAVE_FAILED);
        }
        return $data;
    }
}
