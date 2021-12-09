<?php
namespace App\Traits\Client\V1;

use DB;
use Carbon\Carbon;

use App\Models\Sales\V1\Jos;

trait ComplaintTrait
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

    function getCompanyCodeByJos($josid)
    {
        /*
        select mp.kode from sales.jos sj
        left join master.perusahaan mp on (sj.perusahaan_id=mp.id)
        where sj.id =1
        */

        $code = Jos::leftJoin('master.perusahaan as mj', function($joinMJ){
                    $joinMJ->On('mj.id','=','sales.jos.perusahaan_id');
                })
                ->select('mj.kode')->first();

        $result = "";
        if($code){
            $result = $code->kode;
        }

        return $result;
    }

    function generateTicketNumber($clientCode)
    {
        $ticket  = $clientCode . ".";
        $today   = Carbon::today()->format('y');
        $ticket .= Carbon::today()->format('y') . ".";
        $number  = 1;

        /*
        select  MAX(CONVERT(RIGHT(nomor_tiket,6), UNSIGNED INTEGER)) as maksi
        from tiket where LEFT(nomor_tiket, 10)='NPM567.21.';
        */

        $max = DB::table('envidesk.tiket')
               ->select(DB::raw('MAX(CONVERT(RIGHT(nomor_tiket,6), UNSIGNED INTEGER)) as maksi'))
               ->where(DB::raw('LEFT(nomor_tiket,10)'), $ticket)
               ->first();

        if($max){
            $maksi = $max->maksi;
            if($maksi>=$number) {
                $maksi ++;
            }else{
                $maksi = $number;
            }
        }

        $ticket_number = $ticket . sprintf('%06d', $maksi);
        return $ticket_number;
    }

}
