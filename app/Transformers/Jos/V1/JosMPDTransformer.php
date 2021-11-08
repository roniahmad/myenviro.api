<?php
namespace App\Transformers\Jos\V1;

use League\Fractal\TransformerAbstract;

class JosMPDTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
            "id"                => $s->pegawai_id,
            "nip"               => $s->nip,
            "name"              => $s->nama_pegawai,
            "jabatan"           => $s->jabatan,
            "jabatan_id"        => $s->jabatan_id,
            "status"            => $s->status,
            "avatar"            => $s->avatar,
        ];
    }
}
