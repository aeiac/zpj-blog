<?php

namespace App\Http\Services\Admin\Systems;

use App\Const\Admin\RedisKeyConst;
use App\Http\Services\Admin\BaseAdminServices;
use App\Models\Systems\SystemBlackList;
use App\Utils\Redis\RedisCache;
use Illuminate\Support\Arr;

class SystemServices extends BaseAdminServices
{

    /**
     * 获取IP封禁列表
     * @param array $input
     * @return array
     */
    public function getSelectBlackList(array $input): array
    {
        $data = [];
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
        $data['info'] = $this->paginateToArray(
            SystemBlackList::where($where)
                ->orderBY('created_at', 'desc')
                ->paginate((int)$input['per_page'] ?: 10)
                ->toArray()
        );
        return $data;
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
     * @return string 操作结果
     *
     */
    public function savaBlackList(array $input): string
    {
        $data = '';
        $id = $input['id'] ?? null;
        if (!empty($input['ip_address'])) {
            $IpExists = SystemBlackList::where([
                'ip_address' => $input['ip_address'],
                'status' => SystemBlackList::STATUS_ACTIVE
            ])->exists();
            if ($IpExists) {
                return $this->eMsg::getErrorCodeConstMessages(code: $this->eMsg::DATA_DUPLICATE);
            }
        }
        $input = Arr::only($input, ['ip_address', 'reason', 'status']);
        $component = SystemBlackList::updateOrCreate(['id' => $id], $input);
        if ($component->id && $input['status'] == SystemBlackList::STATUS_INACTIVE) {
            RedisCache::del(sprintf(RedisKeyConst::ACCESS_BLACK_LIST_KEY, $component->ip_address));
        }
        return $data;
    }
}
