<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pact_temp_section extends Model
{
	protected $primaryKey = 'id';
    protected $table = 'pact_temp_section';
   protected $fillable = ['pacttemp_id','delivery_msg','section_title','complete_confirm_msg','has_campaign','time_complete','days','total_nudges','nudge_msg','delivery_time','frequency','clause_body','clause_heading'];
}
