<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Signees_clauses extends Model
{
	protected $primaryKey = 'id';
    // protected $table = 'pact_temp_section';
   protected $fillable = ['signees_id','assign_tbid','sig_name'];
}
