<?php
namespace App\Transformers\Layanan\V1;

use League\Fractal\TransformerAbstract;

class ProdukLayananTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
            "id"                    => $s->id,
            "id_jenis_layanan"      => $s->id_jenis_layanan,
            "nama"                  => $s->nama,
            "narahubung"            => $s->narahubung,
            "telp"                  => $s->telp,
            "email"                 => $s->email,
            "deskripsi"             => $s->deskripsi,
        ];
    }
}
