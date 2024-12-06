<?php

namespace App\Models\Articles;

use App\Models\BaseModel;

class ArticlesType extends BaseModel
{
    protected $table = 'articles_type';
    protected $primaryKey = 'id';
    public $timestamps = true;

}
