<?php
namespace App\Transformers\Cleaning\V1;

use League\Fractal\TransformerAbstract;

class SppAreaTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
            "id"                    => $s->id,
            "area_id"               => $s->area_id,
            "nama"                  => $s->nama,
            "jenis_area"            => $s->deskripsi,
        ];
    }
}
