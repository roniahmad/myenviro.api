<?php
namespace App\Transformers\Cleaning\V1;

use League\Fractal\TransformerAbstract;

class ProdukLayananTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
            "id"                    => $s->id,
            "layanan"               => $s->layanan,
            "nama"                  => $s->nama,
            "deskripsi"             => $s->deskripsi,
            "gambar"                => $s->gambar,
            "narahubung"            => $s->narahubung,
            "telp"                  => $s->telp,
            "email"                 => $s->email,

        ];
    }
}
