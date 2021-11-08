<?php
namespace App\Traits\Hero\V1;

use DB;
use Carbon\Carbon;

use App\Models\Sales\V1\Jos;

trait JosTrait
{
    function getJosNumber($josId)
    {
        $result = '';
        $resource = Jos::select('no_jos')
                    ->where('id', $josId)
                    ->first();
        if($resource){
            $result = $resource->no_jos;
        }
        return $result;
    }
}
