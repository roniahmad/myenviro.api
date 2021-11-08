<?php
namespace App\Traits\Hero\V1;

use DB;
use Carbon\Carbon;

use App\Models\Cleaning\V1\DailyActivityDetil;

trait DacTrait
{
    function checkIfTodayDacExists($josId, $jobId)
    {
        $result = false;
        $today = Carbon::today()->format('Y-m-d');
        /*
        select count(cpdd.id)
        from cleaning.pl_dac_detil cpdd
        left join cleaning.pl_dac pd on (pd.id=cpdd.pl_dac_id)
        where pd.jos_id=1 and pd.jabatan_id=6;
        */

        $resource = DailyActivityDetil::select(
                        DB::raw('COUNt(*) as JML')
                    )
                    ->leftJoin('cleaning.pl_dac as pd', function($join){
                        $join->On('pd.id','=','pl_dac_detil.pl_dac_id');
                    })
                    ->where('pd.jos_id', $josId)
                    ->where('pd.jabatan_id', $jobId)
                    ->where('pd.tanggal_berulang', $today)
                    ->first();
        if($resource){
            $result = ($resource->JML > 0);
        }

        return $result;
    }
}
