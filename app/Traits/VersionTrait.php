<?php
namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait VersionTrait
{
    public function getNewVersion($app_name)
    {
        $ver = DB::table('aplikasi.versi_app')
                ->select('versi_baru')
                ->where('nama_app', $app_name)
                ->first();

        $versi_baru = "";
        if($ver){
            $versi_baru = $ver->versi_baru;
        }

        return $versi_baru;
    }
}
