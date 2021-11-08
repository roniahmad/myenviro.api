<?php

namespace App\Models\Sales\V1;

use Illuminate\Database\Eloquent\Model;

class JosManPowerDetil extends Model
{
    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = "jos_man_power_detil";

    protected $connection = 'db_sales';

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
    ];
}
