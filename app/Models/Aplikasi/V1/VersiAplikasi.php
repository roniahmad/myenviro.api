<?php
namespace App\Models\Aplikasi\V1;

use Illuminate\Database\Eloquent\Model;

class VersiAplikasi extends Model
{
    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = "versi_app";

    protected $connection = 'db_aplikasi';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama_app',
        'versi_sekarang',
        'versi_baru',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
    ];
}
