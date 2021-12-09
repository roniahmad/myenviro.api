<?php
namespace App\Models\Envidesk\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;

    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = "tiket";

    protected $connection = 'db_envidesk';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nomor_tiket',
        'topik',
        'jos_id',
        'klien_id',
        'pic_klien',
        'tanggal_pelayanan',
        'komplain',
        'tanggal_komplain',
        'gambar_komplain',
        'komplain_dibaca',
        'tanggal_in_qc',
        'tanggal_out_qc',
        'qc',
        'gambar_qc',
        'tanggal_in_action',
        'tanggal_out_action',
        'action_plan',
        'gambar_action',
        'status_komplain',
        'rating',
        'feedback',
        'closed',
        'tanggal_closed',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
    ];

}
