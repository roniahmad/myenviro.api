<?php
namespace APP\Transformers\Pestcontrol\V1;

use League\Fractal\TransformerAbstract;

class PelayananTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
            "id"                => $s->id,
            "jos_id"            => $s->jos_id,
            "no_jos"            => $s->no_jos,
            "waktu_mulai"       => $s->waktu_mulai,
            "waktu_selesai"     => $s->waktu_selesai,
        ];
    }
}