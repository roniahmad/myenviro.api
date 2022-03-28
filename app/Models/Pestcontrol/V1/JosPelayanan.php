<?php
namespace App\Models\Pestcontrol\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


Class JosPelayanan extends model 
{


    protected $table = 'jos_pelayanan';
    protected $connection = 'db_pestcontrol';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'jos_id',
        'waktu_mulai',
        'waktu_Selesai',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
    ];
}
