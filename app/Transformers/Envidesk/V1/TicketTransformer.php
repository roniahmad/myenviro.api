<?php
namespace App\Transformers\Envidesk\V1;

use League\Fractal\TransformerAbstract;

class TicketTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
            "id"                     => $s->id,
            "nomor_tiket"            => $s->nomor_tiket,
            "tanggal_pelayanan"      => $s->tanggal_pelayanan,
            "no_jos"                 => $s->no_jos,
            "nama"                   => $s->nama,
            "status_komplain"        => $s->status_komplain,
            "date_komplain"          => $s->date_komplain,
            "time_komplain"          => $s->time_komplain,
            "topik"                  => $s->topik,
            "komplain"               => $s->komplain,
            "gambar_komplain"        => $s->gambar_komplain,
            "date_qc"          => $s->date_qc,
        ];
    }
}
