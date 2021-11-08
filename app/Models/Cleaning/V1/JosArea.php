<?php

namespace App\Models\Cleaning\V1;

use Illuminate\Database\Eloquent\Model;

class JosArea extends Model
{
    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = "jos_area";

    protected $connection = 'db_cleaning';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'jos_id',
        'area_id',
        'io',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
    ];
}
