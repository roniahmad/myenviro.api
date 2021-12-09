<?php
namespace App\Traits\Hero\V1;

use DB;
use Carbon\Carbon;

use App\Models\Sales\V1\Jos;

trait RecomendationTrait
{
    function getClientCode($clientId)
    {
        $code = DB::table('master.klien')
                ->select('kode')
                ->where('id', $clientId)
                ->first();

        $result = "";
        if($code){
            $result = $code->kode;
        }

        return $result;
    }

    function generateRecomendationNumber($year)
    {
        $number  = 1;

        /*
        select  MAX(CONVERT(RIGHT(nomor_tiket,6), UNSIGNED INTEGER)) as maksi
        from tiket where LEFT(nomor_tiket, 10)='NPM567.21.';
        */

        $max = DB::table('envidesk.rekomendasi')
               ->select(DB::raw('MAX(CONVERT(nomor_rekomendasi, UNSIGNED INTEGER)) as maksi'))
               ->where('envidesk.rekomendasi.tahun', $year)
               ->first();

        if($max){
            $maksi = $max->maksi;
            if($maksi>=$number) {
                $maksi ++;
            }else{
                $maksi = $number;
            }
        }

        $ticket_number = sprintf('%07d', $maksi);
        return $ticket_number;
    }

}
