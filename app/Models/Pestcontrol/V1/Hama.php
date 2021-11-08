<?php

namespace App\Models\Pestcontrol\V1;

use Illuminate\Database\Eloquent\Model;

class Hama extends Model
{
    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = "hama";

    /**
	* Indicates if the IDs are auto-incrementing.
	*
	* @var bool
	*/
    public $incrementing = false;

    protected $connection = 'db_pestcontrol';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'jenis',
        'id',
        'deskripsi',
        'status',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
    ];
}
