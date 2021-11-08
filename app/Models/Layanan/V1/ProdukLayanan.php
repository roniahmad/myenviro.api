<?php
namespace App\Models\Layanan\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProdukLayanan extends Model
{
    use SoftDeletes;
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
        'status',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
    ];
}
