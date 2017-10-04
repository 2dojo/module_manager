<?php

namespace TwoDojo\ModuleManager\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = [
        'prefix',
        'is_enabled',
    ];

    public function scopeEnabled($query)
    {
        $query->whereIsEnabled(true);
    }
}
