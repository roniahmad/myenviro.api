<?php
namespace App\Transformers\Pestcontrol\V1;

use League\Fractal\TransformerAbstract;

class SppHamaTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
            "id"                    => $s->id,
            "id_hama"               => $s->jenis_hama,
            "jenis_hama"            => $s->deskripsi,
        ];
    }
}
