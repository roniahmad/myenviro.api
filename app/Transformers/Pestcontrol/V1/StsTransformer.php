<?php
namespace APP\Transformers\Pestcontrol\V1;

use League\Fractal\TransformerAbstract;

class StsTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
            "id"                    => $s->id,
            "pelayanan_id"          => $s->pelayanan_id,
            "no_jos"                => $s->no_jos,
            "klient"                => $s->klient,
            // "pelayanan_id"          => $s->pelayanan_id,
            // "klien_id"              => $s->klien_id,
            "slip_number"           => $s->slip_number,
            "date_start"            => $s->date_start,
            "date_end"              => $s->date_end,
            "pelayanan"             => $s->pelayanan,
            // 'pic_perusahaan'        => $s->pic_perusahaan,
            // 'pic_klien'             => $s->pic_klien,
            // 'rekomendasi'           => $s->recomendasi,
        ];
    }
}