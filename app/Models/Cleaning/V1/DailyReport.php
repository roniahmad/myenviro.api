<?php
namespace App\Models\Cleaning\V1;

use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = "laporan_dac";

    protected $connection = 'db_cleaning';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'jos_id',
        'pegawai_id',
        'tanggal_lapor',
        'deskripsi',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
    ];
}
