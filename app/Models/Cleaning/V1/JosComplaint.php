<?php

namespace App\Models\Cleaning\V1;

use Illuminate\Database\Eloquent\Model;

class JosComplaint extends Model
{
    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = "jos_komplain";

    protected $connection = 'db_cleaning';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pelayanan_id',
        'nomor',
        'klien_id',
        'tanggal',
        'status',
        'pic_klien',
        'pic_perusahaan',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        // "deleted_at",
    ];
}
