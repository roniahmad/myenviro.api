<?php

namespace App\Models\Pestcontrol\V1;

use Illuminate\Database\Eloquent\Model;

class KlienNarahubung extends Model
{
    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = "klien_narahubung";

    protected $connection = 'db_pestcontrol';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'klien_id',
        'nama',
        'telp',
        'telp_alt',
        'email',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
    ];
}
