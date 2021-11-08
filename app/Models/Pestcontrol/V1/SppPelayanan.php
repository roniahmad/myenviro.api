<?php 
 
namespace App\Models\Pestcontrol\V1;

use Illuminate\Database\Eloquent\Model;

class SppPelayanan extends Model
{
    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = "spp_pelayanan";

    protected $connection = 'db_pestcontrol';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'spp_id', 
        'klien_id', 
        'jenis_pelayanan', 
        'waktu_mulai', 
        'waktu_selesai',
    ];

}

