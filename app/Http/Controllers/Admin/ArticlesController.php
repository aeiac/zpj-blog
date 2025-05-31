<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Http\Services\Admin\Articles\ArticlesServices;

class ArticlesController extends BaseController
{
    public function list(Request $request, ArticlesServices $services): JsonResponse
    {
        $input = $request->all();
        $data = $services->getSelectArticlesTable($input);
        return $this->validationServicesData($data);
    }
}
