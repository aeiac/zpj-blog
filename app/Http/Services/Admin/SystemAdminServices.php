<?php

namespace App\Http\Services\Admin;

use App\Models\SystemBlackList;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class SystemAdminServices
{

    /**
     * 获取IP封禁列表
     * @param array $input
     * @return array
     */
    public function getSelectBlackList(array $input): array
    {
        $where = [];
        if (isset($input['ip']) && $input['ip'] !== '') {
            $where[] = ['ip_address', 'like', '%' . $input['ip'] . '%'];
        }
        if (isset($input['reason']) && $input['reason'] !== '') {
            $where[] = ['reason', 'like', '%' . $input['reason'] . '%'];
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
        return SystemBlackList::where($where)
            ->orderBY('created_at', 'desc')
            ->paginate((int)$input['per_page'] ?: 10, ['*'])
            ->toArray();
    }

    /**
     * 操作IP封禁列表（新增、修改）
     * @param array $input
     * @return array
     */
    public function savaBlackList(array $input): array
    {
        $id = $input['id'] ?? null;
        if (!empty($id)) {
            $IpExists = SystemBlackList::where(['ip', '=', $input['ip']])->exists();
            return $IpExists ?? ['message' => '当前IP已存在！'];
        }
        $input = Arr::only($input, [
            'ip', 'reason', 'status'
        ]);
        $component = SystemBlackList::updateOrCreate(['id' => $id], $input);
        return ['message' => '记录成功！当前ID为：' . $component->id];
    }


}
