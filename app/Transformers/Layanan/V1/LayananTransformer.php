<?php
namespace App\Transformers\Layanan\V1;

use League\Fractal\TransformerAbstract;

class LayananTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
            "id"                    => $s->id,
            "nama"                  => $s->nama,
            "deskripsi"             => $s->deskripsi,
            "id_jenis_layanan"      => $s->id_jenis_layanan,
            "jenis_layanan"         => $s->jenis_layanan,
            "gambar"                => $s->gambar,
        ];
    }
}
