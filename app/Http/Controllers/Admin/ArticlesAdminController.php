<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Http\Services\Admin\Articles\ArticlesAdminServices;
use Illuminate\Http\Request;

class ArticlesAdminController extends AdminController
{
    public function list(Request $request, ArticlesAdminServices $services)
    {
        $input = $request->all();
        $paginateResult = $services->getSelectArticlesTable($input);
        return $this->success($this->paginateToArray($paginateResult));
    }
}
