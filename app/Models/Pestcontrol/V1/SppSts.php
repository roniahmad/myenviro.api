<?php

namespace App\Models\Pestcontrol\V1;

use Illuminate\Database\Eloquent\Model;

class SppSts extends Model
{
    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = "spp_sts";

    protected $connection = 'db_pestcontrol';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pelayanan_id',
        'klien_id',
        'slip_number',
        'tanggal_mulai',
        'tanggal_selesai',
        'pic_perusahaan',
        'pic_klien',
        'rekomendasi',
        'feedback_klien',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
    ];
}
