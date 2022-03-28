<?php
namespace App\Models\Pestcontrol\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

Class JosInstallation extends model
{
    use SoftDeletes;
    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/

    protected $table = 'jos_installation';
    protected $connection = 'db_pestcontrol';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'area_id',
        'no_unit',
        'tanggal_instalasi',
        'maintenance_number',
        'tube_change',
        'glue_change',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
    ];
}