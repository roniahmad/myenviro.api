<?php

namespace App\Models\Pestcontrol\V1;

use Illuminate\Database\Eloquent\Model;

class SppStsDetil extends Model
{
    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = "spp_sts_detil";

    protected $connection = 'db_pestcontrol';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sts_id' ,
        'spp_area_id' ,
        'tipe_treatmen' ,
        'remark' ,
        'bahan_aktif' ,
        'dosis' ,
        'dosis_satuan' ,
        'jumlah_pemakaian',
        'keterangan' ,
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
    ];
}
