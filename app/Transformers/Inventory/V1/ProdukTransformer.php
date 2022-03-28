<?php
namespace APP\Transformers\Inventory\V1;

use League\Fractal\TransformerAbstract;

class ProdukTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
            "id"            =>$s->id,
            "gudang"        =>$s->gudang,
            "kode_produk"   =>$s->kode_produk,
            "nama_produk"   =>$s->nama_produk,
            "satuan"        =>$s->satuan,
            "perusahaan"    =>$s->perusahaan
        ];
    }
}