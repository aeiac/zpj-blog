<?php

namespace App\Http\Controllers\Admin;

use App\Http\Services\Admin\Articles\ArticlesServices;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ArticlesController extends BaseController
{
    public function list(Request $request, ArticlesServices $services): JsonResponse
    {
        $data = [];
        $input = $request->all();
        // 数据校验


        // 返回结果
        $paginateResult = $services->getSelectArticlesTable($input);


        return $this->appResponse::success($this->paginateToArray($paginateResult));
    }
}
