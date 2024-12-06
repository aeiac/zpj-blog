<?php

namespace App\Http\Services\Admin\Systems;

use App\Models\Systems\SystemBlackList;
use App\Const\Admin\RedisKeyConst;
use Illuminate\Support\Arr;

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
     * 新增或修改IP封禁记录
     *
     * @param array $input 需要操作的数据，包括：
     * - id: 封禁记录的ID
     * - ip_address: IP地址（选填）
     * - reason: 封禁原因（选填）
     * - status: 封禁状态（选填，1启用，0禁用）
     *
     * @return array 操作结果，包括：
     * - message: 操作成功后的提示信息，包含当前操作的ID
     * - code: 错误码（若有错误）
     *
     */
    public function savaBlackList(array $input): array
    {
        $id = $input['id'] ?? null;
        if (!empty($input['ip_address'])) {
            $IpExists = SystemBlackList::where([
                'ip_address' => $input['ip_address'],
                'status' => SystemBlackList::STATUS_ACTIVE
            ])->exists();
            if ($IpExists) {
                return ['code'=>401,'message' => '当前IP已存在！'];
            }
        }
        $input = Arr::only($input, ['ip_address', 'reason', 'status']);
        $component = SystemBlackList::updateOrCreate(['id' => $id], $input);
        if ($component->id && $input['status'] == SystemBlackList::STATUS_INACTIVE) {
            RedisKeyConst::del(sprintf(RedisKeyConst::ACCESS_BLACK_LIST_KEY, $component->ip_address));
        }
        return ['message' => '操作成功！当前ID为：' . $component->id];
    }


}
