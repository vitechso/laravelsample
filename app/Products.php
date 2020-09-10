<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
	protected $fillable = [
        'category_id','title','pro_image','description','created_date'
    ];
    public function category() 
	{
	    return $this->belongsTo(Category::class);
	}
}
