<?php

namespace App\Models\Cleaning\V1;

use Illuminate\Database\Eloquent\Model;

class Spp extends Model
{
    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = "spp";

    protected $connection = 'db_cleaning';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'perusahaan_id',
        'klien_id',
        'no_spp',
        'produk_id',
        'frekuensi_pekerjaan',
        'tanggal_spp',
        'pegawai_perusahaan',
        'pegawai_klien',
        'tgl_awal',
        'tgl_akhir',
        'nilai_kontrak',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
    ];
}
