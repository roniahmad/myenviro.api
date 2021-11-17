<?php

namespace App\Models\Cleaning\V1;

use Illuminate\Database\Eloquent\Model;

class JosComplaintFeedback extends Model
{
    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = "jos_komplain_feedback";

    protected $connection = 'db_cleaning';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'jos_komplain_id',
        'feedback',
        'rating',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
    ];
}
