<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    public function groups()
    {
        return $this->hasMany('App\Models\Group');
    }

    public function courses()
    {
        return $this->hasMany('App\Models\Course');
    }
}
