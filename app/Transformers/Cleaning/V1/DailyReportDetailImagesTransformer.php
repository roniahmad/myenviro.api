<?php
namespace App\Transformers\Cleaning\V1;

use League\Fractal\TransformerAbstract;

class DailyReportDetailImagesTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
            "id"                    => $s->id,
            "ldd_id"                => $s->ldd_id,
            "filename"              => $s->filename,
            "tgl_capture"           => $s->tgl_capture,
            "jam_capture"           => $s->jam_capture,
        ];
    }
}
