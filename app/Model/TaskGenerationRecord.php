<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TaskGenerationRecord extends Model
{
    protected $table = "task_generation_record";

    protected $primaryKey = "record_id";

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('whereCreateUser',function (Builder $builder){
            $builder->where('create_user', session("userId"));
        });
    }
}
