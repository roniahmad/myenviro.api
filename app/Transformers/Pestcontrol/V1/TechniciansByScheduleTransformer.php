<?php
namespace App\Transformers\Pestcontrol\V1;

use League\Fractal\TransformerAbstract;

class TechniciansByScheduleTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
            "pelayanan_id"      => $s->pelayanan_id,
            "nip"               => $s->nip,
            "nama"              => $s->nama,
            "jenis_kelamin"     => $s->jenis_kelamin,
            "kelamin_deskripsi"         => $s->deskripsi,
            "avatar"            => $s->avatar,
        ];
    }
}
