<?php
namespace App\Models\Cleaning\V1;

use Illuminate\Database\Eloquent\Model;

class DailyReportDetilImages extends Model
{
    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = "ldd_images";

    protected $connection = 'db_cleaning';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ldd_id',
        'filename',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
    ];
}
