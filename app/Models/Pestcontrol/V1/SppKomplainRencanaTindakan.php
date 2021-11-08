<?php

namespace App\Models\Pestcontrol\V1;

use Illuminate\Database\Eloquent\Model;

class SppKomplainRencanaTindakan extends Model
{
    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = "spp_komplain_rencana_tindakan";

    protected $connection = 'db_pestcontrol';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'spp_komplain_id',
        'rencana_tindakan',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
    ];
}
