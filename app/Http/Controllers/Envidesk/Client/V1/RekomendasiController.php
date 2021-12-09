<?php
namespace App\Http\Controllers\Envidesk\Client\V1;

use Auth;
use Illuminate\Http\Request;
use DB;
use Validator;
use Carbon\Carbon;
use Config;

use App\Http\Controllers\Base\V1\BaseApiController;

use App\Models\Envidesk\V1\Rekomendasi;

use App\Transformers\Envidesk\V1\RekomendasiTransformer;
use App\Transformers\Envidesk\V1\RekomendasiDetailTransformer;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class RekomendasiController extends BaseApiController
{
    /**
     * @var Manager
     */
    private $fractal;
    /**
     * @var RekomendasiTransformer
     */
    private $recTransformer;
    /**
     * @var RekomendasiDetailTransformer
     */
    private $recDetTransformer;

    function __construct(Manager $fractal, RekomendasiTransformer $rTransformer, RekomendasiDetailTransformer $rdTransformer)
    {
        $this->fractal = $fractal;
        $this->recTransformer = $rTransformer;
        $this->recDetTransformer = $rdTransformer;
    }

    public function getRekomendasi(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'klien_id'=>'required',
        ]);

        if ($validator->fails()){
          return response()->json($validator->errors());
        }

        $klien_id = $request->klien_id;
        /*
        use envidesk;

        select et.id, et.nomor_rekomendasi, sj.no_jos,
        kp.nama,
        DATE(et.tanggal_rekomendasi) as date_rekomendasi,
        TIME(et.tanggal_rekomendasi) as time_rekomendasi,
        CONCAT(LEFT(et.rekomendasi,150),'...') as rekomendasi,
        et.gambar_rekomendasi, et.closed
        from envidesk.rekomendasi et
        left join sales.jos sj on (sj.id=et.jos_id)
        left join master.pegawai kp on (kp.id=et.pic_perusahaan)
        where sj.perusahaan_id = 'e9b02594-6b45-46ce-bd8c-596019a6d5f8'
        */

        $resource = Rekomendasi::leftJoin('sales.jos as sj', function($joinSJ){
                        $joinSJ->On('envidesk.rekomendasi.jos_id','=','sj.id');
                    })
                    ->leftJoin('master.pegawai as kp', function($joinKP){
                        $joinKP->On('envidesk.rekomendasi.pic_perusahaan','=','kp.id');
                    })
                    ->select(
                        'envidesk.rekomendasi.id','envidesk.rekomendasi.nomor_rekomendasi',
                        'sj.no_jos',
                        'kp.nama',
                        DB::raw('DATE(envidesk.rekomendasi.tanggal_rekomendasi) as date_rekomendasi'),
                        DB::raw('TIME(envidesk.rekomendasi.tanggal_rekomendasi) as time_rekomendasi'),
                        DB::raw("CONCAT(LEFT(envidesk.rekomendasi.rekomendasi,150),'...') as rekomendasi"),
                        'envidesk.rekomendasi.gambar_rekomendasi','envidesk.rekomendasi.closed'
                    )
                    ->where('envidesk.rekomendasi.klien_id', $klien_id)->get();

        $recommendation = new Collection($resource, $this->recTransformer);
        $recommendation = $this->fractal->createData($recommendation)->toArray(); // Transform data

        return $this->respond($recommendation);
    }

    public function getRekomendasiDetail(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id'=>'required',
        ]);

        if ($validator->fails()){
          return response()->json($validator->errors());
        }

        $recomId = $request->id;

        $resource = Rekomendasi::leftJoin('sales.jos as sj', function($joinSJ){
                        $joinSJ->On('envidesk.rekomendasi.jos_id','=','sj.id');
                    })
                    ->leftJoin('master.pegawai as kp', function($joinKP){
                        $joinKP->On('envidesk.rekomendasi.pic_perusahaan','=','kp.id');
                    })
                    ->select(
                        'envidesk.rekomendasi.id',
                        'envidesk.rekomendasi.nomor_rekomendasi',
                        'envidesk.rekomendasi.tahun',
                        'sj.no_jos','kp.nama',
                        DB::raw('DATE(envidesk.rekomendasi.tanggal_rekomendasi) as date_rekomendasi'),
                        DB::raw('TIME(envidesk.rekomendasi.tanggal_rekomendasi) as time_rekomendasi'),
                        'envidesk.rekomendasi.rekomendasi',
                        'envidesk.rekomendasi.gambar_rekomendasi',
                        DB::raw('DATE(envidesk.rekomendasi.rekomendasi_dibaca) as date_dibaca'),
                        DB::raw('TIME(envidesk.rekomendasi.rekomendasi_dibaca) as time_dibaca'),
                        'envidesk.rekomendasi.feedback',
                        DB::raw('DATE(envidesk.rekomendasi.tanggal_closed) as date_closed'),
                        DB::raw('TIME(envidesk.rekomendasi.tanggal_closed) as time_closed'),
                        'envidesk.rekomendasi.closed'
                    )
                    ->where('envidesk.rekomendasi.id', $recomId)->get();

        $recommendation = new Collection($resource, $this->recDetTransformer);
        $recommendation = $this->fractal->createData($recommendation)->toArray(); // Transform data

        return $this->respond($recommendation);

    }

    public function updateStatusReadRecomendation(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id'=>'required',
        ]);

        if ($validator->fails()){
          return response()->json($validator->errors());
        }

        $id = $request->id;
        $today = Carbon::now()->format('Y-m-d H:i:s');
        $is_read = Rekomendasi::where('id', $id)->select('rekomendasi_dibaca')->first();
        if($is_read && (trim($is_read->rekomendasi_dibaca))!=""){
            return $this->respond([
                'success' => 0,
                'message'=> 'err',
            ]);
        }

        $read = Rekomendasi::where('id', $id)->update(['rekomendasi_dibaca'=>$today]);
        if($read>0){
            return $this->respond([
                'success' => 1,
                'message'=> 'ok',
            ]);
        }else{
            return $this->respond([
                'success' => 0,
                'message'=> 'err',
            ]);
        }
    }

    public function recomFeedback(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id'=>'required',
            'feedback' => 'required',
        ]);

        if ($validator->fails()){
          return response()->json($validator->errors());
        }

        $id = $request->id;
        $feedback = $request->feedback;

        $closed = 1;
        $today = Carbon::now()->format('Y-m-d H:i:s');
        $today_date = Carbon::now()->format('Y-m-d');
        $today_time = Carbon::now()->format('H:i:s');

        $feedback ="";
        if($request->has('feedback')) $feedback = $request->feedback;

        $rate = Rekomendasi::where('id', $id)->update(['closed'=>$closed,
                  'feedback'=>$feedback, 'tanggal_closed'=>$today]);

        if($rate>0){

            return $this->respond([
                'success' => 1,
                'message'=> Config('constants.messages.envidesk_feedback_ok'),
                'response_date' =>$today_date,
                'response_time' =>$today_time,
                'closed' => $closed,
            ]
            );

        }else{

            return $this->respond([
                'success' => 0,
                'message'=> Config('constants.messages.envidesk_feedback_fail'),
                'response_date' =>$today_date,
                'response_time' =>$today_time,
                'closed' => $closed,
            ]
            );

        }

    }

}
