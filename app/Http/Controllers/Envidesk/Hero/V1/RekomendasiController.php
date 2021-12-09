<?php
namespace App\Http\Controllers\Envidesk\Hero\V1;

use Auth;
use Illuminate\Http\Request;
use DB;
use Validator;
use Carbon\Carbon;
use Config;

use App\Http\Controllers\Base\V1\BaseApiController;

use App\Models\Envidesk\V1\Rekomendasi;

use App\Traits\Hero\V1\RecomendationTrait;

use App\Transformers\Envidesk\V1\RekomendasiTransformer;
use App\Transformers\Envidesk\V1\RekomendasiDetailTransformer;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class RekomendasiController extends BaseApiController
{
    use RecomendationTrait;

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
            'perusahaan_id'=>'required',
        ]);

        if ($validator->fails()){
          return response()->json($validator->errors());
        }

        $perusahaanid = $request->perusahaan_id;
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
                        'envidesk.rekomendasi.id',
                        'envidesk.rekomendasi.nomor_rekomendasi',
                        'sj.no_jos','kp.nama',
                        DB::raw('DATE(envidesk.rekomendasi.tanggal_rekomendasi) as date_rekomendasi'),
                        DB::raw('TIME(envidesk.rekomendasi.tanggal_rekomendasi) as time_rekomendasi'),
                        DB::raw("CONCAT(LEFT(envidesk.rekomendasi.rekomendasi,150),'...') as rekomendasi"),
                        'envidesk.rekomendasi.gambar_rekomendasi',
                        DB::raw('DATE(envidesk.rekomendasi.tanggal_closed) as date_closed'),
                        DB::raw('TIME(envidesk.rekomendasi.tanggal_closed) as time_closed'),
                        'envidesk.rekomendasi.closed'
                    )
                    ->where('sj.perusahaan_id', $perusahaanid)->get();

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

    public function createRecommendation(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'jos_id'=>'required',
            'klien_id'=>'required',
            'perusahaan_id'=>'required',
            'pic_perusahaan'=>'required',
            'rekomendasi'=>'required',
            // 'gambar_rekomendasi' => 'required',
        ]);

        if ($validator->fails()){
          return response()->json($validator->errors());
        }

        $josid = $request->jos_id;
        $klienid = $request->klien_id;
        $perusahaanid = $request->perusahaan_id;
        $picperusahaan = $request->pic_perusahaan;
        $rekomendasi = $request->rekomendasi;
        $gambar = $request->gambar_rekomendasi;
        $closed = 0;

        $user = (object) ['image' => ""];

        if($request->hasFile('image')){
            $original_filename = $request->file('image')->getClientOriginalName();
            $original_filename_arr = explode('.', $original_filename);
            $file_ext = end($original_filename_arr);
            $destination_path = (Config('constants.upload_dir.envidesk_recomendation'));
            $filename_path = Config('constants.upload_filename.envidesk_recomendation');
            $file_prefix = Config('constants.upload_file_prefix.envidesk_recomendation');

            $year = Carbon::now()->format('Y');
            $ticket_number = $this->generateRecomendationNumber($year);
            $today = Carbon::now()->format('Y-m-d H:i:s');
            $year = Carbon::now()->format('Y');

            $err = Config('constants.messages.envidesk_recommendation_fail');
            $err_file_not_found = Config('constants.messages.file_not_found');

            $image = $file_prefix.'_' . time() . '.' . $file_ext;

            if ($request->file('image')->move($destination_path, $image)) {
                $user->image = $filename_path . $image;

                $ticket = new Rekomendasi;
                $ticket->nomor_rekomendasi        = $ticket_number;
                $ticket->tahun                    = $year;
                $ticket->jos_id                   = $josid;
                $ticket->klien_id                 = $klienid;
                $ticket->perusahaan_id            = $perusahaanid;
                $ticket->pic_perusahaan           = $picperusahaan;
                $ticket->rekomendasi              = $rekomendasi;
                $ticket->tanggal_rekomendasi      = $today;
                $ticket->gambar_rekomendasi       = $user->image;
                $ticket->closed                   = $closed;
                $ticket->save();

                $new_ticket = $ticket->id;
                $user->ticket_id = $new_ticket;
                $user->ticket_no = $ticket_number;

                return $this->responseRequestSuccess($user);
            } else {
                return $this->responseRequestError($err);
            }

        }else{
            return $this->responseRequestError('File not found');
        }

    }

    protected function responseRequestSuccess($ret)
    {
        return response()->json(['success' => 1, 'data' => $ret], 200)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }

    protected function responseRequestError($message = 'Bad request', $statusCode = 200)
    {
        return response()->json(['success' => 0, 'message' => $message], $statusCode)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }

}
