<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Pacttemplate extends Model
{
	protected $primaryKey = 'id';
	public $sortable = ['id','title','type', 'created_at', 'updated_at'];
    
    protected $fillable = ['title','type','temp_section','created_by'];


}
