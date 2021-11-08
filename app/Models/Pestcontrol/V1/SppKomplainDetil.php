<?php

namespace App\Models\Pestcontrol\V1;

use Illuminate\Database\Eloquent\Model;

class SppKomplainDetil extends Model
{
    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = "spp_komplain_detil";

    protected $connection = 'db_pestcontrol';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'spp_komplain_id',
        'komplain',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
    ];
}
