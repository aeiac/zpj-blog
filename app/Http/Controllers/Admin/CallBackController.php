<?php

namespace App\Http\Controllers\Admin;

use App\Http\Services\Admin\Systems\CallBackServices;
use App\Utils\Api\TianApi;
use Illuminate\Http\Request;

class CallBackController extends BaseController
{
    protected static array $excludedAuth = ['test'];

    // test系统回调
    public function test(Request $request, CallBackServices $services): array
    {

        $e = (new TianApi())->sfzQuery();
        dump($e);die;
        $params = $request->all();
        if (empty($params)) {
            return $this->appResponse::errorToArray();
        }
        $result = $services::test($params);
        if (!empty($result)) {
            return $this->appResponse::errorToArray(msg: $result);
        }
        return $this->appResponse::successToArray();

    }
}
