<?php

namespace App\Models;

class Setting extends BaseModel
{
    /**
     * Disable timestamps
     *
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
    ];
}
