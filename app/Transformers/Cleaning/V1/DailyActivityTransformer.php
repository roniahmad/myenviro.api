<?php
namespace App\Transformers\Cleaning\V1;

use League\Fractal\TransformerAbstract;

class DailyActivityTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
            "id"                    => $s->id,
            "mulai"                 => $s->mulai,
            "selesai"               => $s->selesai,
            "pekerjaan"             => $s->pekerjaan,
        ];
    }
}
