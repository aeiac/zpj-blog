<?php

namespace App\Http\Services\Admin\Systems;

use App\Const\Admin\CodeConst;
use App\Const\Admin\RedisKeyConst;
use App\Http\Services\Admin\BaseAdminServices;
use App\Models\Systems\PageLayout;
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
                return $this->eMsg::getCodeMsg(code: $this->eMsg::DATA_DUPLICATE);
            }
        }
        $input = Arr::only($input, ['ip_address', 'reason', 'status']);
        $component = SystemBlackList::updateOrCreate(['id' => $id], $input);
        if ($component->id && $input['status'] == SystemBlackList::STATUS_INACTIVE) {
            RedisCache::del(sprintf(RedisKeyConst::ACCESS_BLACK_LIST_KEY, $component->ip_address));
        }
        return $data;
    }

    /**
     * 获取分页列表
     *
     * @param array $params
     *  - name        string  模糊搜索业务名称
     *  - status      int     状态过滤
     *  - page_name   string  页面名称
     *  - area        string  区域
     *  - is_deleted  int     逻辑删除状态（0=未删除，1=已删除）
     *  - per_page    int     每页数量，默认 15
     *
     * @return array
     */
    public static function getPageLayoutList(array $params): array
    {
        $data = [];
        $query = PageLayout::query();

        if (!empty($params['name'])) {
            $query->where('name', 'like', '%' . $params['name'] . '%');
        }

        if (isset($params['status'])) {
            $query->where('status', $params['status']);
        }

        if (!empty($params['page_name'])) {
            $query->where('page_name', $params['page_name']);
        }

        if (!empty($params['area'])) {
            $query->where('area', $params['area']);
        }

        if (isset($params['is_deleted'])) {
            $query->where('is_deleted', $params['is_deleted']);
        } else {
            // 默认只查未删除的
            $query->where('is_deleted', 0);
        }
        // 分页参数
        $perPage = isset($params['per_page']) && is_numeric($params['per_page']) ? (int)$params['per_page'] : 10;

        // 查询并分页
        $query = $query->orderByDesc('created_at')->paginate($perPage)->toArray();
        $data['info'] = BaseAdminServices::paginateToArray($query);
        return $data;
    }

    // 新增一条页面配置记录
    public static function addPageLayout(array $params): string
    {
        $data = '';
        $addResult = PageLayout::addPageLayout($params);
        if (empty($addResult)) {
            return CodeConst::getCodeMsg(CodeConst::DATA_SAVE_FAILED);
        }
        return $data;
    }



}
