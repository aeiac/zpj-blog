<?php

namespace App\Models\Systems;

use App\Models\BaseModel;

class PageLayout extends BaseModel
{
    protected $table = 'blog_page_layouts';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'status',
        'icon_url',
        'is_deleted',
        'page_name',
        'area',
        'position',
        'function_desc',
        'components',
        'size',
        'interaction',
        'remarks',
        'deleted_at',
        'created_by',
        'updated_by',
    ];


    public static function addPageLayout(array $data) :object
    {
        return self::create($data);
    }
}
