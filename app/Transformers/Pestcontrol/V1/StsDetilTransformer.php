<?php
namespace APP\Transformers\Pestcontrol\V1;

use League\Fractal\TransformerAbstract;

class StsDetilTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
        "id"                        =>$s->id,
        "sts_id"                    =>$s->sts_id,
        "area"                      =>$s->area,
        "treatmen"                  =>$s->treatmen,
        "remark"                    =>$s->remark,  
        "bahan_aktif"               =>$s->bahan_aktif,
        "dosis_satuan"              =>$s->dosis_satuan,
        "dosis"                     =>$s->dosis,
        "dosis_satuan"              =>$s->dosis_satuan,
        "jumlah_pemakaian"          =>$s->jumlah_pemakaian,
        "keterangan"                =>$s->keterangan,
        "tanggal_mulai"             =>$s->tanggal_mulai,
        "tanggal_selesai"           =>$s->tanggal_selesai,
        "rekomendasi"               =>$s->rekomendasi,
        "petugas"                   =>$s->petugas,
        "feedback_klien"            =>$s->feedback_klien,
        "klien"                     =>$s->klien,
        ];
    
    }
}