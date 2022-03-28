<?php
namespace App\Http\Controllers\Sales\Hero\V1;

use Auth;
use Illuminate\Http\Request;
use DB;
use Validator;
use Carbon\Carbon;
use Config;


use App\Http\Controllers\Base\V1\BaseApiController;

use App\Models\Sales\V1\Jos;

use App\Transformers\Jos\V1\JosTransformer;

use App\Transformers\Jos\V1\JosByKlienTransformer;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class HeroJosController extends BaseApiController
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var JosTransformer
     */
    private $josTransformer;

        /**
     * @var JosByKlienTransformer
     */
    private $josByKlienTransformer;

    /**
     * Create a new ScheduleTreatmentController instance.
     *
     * @return void
     */
    public function __construct(Manager $fractal, JosTransformer $st, JosByKlienTransformer $sk)
    {
        $this->fractal = $fractal;
        $this->josTransformer = $st;
        $this->josByKlienTransformer = $sk;

    }

    public function getJosByEmployeeId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id'=>'required',
        ]);

        if ($validator->fails()) {
          return response()->json($validator->errors());
        }

        $employee = $request->employee_id;
        /*
        SELECT sj.id,
        sj.klien_id, mk.nama, mk.kode,
        sj.no_jos,sj.currency as currency_code, mr.singkatan as currency,sj.scope_of_work,
        sj.start_date , sj.end_date
        from sales.jos sj
        LEFT JOIN master.klien mk on (mk.id=sj.klien_id)
        LEFT JOIN master.referensi mr on (sj.currency=mr.id)
        LEFT JOIN sales.jos_man_power_detil jmpd on (jmpd.jos_id=sj.id)
        WHERE jmpd.pegawai_id =1
        and NOW() <= sj.end_date
        and mr.jenis =141;
        */
        $today = Carbon::today()->format('Y-m-d');
        $jenis_currency = Config('constants.referensi.jenis_currency');
        $resource = Jos::leftJoin('master.klien as mk', function($join){
                            $join->On('mk.id','=','sales.jos.klien_id');
                        })
                        ->leftJoin('master.referensi as mr', function($joinMR){
                            $joinMR->On('sales.jos.currency','=','mr.id');
                        })
                        ->leftJoin('sales.jos_man_power_detil as jmpd', function($joinJMPD){
                            $joinJMPD->On('jmpd.jos_id','=','sales.jos.id');
                        })
                    ->select(
                            'sales.jos.id',
                            'sales.jos.klien_id',
                            // 'mk.id as client_id',
                            'mk.nama', 'mk.kode',
                            'sales.jos.no_jos',
                            'sales.jos.currency as currency_code',
                            'mr.singkatan as currency',
                            'sales.jos.scope_of_work',
                            'sales.jos.billing_contact',
                            'sales.jos.start_date', 'sales.jos.end_date'
                    )
                    ->where('jmpd.pegawai_id', $employee)
                    ->whereNull('jmpd.deleted_at')
                    ->where('sales.jos.end_date', '>=',$today )
                    ->where('mr.jenis', $jenis_currency)
                    ->get();

        $jos = new Collection($resource, $this->josTransformer);
        $jos = $this->fractal->createData($jos)->toArray(); // Transform data

        return $this->respond($jos);

    }

     public function getJosKlienByEmployeeId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id'=>'required',
        ]);

        if ($validator->fails()) {
          return response()->json($validator->errors());
        }

        $employee = $request->employee_id;
        /*
        SELECT  kk.id , kk.nama as nama_klien
		    from sales.jos sj
		    left join master.klien kk on (kk.id =sj .klien_id)
		    LEFT JOIN sales.jos_man_power_detil jmpd on (jmpd.jos_id=sj.id)
		    WHERE jmpd.pegawai_id =1
        */
        $today = Carbon::today()->format('Y-m-d');
        $jenis_currency = Config('constants.referensi.jenis_currency');
        $resource = Jos::leftJoin('master.klien as mk', function($joinMK){
                            $joinMK->On('mk.id','=','sales.jos.klien_id');
                        })
                        ->leftJoin('sales.jos_man_power_detil as jmpd', function($joinJMPD){
                            $joinJMPD->On('jmpd.jos_id','=','sales.jos.id');
                        })
                    ->select(
                            'sales.jos.klien_id',
                            'mk.nama'
                    )
                    ->where('jmpd.pegawai_id', $employee)
                    ->whereNull('jmpd.deleted_at')
                    ->where('sales.jos.end_date', '>=',$today )
                    ->get();

        $jos = new Collection($resource, $this->josByKlienTransformer);
        $jos = $this->fractal->createData($jos)->toArray(); // Transform data

        return $this->respond($jos);

    }

}
