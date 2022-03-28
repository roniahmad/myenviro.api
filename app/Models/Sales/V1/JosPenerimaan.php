<?php

namespace App\Models\Sales\V1;

use Illuminate\Database\Eloquent\Model;

class JosPenerimaan extends Model
{
    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = "jos_penerimaan";

    protected $connection = 'db_sales';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'jmp_detil_id',
        'nomor_registrasi',
        'barang',
        'merk',
        'masa_depresiasi',
        'status',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
    ];
}