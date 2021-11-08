<?php
namespace App\Transformers\Pestcontrol\V1;

use League\Fractal\TransformerAbstract;

class ScheduleTreatmentTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
            "id"            => $s->id,
            "spp_id"        => $s->spp_id,
            "no_spp"        => $s->no_spp,
            "produk_id"     => $s->produk_id,
            "deskripsi"     => $s->deskripsi,
            "waktu_mulai"   => $s->waktu_mulai,
            "waktu_selesai" => $s->waktu_selesai,
            "perusahaan_id" => $s->perusahaan_id,
        ];
    }
}
