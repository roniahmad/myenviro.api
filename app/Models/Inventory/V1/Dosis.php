<?php
namespace App\Models\Inventory\V1;

use Illuminate\Database\Eloquent\Model;

class Dosis extends Model
{
     /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = 'dosis';
    protected $connection = 'db_inventory';

        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama',
        'deskripsi',
        'status',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
    ];
}