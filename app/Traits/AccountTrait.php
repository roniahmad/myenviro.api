<?php
namespace App\Traits;

use DB;

trait AccountTrait
{
    function getLoggedUserInfo($email)
    {
        $data = DB::select("
            SELECT muo.tipe_user, muo.email, muo.organisasi_id as id_organisasi,
            (
            CASE
                WHEN muo.tipe_user = 1 THEN
                    (select CONCAT(nama,'#', alamat) from master.perusahaan where master.perusahaan.id = muo.organisasi_id)
                ELSE
                    (SELECT CONCAT(nama,'#', alamat) from master.klien where master.klien.id = muo.organisasi_id)
            END
            ) as data_perusahaan,
            (
            CASE
                WHEN muo.tipe_user = 1 THEN
                    (select CONCAT(nama,'#', alamat) from master.pegawai where master.pegawai.perusahaan = muo.organisasi_id and master.pegawai.id=muo.pegawai_id)
                ELSE
                    (SELECT CONCAT(nama,'#', alamat) from master.klien_pegawai where master.klien_pegawai.perusahaan = muo.organisasi_id and master.klien_pegawai.id=muo.pegawai_id)
            END
            ) as data_pegawai
            FROM master.user_organisasi muo
            WHERE muo.email='".$email."' "
        );

        return $data;
    }
}
