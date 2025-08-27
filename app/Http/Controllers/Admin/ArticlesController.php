<?php

namespace App\Http\Controllers\Admin;

use App\Models\Articles\Articlesr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Services\Admin\Articles\ArticlesServices;

class ArticlesController extends BaseController
{
    public function list(Request $request, ArticlesServices $services): array
    {
        $input = $request->all();
        $data = $services->getSelectArticlesTable($input);
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
     * @param Request $request
     * @param ArticlesServices $services
     * @return array
     */
    public function add(Request $request, ArticlesServices $services):array
    {
        $params = $request->all();
        $validation = Validator::make($params, [
            'title'       => 'required|string|max:200',
            'secret'      => 'nullable|string|max:16',
            'content'     => 'nullable|string',
            'type_id'     => 'nullable|string',
            'status'      => 'required|string|in:'.implode(',',Articlesr::$status),
            'published_at'=> 'nullable|date'
        ]);
        if ($validation->fails()) {
            return $this->appResponse::errorToArray(msg: $validation->errors()->first());
        }
        $result=$services->addArticle($params);
        if(!empty($result)){
            return $this->appResponse::errorToArray(msg: $result);
        }
        return $this->appResponse::successToArray();

    }

    // 操作
    public function opertion(Request $request)
    {
        $params = $request->all();
        $validation = Validator::make($params, [
            'title'       => 'nullable|string|max:200',
            'secret'      => 'nullable|string|max:16',
            'content'     => 'nullable|string',
            'type_id'     => 'nullable|string',
            'status'      => 'nullable|string|in:'.implode(',',Articlesr::$status),
            'published_at'=> 'nullable|date',
            'sort'        => 'nullable|integer',
            'is_deleted'  => 'nullable|integer'
        ]);
        if ($validation->fails()) {
            return $this->appResponse::errorToArray(msg: $validation->errors()->first());
        }


    }
}
