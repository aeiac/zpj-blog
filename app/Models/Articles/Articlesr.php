<?php

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Model;

class Articlesr extends  Model
{
    protected $table = 'articles';
    protected $primaryKey = 'id';
    public $timestamps = true;

}
