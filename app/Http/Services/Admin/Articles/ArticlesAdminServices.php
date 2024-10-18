<?php

namespace App\Http\Services\Admin\Articles;

use App\Models\Articles\Articlesr;

class ArticlesAdminServices
{
    public function getSelectArticlesTable(array $input): array
    {
        $where = [];
        if (isset($input['title']) && $input['title'] !== '') {
            $where[] = ['title', 'like', '%' . $input['title'] . '%'];
        }
        if (isset($input['content']) && $input['content'] !== '') {
            $where[] = ['content', 'like', '%' . $input['content'] . '%'];
        }
        if (isset($input['type_id']) && $input['type_id'] !== '') {
            $where[] = ['content', '=', $input['type_id']];
        }
        if (isset($input['author_id']) && $input['author_id'] !== '') {
            $where[] = ['author_id', '=', $input['author_id']];
        }
        if (isset($input['status']) && $input['status'] !== '') {
            $where[] = ['status', '=', $input['status']];
        }
        if (isset($input['start_time']) && $input['start_time'] !== '') {
            $where[] = ['created_at', '>=', $input['start_time']];
        }
        if (isset($input['end_time']) && $input['end_time'] !== '') {
            $where[] = ['created_at', '<=', $input['end_time']];
        }
        return Articlesr::where($where)
            ->orderBY('created_at', 'desc')
            ->paginate((int)$input['per_page'] ?: 10, ['*'])
            ->toArray();
    }
}
