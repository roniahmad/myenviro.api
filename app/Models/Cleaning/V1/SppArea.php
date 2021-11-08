<?php

namespace App\Models\Cleaning\V1;

use Illuminate\Database\Eloquent\Model;

class SppArea extends Model
{
    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = "spp_area";

    protected $connection = 'db_cleaning';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'spp_id',
        'area_id',
        'io',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
    ];
}
