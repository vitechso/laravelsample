<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\User\User;
use DB;

class Products extends Model
{
	

    public $sortable = ['title', 'id', 'created_at', 'updated_at'];
    /**
	* The table associated with the model.
	*
	* @var string
	*/
    protected $table = 'products';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = ['id', 'title', 'description', 'price', 'created_by'];

    /**
     * Relation with created_by
     *
     * @return BelongsToMany
     */
    static function created_artist()
    {
    	
    	
    	return DB::table('products')->select(['products.*','users.name as artistname'])->where(['products.status'=>1])->join('users', 'users.id', '=', 'products.created_by')->orderBy('products.id', 'DESC')->get();
        // return $this->belongsToMany(User::class, 'users', 'user_id', 'created_by');
    }

    static function created_artistjoin($id='')
    {
    	
    	$where1 = ['products.id'=>$id,'products.status'=>1];
    	return DB::table('products')->select(['products.*','users.name as artistname'])->where($where1)->join('users', 'users.id', '=', 'products.created_by')->orderBy('products.id', 'DESC')->first();
        // return $this->belongsToMany(User::class, 'users', 'user_id', 'created_by');
    }

    static function test(){
    echo "This is a test function";
   }
}
