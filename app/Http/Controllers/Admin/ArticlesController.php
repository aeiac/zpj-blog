<?php

namespace App\Http\Controllers\Admin;

use App\Models\Articles\Articlesr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Http\Services\Admin\Articles\ArticlesServices;

class ArticlesController extends BaseController
{
    public function list(Request $request, ArticlesServices $services): array
    {
        $input = $request->all();
        $data = $services->getSelectArticlesTable($input);
        return $this->appResponse::successToArray($data);
    }


    // 新增文章
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
}
