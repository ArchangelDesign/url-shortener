<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as LaravelUser;

class User extends LaravelUser
{
    public $incrementing = false;

    protected $primaryKey = 'token';

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = ['token', 'name'];
}
