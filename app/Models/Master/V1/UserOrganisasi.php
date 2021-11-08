<?php
namespace App\Models\Master\V1;

use Illuminate\Database\Eloquent\Model;

class UserOrganisasi extends Model
{
    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = "user_organisasi";

    protected $connection = 'db_master';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'jenis_layanan',
        'nama',
        'deskripsi',
        'gambar',
        'narahubung',
        'telp',
        'email',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
    ];
}
