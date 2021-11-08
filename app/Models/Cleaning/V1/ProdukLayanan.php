<?php
namespace App\Models\Cleaning\V1;

use Illuminate\Database\Eloquent\Model;

class ProdukLayanan extends Model
{
    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = "produk_layanan";

    protected $connection = 'db_layanan';

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
