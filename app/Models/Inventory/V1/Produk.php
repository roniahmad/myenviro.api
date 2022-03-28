<?php
namespace App\Models\Inventory\V1;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = 'produk';
    protected $connection = 'db_inventory';

        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'gudang_id',
        'kategori_produk',
        'kode_produk',
        'nama_produk',
        'satuan',
        'masa_deprisiasi',
        'satuan_deprisiasi',
        'harga_uom',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "delete_at",
    ];
}