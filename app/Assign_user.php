<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Assign_user extends Model
{
	protected $primaryKey = 'id';
    
    protected $fillable = ['user_id','admin_id','pact_id'];


}
