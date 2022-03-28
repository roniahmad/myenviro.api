<?php
namespace APP\Transformers\Pestcontrol\V1;

use League\Fractal\TransformerAbstract;

class InstallationTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
            "id"                    =>$s->id,
            "area"                  =>$s->area,
            "no_jos"                =>$s->no_jos,
            "klien"                 =>$s->klien,
            "telepon"               =>$s->telepon,
            "no_unit"               =>$s->no_unit,
            "date_instalasi"        =>$s->date_instalasi,
            // "tanggal_monitoring"     =>$s->tanggal_monitoring,
            "maintenance_number"    =>$s->maintenance_number,
            "tube_change"           =>$s->tube_change,
            "glue_change"           =>$s->glue_change,
            // "date_monitoring"       =>$s->date_monitoring,
            "nomor_registrasi"      =>$s->nomor_registrasi,
        ];
    }

}