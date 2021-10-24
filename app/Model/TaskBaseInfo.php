<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TaskBaseInfo extends Model
{
    protected $table = "task_base_info";

    protected $primaryKey = "task_id";

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('whereCreateUser',function (Builder $builder){
            $builder->where('create_user', session("userId"));
        });
    }
}
