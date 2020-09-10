<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = ['cat_name'];

    public function Product()
    {
        return $this->hasMany(Product::class);
    }
}
