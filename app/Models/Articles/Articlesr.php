<?php

namespace App\Models\Articles;

use App\Models\BaseModel;
use App\Models\Utils\Files;
use App\Utils\Admin\UserInfo;
use Illuminate\Support\Str;

class Articlesr extends BaseModel
{
    protected $table = 'blog_articles';
    protected $primaryKey = 'id';

    public $timestamps = true;

    public static array $status = [
            0,  // '回收站',
            1,  // '草稿',
            2,  // '默认',
            4,  // '待发布',
            5,  // '已发布',
            6,  // '已下线',
            7,  // '禁用',
            8,  // '定时发布',
            11, // '精选',
            12, // '私密',
            13  // '推流文章',
    ];


    /**
     * 新增文章
     *
     * @param string $title        文章标题
     * @param string $content      文章内容（支持富文本/Markdown）
     * @param string $slug         文章唯一标识（Slug，用于URL友好）
     * @param string $typeId       文章分类ID或类型标识
     * @param string $publishedAt  发布时间（可定时发布，格式：Y-m-d H:i:s）
     * @param string $secret       是否私密文章（如 '0' = 否，'1' = 是）
     * @param int    $status
     *
     * @return object 新增后的文章对象
     */
    public static function addArticlesr(
        string $title,
        string $content,
        string $slug,
        string $typeId,
        string $publishedAt,
        string $secret,
        int    $status
    ): object
    {
        do {
            $code = (string)Str::uuid();
        } while (Articlesr::where('code', $code)->exists());

        $user = UserInfo::userInfo();
        $data = [
            'code'          => $code,
            'title'         => $title,
            'content'       => $content,
            'slug'          => $slug,
            'author_id'     => $user->id,
            'type_id'       => $typeId,
            'created_by'    => $user->id,
            'updated_by'    => $user->id,
        ];

        // 状态
        if (empty($status)) {
            $data['status'] = $status;
        } else {
            $data['status'] = self::$status[5];
        }

        // 发布时间
        if (!empty($publishedAt)) {
            $data['status'] = self::$status[8];
            $data['published_at'] = $publishedAt;
        }

        // 文章是否加密
        if (!empty($secret)) {
            $data['secret'] = $secret;
            $data['status'] = self::$status[12];
            $data['is_secret'] = 1;
        }

        return self::create($data);
    }


}
