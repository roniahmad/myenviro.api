<?php
namespace App\Transformers\Jos\V1;

use League\Fractal\TransformerAbstract;

class JosTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
            "id"                    => $s->id,
            "client_id"             => $s->klien_id,
            "client_name"           => $s->nama,
            "client_code"           => $s->kode,
            "jos_no"                => $s->no_jos,
            "currency"              => $s->currency,
            "scope_of_work"         => $s->scope_of_work,
            "billing_contact"       => $s->billing_contact,
            "start_date"            => $s->start_date,
            "end_date"              => $s->end_date,

        ];
    }
}
