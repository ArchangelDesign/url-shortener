<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $primaryKey = 'hash';

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = ['url', 'hash'];

}
