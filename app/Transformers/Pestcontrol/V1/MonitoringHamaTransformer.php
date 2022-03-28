<?php
namespace APP\Transformers\Pestcontrol\V1;

use League\Fractal\TransformerAbstract;

class MonitoringHamaTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
            "id"                    =>$s->id,
            "jos_installation_id"   =>$s->jos_installation_id,
            // "no_jos"                =>$s->no_jos,
            // "klien"                 =>$s->klien,
            // "telepon"               =>$s->telepon,
            // "no_unit"               =>$s->no_unit,
            // "nomer_pemeliharaan"    =>$s->nomer_pemeliharaan,
            // "area"                  =>$s->area,
            "jenis_hama"            =>$s->jenis_hama,
            "hama"                  =>$s->hama,
            "jumlah"                =>$s->jumlah,
            // "date_installasi"       =>$s->date_installasi,
            // "date_monitoring"       =>$s->date_monitoring,
            "tanggal_monitoring"    =>$s->tanggal_monitoring,
        ];
    }
}