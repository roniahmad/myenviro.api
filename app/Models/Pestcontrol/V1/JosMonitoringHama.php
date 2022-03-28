<?php
namespace App\Models\Pestcontrol\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

Class JosMonitoringHama extends model
{
    use SoftDeletes;
    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/

    protected $table = 'jos_monitoring_hama';
    protected $connection = 'db_pestcontrol';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'jos_installation_id',
        'jenis_hama',
        'hama',
        'jumlah',
        'tanggal_monitoring',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
    ];

}