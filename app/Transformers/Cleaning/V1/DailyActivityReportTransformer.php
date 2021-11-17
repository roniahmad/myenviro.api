<?php
namespace App\Transformers\Cleaning\V1;

use League\Fractal\TransformerAbstract;

class DailyActivityReportTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
            "id"                    => $s->id,
            "tanggal_lapor"         => $s->tanggal_lapor,
            "jos_id"                => $s->jos_id,
            "pegawai_id"            => $s->pegawai_id,
            "deskripsi"             => $s->deskripsi,
            "rekomendasi"           => $s->rekomendasi,
            "tanggal_rekomendasi"   => $s->tanggal_rekomendasi,
            "waktu_rekomendasi"     => $s->waktu_rekomendasi,
            "feedback_klien"        => $s->feedback_klien,
            "tanggal_feedback"      => $s->tanggal_feedback,
            "waktu_feedback"        => $s->waktu_feedback,
        ];
    }
}
