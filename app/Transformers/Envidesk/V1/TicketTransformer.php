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
            "nama_klient"            => $s->nama_klient,
            "no_jos"                 => $s->no_jos,
            // "pegawai_id"             => $s->pegawai_id,
            "nama"                   => $s->nama,
            "status_komplain"        => $s->status_komplain,
            "date_komplain"          => $s->date_komplain,
            "time_komplain"          => $s->time_komplain,
            "topik"                  => $s->topik,
            "komplain"               => $s->komplain,
            "gambar_komplain"        => $s->gambar_komplain,
            "date_qc"                => $s->date_qc,
            "date_dibaca"            => $s->date_dibaca,
            "time_dibaca"            => $s->time_dibaca,
        ];
    }
}
