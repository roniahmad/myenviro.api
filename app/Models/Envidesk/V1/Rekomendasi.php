<?php
namespace App\Models\Envidesk\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rekomendasi extends Model
{
    use SoftDeletes;

    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = "rekomendasi";

    protected $connection = 'db_envidesk';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nomor_rekomendasi',
        'tahun',
        'jos_id',
        'klien_id',
        'perusahaan_id',
        'pic_perusahaan',
        'rekomendasi',
        'tanggal_rekomendasi',
        'gambar_rekomendasi',
        'rekomendasi_dibaca',
        'feedback',
        'closed',
        'tanggal_closed',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
    ];

}
