<?php
namespace APP\Transformers\Inventory\V1;

use League\Fractal\TransformerAbstract;

class DosisTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
            "id"            =>$s->id,
            "nama"          =>$s->nama,
            "deskripsi"     =>$s->deskripsi,
            "status"        =>$s->status
        ];
    }
}