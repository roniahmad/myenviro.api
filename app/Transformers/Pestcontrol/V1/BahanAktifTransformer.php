<?php
namespace APP\Transformers\Pestcontrol\V1;

use League\Fractal\TransformerAbstract;

class BahanAktifTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
            "id"                    => $s->id,
            "nama_produk"           => $s->nama_produk,
            "jos_id"                => $s->jos_id,
        ];
    }
}