<?php
namespace App\Models\Master\V1;

use Illuminate\Database\Eloquent\Model;

class Referensi extends Model
{
    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = "referensi";

    protected $connection = 'db_master';

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
    ];
}
