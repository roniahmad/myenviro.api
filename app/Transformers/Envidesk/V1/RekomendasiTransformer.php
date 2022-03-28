<?php
namespace App\Transformers\Envidesk\V1;

use League\Fractal\TransformerAbstract;

class RekomendasiTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
            "id"                        => $s->id,
            "nomor_rekomendasi"         => $s->nomor_rekomendasi,
            "nama_klient"               => $s->nama_klient,
            "no_jos"                    => $s->no_jos,
            "nama"                      => $s->nama,
            "date_rekomendasi"          => $s->date_rekomendasi,
            "time_rekomendasi"          => $s->time_rekomendasi,
            "rekomendasi"               => $s->rekomendasi,
            "gambar_rekomendasi"        => $s->gambar_rekomendasi,
            "closed"                    => $s->closed,
            "date_closed"               => $s->date_closed,
            "time_closed"               => $s->time_closed,
            "date_dibaca"               => $s->date_dibaca,
            "time_dibaca"               => $s->time_dibaca,
        ];
    }
}
