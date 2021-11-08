<?php
namespace App\Models\Cleaning\V1;

use Illuminate\Database\Eloquent\Model;

class DailyActivity extends Model
{
    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = "pl_dac";

    protected $connection = 'db_cleaning';

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
    ];
}
