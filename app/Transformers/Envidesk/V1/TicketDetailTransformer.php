<?php
namespace App\Transformers\Envidesk\V1;

use League\Fractal\TransformerAbstract;

class TicketDetailTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
            "id"                     => $s->id,
            "nomor_tiket"            => $s->nomor_tiket,
            "tanggal_pelayanan"      => $s->tanggal_pelayanan,
            "nama_klient"            => $s->nama_klient,
            "no_jos"                 => $s->no_jos,
            "nama"                   => $s->nama,
            "status_komplain"        => $s->status_komplain,
            "date_komplain"          => $s->date_komplain,
            "time_komplain"          => $s->time_komplain,
            "topik"                  => $s->topik,
            "komplain"               => $s->komplain,
            "gambar_komplain"        => $s->gambar_komplain,
            "date_dibaca"            => $s->date_dibaca,
            "time_dibaca"            => $s->time_dibaca,

            "date_qc_in"            => $s->date_qc_in,
            "time_qc_in"            => $s->time_qc_in,
            "qc"                    => $s->qc,
            "date_qc_out"           => $s->date_qc_out,
            "time_qc_out"           => $s->time_qc_out,
            "gambar_qc"             => $s->gambar_qc,

            "date_action_in"            => $s->date_action_in,
            "time_action_in"            => $s->time_action_in,
            "action_plan"               => $s->action_plan,
            "date_action_out"           => $s->date_action_out,
            "time_action_out"           => $s->time_action_out,
            "gambar_action"             => $s->gambar_action,
            "rating"                    => $s->rating,
            "feedback"                  => $s->feedback,


        ];
    }
}
