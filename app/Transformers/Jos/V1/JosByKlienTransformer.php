<?php
namespace App\Transformers\Jos\V1;

use League\Fractal\TransformerAbstract;

class JosByKlienTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
            "client_id"             => $s->klien_id,
            "client_name"           => $s->nama,
        ];
    }
}
