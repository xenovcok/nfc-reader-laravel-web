<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WrisbandMaster extends Model
{
    protected $table = 'tbl_wrisband_master';
    protected $fillable = ['uuid', 'name', 'alias', 'code'];
    public $timestamps = false;
}
