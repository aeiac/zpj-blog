<?php

namespace App\Http\Controllers\Admin;

use App\Http\Services\Admin\Articles\ArticlesServices;
use Illuminate\Http\Request;

class ArticlesController extends BaseController
{
    public function list(Request $request, ArticlesServices $services): array
    {
        $input = $request->all();
        $paginateResult = $services->getSelectArticlesTable($input);
        return $this->success($this->paginateToArray($paginateResult));
    }
}
