<?php
namespace App\Models\Pestcontrol\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

Class JosStsDetil extends model
{
    // use SoftDeletes;

    //     /**
	// * The table associated with the model.
	// * if your table name is different then your class name,
	// * define here
	// * @var string
	// */

    protected $table = 'jos_sts_detil';
    protected $connection = 'db_pestcontrol';

	protected $fillabe = [
		'sts_id',
		'jos_area_id',
		'tipe_treatmen',
		'remark',
		'bahan_aktif',
		'dosis',
		'dosis_satuan',
		'jumlah_pemakaian',
		'keterangan',

	];

	protected $hidden = [
		"created_at",
		"updated_at",
	];

}