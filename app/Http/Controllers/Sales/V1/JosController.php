<?php
namespace App\Http\Controllers\Sales\V1;

use Auth;
use Illuminate\Http\Request;
use DB;
use Validator;
use Carbon\Carbon;
use Config;


use App\Http\Controllers\Base\V1\BaseApiController;

use App\Models\Sales\V1\Jos;

use App\Transformers\Jos\V1\JosTransformer;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class JosController extends BaseApiController
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
     * Create a new ScheduleTreatmentController instance.
     *
     * @return void
     */
    public function __construct(Manager $fractal, JosTransformer $st)
    {
        $this->fractal = $fractal;
        $this->josTransformer = $st;
    }

    /**
     * Get JOS Information by klien Id.
     * @param Klien Id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getJosByClient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'klien_id'=>'required',
        ]);

        if ($validator->fails()) {
          return response()->json($validator->errors());
        }

        $klien = $request->klien_id;

        /*
        SELECT sj.id,
        sj.klien_id, mk.nama, mk.kode,
        sj.no_jos,sj.currency as currency_code, mr.singkatan as currency,sj.scope_of_work,
        sj.start_date , sj.end_date
        from sales.jos sj
        LEFT JOIN master.klien mk on (mk.id=sj.klien_id)
        left join master.referensi mr on (sj.currency=mr.id)
        WHERE sj.klien_id ='9c9d2d41-8da5-42c2-81ac-5a5245913aa3'
        and NOW() <= sj.end_date
        and mr.jenis =141;
        */
        $today = Carbon::today()->format('Y-m-d');
        $jenis_currency = Config('constants.referensi.jenis_currency');

        $resource = Jos::select(
                        'jos.id',
                        'jos.klien_id',
                        DB::raw('mk.id as client_id'),
                        'mk.nama', 'mk.kode',
                        'jos.no_jos',
                        DB::raw('jos.currency as currency_code'),
                        DB::raw('mr.singkatan as currency'),
                        'jos.scope_of_work',
                        'jos.start_date', 'jos.end_date'
                    )
                    ->leftJoin('master.klien as mk', function($join){
                        $join->On('mk.id','=','jos.klien_id');
                    })
                    ->leftJoin('master.referensi as mr', function($joinMR){
                        $joinMR->On('mr.id','=','jos.currency');
                    })
                    ->where('jos.klien_id', $klien)
                    ->where('jos.end_date', '>=', $today)
                    ->where('mr.jenis', $jenis_currency)
                    ->get();

        $jos = new Collection($resource, $this->josTransformer);
        $jos = $this->fractal->createData($jos)->toArray(); // Transform data

        return $this->respond($jos);
    }


}
