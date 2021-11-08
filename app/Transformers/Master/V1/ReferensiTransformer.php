<?php
namespace App\Transformers\Master\V1;

use League\Fractal\TransformerAbstract;

class ReferensiTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
            "id"                    => $s->id,
            "deskripsi"             => $s->deskripsi,
        ];
    }
}
