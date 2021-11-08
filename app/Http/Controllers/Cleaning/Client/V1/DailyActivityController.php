<?php
namespace App\Http\Controllers\Cleaning\Client\V1;

use Auth;
use Illuminate\Http\Request;
use DB;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

use App\Mail\ProductOffer;

use App\Http\Controllers\Base\V1\BaseApiController;

use App\Models\Cleaning\V1\DailyActivityDetil;

use App\Transformers\Cleaning\V1\DailyActivityTransformer;


use App\Traits\Hero\V1\DacTrait;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class DailyActivityController extends BaseApiController
{
    use DacTrait;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var DailyActivityTransformer
     */
    private $dacTransformer;

    /**
     * Create a new ScheduleTreatmentController instance.
     *
     * @return void
     */
    public function __construct(Manager $fractal, DailyActivityTransformer $st)
    {
        $this->fractal = $fractal;
        $this->dacTransformer = $st;
    }

    /**
     * Get SPP Information by Id.
     * @param SppId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDailyByJosJobid(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'jos_id'=>'required',
            'job_id'=>'required',
        ]);

        if ($validator->fails()){
          return response()->json($validator->errors());
        }

        $josid = $request->jos_id;
        $jobid = $request->job_id;

        /*
        select cpdd.id, cpdd.mulai, cpdd.selesai, cpdd.pekerjaan
        from cleaning.pl_dac_detil cpdd
        left join cleaning.pl_dac pd on (pd.id=cpdd.pl_dac_id)
        where pd.jos_id=1 and pd.jabatan_id=6;
        */
        /*
        select cpdd.id, cpdd.mulai, cpdd.selesai, cpdd.pekerjaan
        from cleaning.pl_dac_detil cpdd
        left join cleaning.pl_dac pd on (pd.id=cpdd.pl_dac_id)
        where pd.jos_id=1 and pd.jabatan_id=6
        and pd.tanggal_berulang ='2021-09-27';

        */
        $today = Carbon::today()->format('Y-m-d');

        $todayDailyExists = $this->checkIfTodayDacExists($josid, $jobid);

        if($todayDailyExists){
            $resource = DailyActivityDetil::select(
                            'pl_dac_detil.id','pl_dac_detil.mulai',
                            'pl_dac_detil.selesai',
                            'pl_dac_detil.pekerjaan'
                        )
                        ->leftJoin('cleaning.pl_dac as pd', function($join){
                            $join->On('pd.id','=','pl_dac_detil.pl_dac_id');
                        })
                        ->where('pd.jos_id', $josid)
                        ->where('pd.jabatan_id', $jobid)
                        ->where('pd.tanggal_berulang', $today)
                        ->get();
        }else{
            $resource = DailyActivityDetil::select(
                            'pl_dac_detil.id','pl_dac_detil.mulai',
                            'pl_dac_detil.selesai',
                            'pl_dac_detil.pekerjaan'
                        )
                        ->leftJoin('cleaning.pl_dac as pd', function($join){
                            $join->On('pd.id','=','pl_dac_detil.pl_dac_id');
                        })
                        ->where('pd.jos_id', $josid)
                        ->where('pd.jabatan_id', $jobid)
                        ->get();
        }


        $products = new Collection($resource, $this->dacTransformer);
        $products = $this->fractal->createData($products)->toArray(); // Transform data

        return $this->respond($products);
    }

}
