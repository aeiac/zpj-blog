<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
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
}
