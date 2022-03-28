<?php
namespace APP\Transformers\Pestcontrol\V1;

use League\Fractal\TransformerAbstract;

class TanggalMonitoringTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
            "id"                    => $s->id,
            "jos_installation_id"   => $s->jos_installation_id,
            // "tanggal_monitoring"    => $s->tanggal_monitoring,
            "no_unit"               => $s->no_unit,
            "area"                  => $s->area,
            "date_monitoring"       => $s->date_monitoring,
            "nomor_registrasi"      => $s->nomor_registrasi,
        ];
    }
}