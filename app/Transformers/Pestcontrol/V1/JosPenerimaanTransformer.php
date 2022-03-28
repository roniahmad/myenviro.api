<?php
namespace APP\Transformers\Pestcontrol\V1;

use League\Fractal\TransformerAbstract;

class JosPenerimaanTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
            "id"                 =>$s->id,
            "nomor_registrasi"   =>$s->nomor_registrasi,
            "barang"             =>$s->barang,
            "masa_depresiasi"    =>$s->masa_depresiasi,
            "merk"               =>$s->merk,
            "status"             =>$s->status,
        ];
    }
}