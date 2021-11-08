<?php
namespace App\Transformers\Cleaning\V1;

use League\Fractal\TransformerAbstract;

class DailyReportTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
            "id"                    => $s->id,
            "tanggal_lapor"         => $s->tanggal_lapor,
            "laporan_dac_id"        => $s->laporan_dac_id,
            "jp_id"                 => $s->jp_id,
            "jenis_pekerjaan"       => $s->jenis_pekerjaan,
            "joi"                   => $s->joi,
            "area"                  => $s->area,
            "jp_id"                 => $s->jp_id,
            "mulai"                 => $s->mulai,
            "selesai"               => $s->selesai,
            "pekerjaan"             => $s->pekerjaan,
            "catatan"               => $s->catatan,
        ];
    }
}
