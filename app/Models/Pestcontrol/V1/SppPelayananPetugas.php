<?php

namespace App\Models\Pestcontrol\V1;

use Illuminate\Database\Eloquent\Model;

class SppPelayananPetugas extends Model
{
    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = "spp_pelayanan_petugas";

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
        'pelayanan_id',
        'pegawai_id',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
    ];

}
