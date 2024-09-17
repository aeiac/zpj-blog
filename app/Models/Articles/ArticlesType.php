<?php

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Model;

class ArticlesType extends  Model
{
    protected $table = 'articles_type';
    protected $primaryKey = 'id';
    public $timestamps = true;

}
