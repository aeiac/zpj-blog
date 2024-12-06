<?php

namespace App\Models\Articles;

use App\Models\BaseModel;

class Articlesr extends BaseModel
{
    protected $table = 'articles';
    protected $primaryKey = 'id';
    public $timestamps = true;

}
