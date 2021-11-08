<?php

namespace App\Models\Pestcontrol\V1;

use Illuminate\Database\Eloquent\Model;

class SppKomplain extends Model
{
    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = "spp_komplain";

    protected $connection = 'db_pestcontrol';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pelayanan_id',
        'klien_id',
        'tanggal',
        'status',
        'pic_klien',
        'pic_perusahaan',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
    ];
}
