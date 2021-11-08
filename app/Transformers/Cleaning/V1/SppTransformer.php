<?php
namespace App\Transformers\Cleaning\V1;

use League\Fractal\TransformerAbstract;

class SppTransformer extends TransformerAbstract
{
    public function transform($s)
    {
        return[
            "perusahaan_id"                 => $s->perusahaan_id,
            "perusahaan_nama"               => $s->perusahaan_nama,
            "perusahaan_alamat"             => $s->perusahaan_alamat,
            "kabkota_perusahaan"            => $s->kabkota_perusahaan,
            "deskripsi_kabkota_perusahaan"  => $s->deskripsi_kabkota_perusahaan,
            "perusahaan_kodepos"            => $s->perusahaan_kodepos,

            "klien_id"                      => $s->klien_id,
            "klien_nama"                    => $s->klien_nama,
            "klien_alamat"                  => $s->klien_alamat,
            "kabkota_klien"                 => $s->kabkota,
            "deskripsi_kabkota_klien"       => $s->deskripsi_kabkota_klien,
            "klien_kodepos"                 => $s->klien_kodepos,

            "no_spp"                        => $s->no_spp,
            "produk_id"                     => $s->produk_id,
            "produk"                        => $s->produk,
            "frekuensi_pekerjaan"           => $s->frekuensi_pekerjaan,
            "tgl_awal"                      => $s->tgl_awal,
            "tgl_akhir"                     => $s->tgl_akhir,
            "nilai_kontrak"                 => $s->nilai_kontrak,
        ];
    }
}
