<?php
namespace App\Models\Cleaning\V1;

use Illuminate\Database\Eloquent\Model;

class DailyReportDetil extends Model
{
    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = "laporan_dac_detil";

    protected $connection = 'db_cleaning';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'laporan_dac_id',
        'jenis_pekerjaan_cleaning',
        'jos_area_id',
        'mulai',
        'selesai',
        'pekerjaan',
        'catatan',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
    ];
}
