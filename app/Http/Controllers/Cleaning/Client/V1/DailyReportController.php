<?php
namespace App\Http\Controllers\Cleaning\Client\V1;

use Auth;
use Illuminate\Http\Request;
use Validator;
use Response;
use Config;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

use App\Mail\ProductOffer;

use App\Http\Controllers\Base\V1\BaseApiController;

use App\Models\Cleaning\V1\DailyReportDetil;
use App\Models\Cleaning\V1\DailyReport;

use App\Transformers\Cleaning\V1\DailyReportTransformer;
use App\Transformers\Cleaning\V1\DailyActivityReportTransformer;

use App\Traits\Hero\V1\JosTrait;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class DailyReportController extends BaseApiController
{
    use JosTrait;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var DailyReportTransformer
     */
    private $darTransformer;

    /**
    * @var DailyActivityReportTransformer
    */
    private $dacTransformer;


    /**
     * Create a new ScheduleTreatmentController instance.
     *
     * @return void
     */
    public function __construct(Manager $fractal, DailyReportTransformer $st, DailyActivityReportTransformer $dact)
    {
        $this->fractal = $fractal;
        $this->darTransformer = $st;
        $this->dacTransformer = $dact;
    }

    /**
     * Get Report.
     * @param SppId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDailyReport(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'jos_id'=>'required',
            'client_id'=>'required',
            'date_report' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()){
          return response()->json($validator->errors());
        }

        $josId = $request->jos_id;
        $clientId = $request->client_id;
        $dateReport= $request->date_report;

        /*
        select
        ldd.id, ld.tanggal_lapor, ldd.jenis_pekerjaan_cleaning as jp_id, mr.deskripsi as jenis_pekerjaan ,
        ldd.jos_area_id as joi, kap.nama,
        ldd.mulai, ldd.selesai, ldd.pekerjaan, ldd.catatan
        from cleaning.laporan_dac_detil ldd
        left join cleaning.laporan_dac ld on (ld.id=ldd.laporan_dac_id)
        left join master.referensi mr on (mr.id=ldd.jenis_pekerjaan_cleaning)
        LEFT join sales.jos sj on (ld.jos_id=sj.id)
        left join master.klien_area_pelayanan kap on (kap.id=ldd.jos_area_id)
        where ld.jos_id =1
        and ld.tanggal_lapor ='2021-09-29'
        and mr.jenis =31
        and sj.klien_id ='e9b02594-6b45-46ce-bd8c-596019a6d5f8'
        */
        // $dateReport = Carbon::today()->format('Y-m-d');
        $jpc = Config('constants.referensi.jenis_pekerjaan_cleaning');

        $resource = DailyReportDetil::select(
                        'laporan_dac_detil.id',
                        'ld.tanggal_lapor',
                        'laporan_dac_detil.laporan_dac_id',
                        DB::raw('laporan_dac_detil.jenis_pekerjaan_cleaning as jp_id'),
                        DB::raw('mr.deskripsi as jenis_pekerjaan'),
                        DB::raw('laporan_dac_detil.jos_area_id as joi'),
                        DB::raw('kap.nama as area'),
                        'laporan_dac_detil.mulai',
                        'laporan_dac_detil.selesai',
                        'laporan_dac_detil.pekerjaan',
                        'laporan_dac_detil.catatan'
                    )
                    ->leftJoin('cleaning.laporan_dac as ld', function($join){
                        $join->On('ld.id','=','laporan_dac_detil.laporan_dac_id');
                    })
                    ->leftJoin('master.referensi as mr', function($joinMR){
                        $joinMR->On('mr.id','=','laporan_dac_detil.jenis_pekerjaan_cleaning');
                    })
                    ->leftJoin('sales.jos as sj', function($joinSJ){
                        $joinSJ->On('sj.id','=','ld.jos_id');
                    })
                    ->leftJoin('master.klien_area_pelayanan as kap', function($joinKAP){
                        $joinKAP->On('kap.id','=','laporan_dac_detil.jos_area_id');
                    })
                    ->where('ld.jos_id', $josId)
                    // ->where('ld.tanggal_lapor', $dateReport)
                    ->where('ld.tanggal_lapor', $dateReport)
                    ->where('mr.jenis', $jpc)
                    ->where('sj.klien_id', $clientId)
                    ->orderBy('laporan_dac_detil.mulai', 'ASC')
                    ->orderBy('laporan_dac_detil.selesai', 'ASC')
                    ->get();

        $report = new Collection($resource, $this->darTransformer);
        $report = $this->fractal->createData($report)->toArray(); // Transform data

        return $this->respond($report);
    }

    public function getDailyActivityReport(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'dac_id' => 'required|integer',
            'jos_id' => 'required|integer',
        ]);

        if ($validator->fails()){
            return response()->json([
                'success' => 0,
                'message' => $validator->messages()
            ],422);
        }

        $id = $request->dac_id;
        $josid = $request->jos_id;
        /*
        select id, jos_id, pegawai_id,tanggal_lapor, deskripsi, rekomendasi,
        DATE(date_rekomendasi) as tanggal_rekomendasi,
        TIME(date_rekomendasi) as waktu_rekomendasi,
        feedback_klien,
        DATE(date_feedback) as tanggal_feedback,
        TIME(date_feedback) as waktu_feedback
        from laporan_dac
        where id=1 and jos_id =1
        */
        $resource = DailyReport::where('id', $id)
                    ->where('jos_id', $josid)
                    ->select(
                        'id', 'jos_id', 'pegawai_id', 'tanggal_lapor', 'deskripsi', 'rekomendasi',
                        DB::raw('DATE(date_rekomendasi) as tanggal_rekomendasi'),
                        DB::raw('TIME(date_rekomendasi) as waktu_rekomendasi'),
                        'feedback_klien',
                        DB::raw('DATE(date_feedback) as tanggal_feedback'),
                        DB::raw('TIME(date_feedback) as waktu_feedback')
                    )
                    ->get();

        $report = new Collection($resource, $this->dacTransformer);
        $report = $this->fractal->createData($report)->toArray(); // Transform data

        return $this->respond($report);
    }

    public function addDailyReportFeedback(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'dac_id' => 'required|integer',
            'jos_id' => 'required|integer',
            'feedback' => 'required|string',
        ]);

        if ($validator->fails()){
            return response()->json([
                'success' => 0,
                'message' => $validator->messages()
            ],422);
        }

        $id = $request->dac_id;
        $josid = $request->jos_id;
        $feedback = $request->feedback;

        $today = Carbon::now()->format('Y-m-d H:i:s');

        $recommendation = DailyReport::where('id', $id)->where('jos_id', $josid)
                          ->select('rekomendasi')->first();
        if($recommendation && trim($recommendation->rekomendasi)!=""){

            $avDaily = DailyReport::where('id', $id)->where('jos_id', $josid)->update(['feedback_klien'=>$feedback, 'date_feedback'=>$today]);

            if($avDaily>0){

                return $this->respond([
                    'success' => 1,
                    'message'=> Config('constants.messages.daily_report_feedback_added_ok')]
                );
            }else{
                return $this->respond([
                    'success' => 0,
                    'message'=> Config('constants.messages.daily_report_feedback_added_fail')]
                );
            }
        }else{
            return $this->respond([
                'success' => 0,
                'message'=> Config('constants.messages.daily_report_recommendation_not_found')]
            );
        }



    }

    public function addDailyReport(Request $request)
    {
        $validator = Validator::make($request->all(),[
            //laporan dac
            'id' => 'required|integer',
            'jos_id' => 'required|integer',
            'pegawai_id' => 'required|integer',
            // 'tanggal_lapor' => 'required|date|date_format:Y-m-d',
            'deskripsi' => 'required|string',

            //laporan
            'jenis_pekerjaan_cleaning' => 'required|integer',
            'jos_area_id'=>'required|integer',
            // 'mulai' => 'required|date_format:H:i',
            // 'selesai' => 'required|date_format:H:i',
        ]);

        if ($validator->fails()){
            return response()->json([
                'success' => 0,
                'message' => $validator->messages()
            ],422);
        }

        $id = $request->id;
        $jos_id = $request->jos_id;
        // $tanggal_lapor = $request->tanggal_lapor;
        $pegawai_id = $request->pegawai_id;
        $deskripsi = $request->deskripsi;

        $jenis_pekerjaan_cleaning = $request->jenis_pekerjaan_cleaning;
        $jos_area_id = $request->jos_area_id;
        $mulai = $request->mulai;
        $selesai = $request->selesai;
        $pekerjaan = $request->pekerjaan;
        $catatan = $request->catatan;

        //check if today laporan_dac is exists
        $today = Carbon::today()->format('Y-m-d');

        //if laporan_dac doesn't exists
        $ldd = 0;
        if($id<=0){

            $avDaily = DailyReport::select('id')
                        ->where('jos_id', $jos_id)
                        ->where('tanggal_lapor', $today)
                        ->first();

            $jos_number = $this->getJosNumber($jos_id);
            $deskripsi = Config('constants.prefix.laporan_dac_deskripsi').$jos_number;

            $new_dr = 0;
            if($avDaily){
                $new_dr = $avDaily->id;
            }else{
                $dr = new DailyReport;
                $dr->jos_id        = $jos_id;
                $dr->pegawai_id    = $pegawai_id;
                $dr->tanggal_lapor = $today;
                $dr->deskripsi     = $deskripsi;
                $dr->save();
                $new_dr = $dr->id;
            }

            if($new_dr > 0){

                $ldd = new DailyReportDetil;
                $ldd->laporan_dac_id            = $new_dr;
                $ldd->jenis_pekerjaan_cleaning  = $jenis_pekerjaan_cleaning;
                $ldd->jos_area_id               = $jos_area_id;
                $ldd->mulai                     = $mulai;
                $ldd->selesai                   = $selesai;
                $ldd->pekerjaan                 = $pekerjaan;
                $ldd->catatan                   = $catatan;
                $ldd->save();

                // $laporan_dac_detil = DailyReportDetil::create([
                //                     'laporan_dac_id'            => $new_dr,
                //                     'jenis_pekerjaan_cleaning'  => $jenis_pekerjaan_cleaning,
                //                     'jos_area_id'               => $jos_area_id,
                //                     'mulai'                     => $mulai,
                //                     'selesai'                   => $selesai,
                //                     'pekerjaan'                 => $pekerjaan,
                //                     'catatan'                   => $catatan,
                //                     ]);
            }

        }else{

            $ldd = new DailyReportDetil;
            $ldd->laporan_dac_id            = $id;
            $ldd->jenis_pekerjaan_cleaning  = $jenis_pekerjaan_cleaning;
            $ldd->jos_area_id               = $jos_area_id;
            $ldd->mulai                     = $mulai;
            $ldd->selesai                   = $selesai;
            $ldd->pekerjaan                 = $pekerjaan;
            $ldd->catatan                   = $catatan;
            $ldd->save();

            // $laporan_dac_detil = DailyReportDetil::create([
            //                     'laporan_dac_id'            => $id,
            //                     'jenis_pekerjaan_cleaning'  => $jenis_pekerjaan_cleaning,
            //                     'jos_area_id'               => $jos_area_id,
            //                     'mulai'                     => $mulai,
            //                     'selesai'                   => $selesai,
            //                     'pekerjaan'                 => $pekerjaan,
            //                     'catatan'                   => $catatan,
            //                     ]);

        }

        if($ldd){
            return $this->respond([
                'success' => 1,
                'message'=> Config('constants.messages.daily_report_added_ok')]
            );
        }else{
            return $this->respond([
                'success' => 0,
                'message'=> Config('constants.messages.daily_report_added_fail')]
            );
        }
    }

    public function deleteDailyReport(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id' => 'required',
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors());
        }

        $id = $request->id;

        $affectedRows = DailyReportDetil::where('id', $id)->delete();

        if($affectedRows>0){
            return $this->respond([
                'success' => 1,
                'message'=> Config('constants.messages.daily_report_deleted_ok')]
            );
        }else{
            return $this->respond([
                'success' => 0,
                'message'=> Config('constants.messages.daily_report_deleted_fail')]
            );
        }


    }


}
