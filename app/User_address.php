<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User_address extends Model
{
    public $timestamps = false;
	protected $table = 'useraddress';
	protected $guarded = [];
	protected $primaryKey = 'id';
}
