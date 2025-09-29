<?php

namespace App\Http\Controllers\Admin;

use App\Http\Services\Admin\Systems\SystemServices;
use Illuminate\Http\Request;

class CallBackController extends BaseController
{
    protected static array $excludedAuth = ['test'];


    // test系统回调
    public function test(Request $request, SystemServices $services): array
    {
        $params = $request->all();
        if (empty($params)) {
            return $this->appResponse::errorToArray();
        }
        $result = $services::callback($params);
        if (!empty($result)) {
            return $this->appResponse::errorToArray(msg: $result);
        }
        return $this->appResponse::successToArray();

    }
}
