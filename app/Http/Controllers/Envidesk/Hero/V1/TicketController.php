<?php
namespace App\Http\Controllers\Envidesk\Hero\V1;

use Auth;
use Illuminate\Http\Request;
use DB;
use Validator;
use Carbon\Carbon;
use Config;

use App\Http\Controllers\Base\V1\BaseApiController;

use App\Traits\Client\V1\ComplaintTrait;

use App\Models\Envidesk\V1\Ticket;

use App\Transformers\Envidesk\V1\TicketTransformer;
use App\Transformers\Envidesk\V1\TicketDetailTransformer;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class TicketController extends BaseApiController
{
    use ComplaintTrait;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var TicketTransformer
     */
    private $ticketTransformer;
    /**
     * @var TicketDetailTransformer
     */
    private $tdTransformer;

    function __construct(Manager $fractal, TicketTransformer $tt, TicketDetailTransformer $tdt)
    {
        $this->fractal = $fractal;
        $this->ticketTransformer = $tt;
        $this->tdTransformer = $tdt;
    }

    public function getTicketDetail(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id'=>'required',
        ]);

        if ($validator->fails()){
          return response()->json($validator->errors());
        }

        $ticketId = $request->id;

        /*
        use envidesk;

        select et.nomor_tiket, et.tanggal_pelayanan, sj.no_jos,
        kp.nama, mr2.deskripsi as status_komplain,
        DATE(et.tanggal_komplain) as date_komplain,
        TIME(et.tanggal_komplain) as time_komplain,
        mr.deskripsi as topik,
        CONCAT(LEFT(et.komplain,150),'...') as komplain,
        et.gambar_komplain,

        DATE(et.komplain_dibaca) as date_dibaca,
        TIME(et.komplain_dibaca) as time_dibaca,

        DATE(et.tanggal_in_qc) as date_qc_in,
        TIME(et.tanggal_in_qc) as time_qc_in,
        CONCAT(LEFT(et.qc,150),'...') as qc,
        DATE(et.tanggal_out_qc) as date_qc_out,
        TIME(et.tanggal_out_qc) as time_qc_out,
        et.gambar_qc ,

        DATE(et.tanggal_in_action) as date_action_in,
        TIME(et.tanggal_in_action) as time_action_in,
        CONCAT(LEFT(et.action_plan,150),'...') as action_Plan,
        DATE(et.tanggal_out_action) as date_action_out,
        TIME(et.tanggal_out_action) as time_action_out,
        et.gambar_action,
        et.rating,
        et.feedback

        from envidesk.tiket et
        left join master.referensi mr on (et.topik=mr.id)
        left join master.referensi mr2 on (et.status_komplain =mr2.id)
        left join sales.jos sj on (sj.id=et.jos_id)
        left join master.klien_pegawai kp on (kp.id=et.pic_klien)
        where et.id = 7
        and mr.jenis = 35
        and mr2.jenis = 26
        */

        $jenis_status_komplain = Config('constants.referensi.jenis_status_komplain'); //26
        $jenis_help_topic = Config('constants.referensi.jenis_help_topic'); //35

        $resource = Ticket::leftJoin('master.referensi as mr', function($joinMR){
                        $joinMR->On('envidesk.tiket.topik','=','mr.id');
                    })
                    ->leftJoin('master.referensi as mr2', function($joinMR2){
                        $joinMR2->On('envidesk.tiket.status_komplain','=','mr2.id');
                    })

                    ->leftJoin('master.klien as kk', function($joinKK){
                        $joinKK->On('envidesk.tiket.klien_id','=','kk.id');
                    })
                    ->leftJoin('sales.jos as sj', function($joinSJ){
                        $joinSJ->On('envidesk.tiket.jos_id','=','sj.id');
                    })
                    ->leftJoin('master.klien_pegawai as kp', function($joinKP){
                        $joinKP->On('envidesk.tiket.pic_klien','=','kp.id');
                    })
                    ->select(
                        'envidesk.tiket.id', 'envidesk.tiket.nomor_tiket', 'envidesk.tiket.tanggal_pelayanan',
                        'kk.nama as nama_klient',
                        'sj.no_jos',
                        'kp.nama',
                        DB::raw('mr2.deskripsi as status_komplain'),
                        DB::raw('DATE(envidesk.tiket.tanggal_komplain) as date_komplain'),
                        DB::raw('TIME(envidesk.tiket.tanggal_komplain) as time_komplain'),
                        DB::raw('mr.deskripsi as topik'),
                        DB::raw("envidesk.tiket.komplain as komplain"),
                        'envidesk.tiket.gambar_komplain',
                        DB::raw('DATE(envidesk.tiket.komplain_dibaca) as date_dibaca'),
                        DB::raw('TIME(envidesk.tiket.komplain_dibaca) as time_dibaca'),
                        DB::raw('DATE(envidesk.tiket.tanggal_in_qc) as date_qc_in'),
                        DB::raw('TIME(envidesk.tiket.tanggal_in_qc) as time_qc_in'),
                        "envidesk.tiket.qc",
                        DB::raw('DATE(envidesk.tiket.tanggal_out_qc) as date_qc_out'),
                        DB::raw('TIME(envidesk.tiket.tanggal_out_qc) as time_qc_out'),
                        'envidesk.tiket.gambar_qc' ,
                        DB::raw('DATE(envidesk.tiket.tanggal_in_action) as date_action_in'),
                        DB::raw('TIME(envidesk.tiket.tanggal_in_action) as time_action_in'),
                        "envidesk.tiket.action_plan",
                        DB::raw('DATE(envidesk.tiket.tanggal_out_action) as date_action_out'),
                        DB::raw('TIME(envidesk.tiket.tanggal_out_action) as time_action_out'),
                        'envidesk.tiket.gambar_action', 'envidesk.tiket.rating','envidesk.tiket.feedback'
                    )
                    ->where('envidesk.tiket.id', $ticketId)
                    ->where('mr.jenis', $jenis_help_topic)
                    ->where('mr2.jenis', $jenis_status_komplain)
                    ->get();

        $tickets = new Collection($resource, $this->tdTransformer);
        $tickets = $this->fractal->createData($tickets)->toArray(); // Transform data

        return $this->respond($tickets);
    }

    public function getTicket(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'perusahaan_id'=>'required',
        ]);

        if ($validator->fails()){
          return response()->json($validator->errors());
        }

        $perusahaanid = $request->perusahaan_id;
        // $employee = $request->employee_id;
        
        // $employee = $request->has('employee_id') ? $request->employee_id : "";

        if ($request->has('employee_id')){
            $employee = $request->employee_id;

            $jenis_status_komplain = Config('constants.referensi.jenis_status_komplain'); //26
            $jenis_help_topic = Config('constants.referensi.jenis_help_topic'); //35

            $resource = Ticket::leftJoin('master.referensi as mr', function($joinMR){
                        $joinMR->On('envidesk.tiket.topik','=','mr.id');
                    })
                    ->leftJoin('master.referensi as mr2', function($joinMR2){
                        $joinMR2->On('envidesk.tiket.status_komplain','=','mr2.id');
                    })
                    ->leftJoin('master.klien as kk', function($joinKK){
                        $joinKK->On('envidesk.tiket.klien_id','=','kk.id');
                    })
                    ->leftJoin('sales.jos as sj', function($joinSJ){
                        $joinSJ->On('envidesk.tiket.jos_id','=','sj.id');
                    })
                    ->leftJoin('sales.jos_man_power_detil as jmpd', function($joinJMPD){
                        $joinJMPD->On('jmpd.jos_id','=','sj.id');
                    })
                    ->leftJoin('master.klien_pegawai as kp', function($joinKP){
                        $joinKP->On('envidesk.tiket.pic_klien','=','kp.id');
                    })

                    ->select(
                        'envidesk.tiket.id','envidesk.tiket.nomor_tiket', 'envidesk.tiket.tanggal_pelayanan',
                        'kk.nama as nama_klient',
                        'sj.no_jos',
                        // 'jmpd.pegawai_id',
                        'kp.nama',
                        DB::raw('mr2.deskripsi as status_komplain'),
                        DB::raw('DATE(envidesk.tiket.tanggal_komplain) as date_komplain'),
                        DB::raw('TIME(envidesk.tiket.tanggal_komplain) as time_komplain'),
                        DB::raw('mr.deskripsi as topik'),
                        DB::raw("CONCAT(LEFT(envidesk.tiket.komplain,150),'...') as komplain"),
                        'envidesk.tiket.gambar_komplain',
                        DB::raw('DATE(envidesk.tiket.tanggal_in_qc) as date_qc'),
                        DB::raw('DATE(envidesk.tiket.komplain_dibaca) as date_dibaca'),
                        DB::raw('TIME(envidesk.tiket.komplain_dibaca) as time_dibaca')
                    )
                    ->where('sj.perusahaan_id', $perusahaanid)
                    ->where('jmpd.pegawai_id', $employee)
                    ->where('mr.jenis', $jenis_help_topic)
                    ->where('mr2.jenis', $jenis_status_komplain);

                    if($request->has('client_id')){
                        $resource = $resource->where('kk.id', $request->client_id);
                    }
                    
                    $resource = $resource->get();

        }else{
            
        
        /*
        select et.nomor_tiket, et.tanggal_pelayanan, sj.no_jos,
        kp.nama, mr2.deskripsi as status_komplain,
        DATE(et.tanggal_komplain) as date_komplain,
        TIME(et.tanggal_komplain) as time_komplain,
        mr.deskripsi as topik,
        CONCAT(LEFT(et.komplain,150),'...') as komplain
        from envidesk.tiket et
        left join master.referensi mr on (et.topik=mr.id)
        left join master.referensi mr2 on (et.status_komplain =mr2.id)
        left join sales.jos sj on (sj.id=et.jos_id)
        left join master.klien_pegawai kp on (kp.id=et.pic_klien)
        where sj.perusahaan_id = 'e9b02594-6b45-46ce-bd8c-596019a6d5f8'
        and mr.jenis = 35
        and mr2.jenis = 26
        */

        $jenis_status_komplain = Config('constants.referensi.jenis_status_komplain'); //26
        $jenis_help_topic = Config('constants.referensi.jenis_help_topic'); //35

        $resource = Ticket::leftJoin('master.referensi as mr', function($joinMR){
                        $joinMR->On('envidesk.tiket.topik','=','mr.id');
                    })
                    ->leftJoin('master.referensi as mr2', function($joinMR2){
                        $joinMR2->On('envidesk.tiket.status_komplain','=','mr2.id');
                    })
                    ->leftJoin('master.klien as kk', function($joinKK){
                        $joinKK->On('envidesk.tiket.klien_id','=','kk.id');
                    })
                    ->leftJoin('sales.jos as sj', function($joinSJ){
                        $joinSJ->On('envidesk.tiket.jos_id','=','sj.id');
                    })

                    ->leftJoin('master.klien_pegawai as kp', function($joinKP){
                        $joinKP->On('envidesk.tiket.pic_klien','=','kp.id');
                    })

                    ->select(
                        'envidesk.tiket.id','envidesk.tiket.nomor_tiket', 'envidesk.tiket.tanggal_pelayanan',
                        'kk.nama as nama_klient',
                        'sj.no_jos',
                        // 'jmpd.pegawai_id',
                        'kp.nama',
                        DB::raw('mr2.deskripsi as status_komplain'),
                        DB::raw('DATE(envidesk.tiket.tanggal_komplain) as date_komplain'),
                        DB::raw('TIME(envidesk.tiket.tanggal_komplain) as time_komplain'),
                        DB::raw('mr.deskripsi as topik'),
                        DB::raw("CONCAT(LEFT(envidesk.tiket.komplain,150),'...') as komplain"),
                        'envidesk.tiket.gambar_komplain',
                        DB::raw('DATE(envidesk.tiket.tanggal_in_qc) as date_qc'),
                        DB::raw('DATE(envidesk.tiket.komplain_dibaca) as date_dibaca'),
                        DB::raw('TIME(envidesk.tiket.komplain_dibaca) as time_dibaca')
                    )
                    ->where('sj.perusahaan_id', $perusahaanid)
                    ->where('mr.jenis', $jenis_help_topic)
                    ->where('mr2.jenis', $jenis_status_komplain)->get();


        }

                    $tickets = new Collection($resource, $this->ticketTransformer);
                    $tickets = $this->fractal->createData($tickets)->toArray(); // Transform data

                    return $this->respond($tickets);
    }

    public function updateStatusReadComplaint(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id'=>'required',
        ]);

        if ($validator->fails()){
          return response()->json($validator->errors());
        }

        $id = $request->id;
        $today = Carbon::now()->format('Y-m-d H:i:s');
        $is_read = Ticket::where('id', $id)->select('komplain_dibaca')->first();
        if($is_read && (trim($is_read->komplain_dibaca))!=""){
            return $this->respond([
                'success' => 0,
                'message'=> 'err',
            ]);
        }

        $read = Ticket::where('id', $id)->update(['komplain_dibaca'=>$today]);
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
    /**
    * update when ticket was QC in
    */
    public function qcTicketIn(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id'=>'required',
            // 'qc_in'=>'required',
            'qc'=>'required',
            // 'gambar_qc' => 'required',
        ]);

        if ($validator->fails()){
          return response()->json($validator->errors());
        }

        $today = Carbon::now()->format('Y-m-d H:i:s');
        $today_date = Carbon::now()->format('Y-m-d');
        $today_time =  Carbon::now()->format('H:i:s');

        $id = $request->id;
        $qcin = $today;
        $qc = $request->qc;
        $gambar = $request->gambar_qc;

        $user = (object) ['image' => ""];

        $err = Config('constants.messages.envidesk_qc_fail');
        $ok = Config('constants.messages.envidesk_qc_ok');
        $err_file_not_found = Config('constants.messages.file_not_found');
        $filename = "";
        if($request->hasFile('image')){
            $original_filename = $request->file('image')->getClientOriginalName();
            $original_filename_arr = explode('.', $original_filename);
            $file_ext = end($original_filename_arr);
            $destination_path = (Config('constants.upload_dir.envidesk_qc'));
            $filename_path = Config('constants.upload_filename.envidesk_qc');
            $file_prefix = Config('constants.upload_file_prefix.envidesk_qc');

            $complaint_inprogress = Config('constants.status_complaint.inprogress');

            $image = $file_prefix.'_' . time() . '.' . $file_ext;
            $closed = 0;
            if ($request->file('image')->move($destination_path, $image)) {
                $user->image = $filename_path . $image;
                $filename = $filename_path . $image;

                $qcprocess = Ticket::where('id', $id)->update(
                    ['closed'=>$closed, 'status_komplain'=>$complaint_inprogress,
                    'qc'=>$qc,
                    'tanggal_in_qc'=>$qcin, 'gambar_qc'=>$user->image]);

                if($qcprocess>0){

                    return $this->respond([
                        'success' => 1,
                        'message'=> $ok,
                        'filename' => $filename,
                        'upload_date' =>$today_date,
                        'upload_time' =>$today_time,
                    ]);

                }else{

                    return $this->respond([
                        'success' => 0,
                        'message'=> $err,
                        'filename' => $filename,
                        'upload_date' =>$today_date,
                        'upload_time' =>$today_time,
                    ]);

                }

            } else {
                return $this->responseRequestError($err);
            }

        }else{
            return $this->responseRequestError($err_file_not_found);
        }

    }

    public function qcTicketOut(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id'=>'required',
            // 'qc_out'=>'required',
        ]);

        if ($validator->fails()){
          return response()->json($validator->errors());
        }

        $today = Carbon::now()->format('Y-m-d H:i:s');
        $today_date = Carbon::now()->format('Y-m-d');
        $today_time = Carbon::now()->format('H:i:s');

        $id = $request->id;
        $qcout = $today;

        $complaint_inprogress = Config('constants.status_complaint.inprogress');

        $err = Config('constants.messages.envidesk_qc_fail');
        $ok = Config('constants.messages.envidesk_qc_ok');
        $err_file_not_found = Config('constants.messages.file_not_found');
        $closed = 0;

        $qcprocess = Ticket::where('id', $id)->update(
            ['closed'=>$closed, 'status_komplain'=>$complaint_inprogress,
            'tanggal_out_qc'=>$qcout]);

        if($qcprocess>0){
            return $this->respond([
                'success' => 1,
                'message'=> $ok,
                'response_date' => $today_date,
                'response_time' => $today_time,
            ]);
        }else{
            return $this->respond([
                'success' => 0,
                'message'=> $err,
                'response_date' => $today_date,
                'response_time' => $today_time,
            ]);
        }
    }

    public function actionTicketIn(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id'=>'required',
            // 'action_in'=>'required',
            'action_plan'=>'required',
            // 'gambar_action' => 'required',
        ]);

        if ($validator->fails()){
          return response()->json($validator->errors());
        }

        $today = Carbon::now()->format('Y-m-d H:i:s');
        $today_date = Carbon::now()->format('Y-m-d');
        $today_time =  Carbon::now()->format('H:i:s');

        $id = $request->id;
        $qcin = $today;
        $qc = $request->action_plan;
        $gambar = $request->gambar_action;

        $user = (object) ['image' => ""];

        $err = Config('constants.messages.envidesk_action_fail');
        $ok = Config('constants.messages.envidesk_action_ok');
        $err_file_not_found = Config('constants.messages.file_not_found');
        $filename = "";
        if($request->hasFile('image')){
            $original_filename = $request->file('image')->getClientOriginalName();
            $original_filename_arr = explode('.', $original_filename);
            $file_ext = end($original_filename_arr);
            $destination_path = (Config('constants.upload_dir.envidesk_action'));
            $filename_path = Config('constants.upload_filename.envidesk_action');
            $file_prefix = Config('constants.upload_file_prefix.envidesk_action');

            $complaint_inprogress = Config('constants.status_complaint.inprogress');

            $image = $file_prefix.'_' . time() . '.' . $file_ext;
            $closed = 0;
            if ($request->file('image')->move($destination_path, $image)) {
                $user->image = $filename_path . $image;
                $filename = $filename_path . $image;

                $qcprocess = Ticket::where('id', $id)->update(
                    ['closed'=>$closed, 'status_komplain'=>$complaint_inprogress,
                    'action_plan' => $qc,
                    'tanggal_in_action'=>$qcin, 'gambar_action'=>$user->image]);

                if($qcprocess>0){

                    return $this->respond([
                        'success' => 1,
                        'message'=> $ok,
                        'filename' => $filename,
                        'upload_date' =>$today_date,
                        'upload_time' =>$today_time,
                    ]);

                }else{

                    return $this->respond([
                        'success' => 0,
                        'message'=> $err,
                        'filename' => $filename,
                        'upload_date' =>$today_date,
                        'upload_time' =>$today_time,
                    ]);

                }

            } else {
                return $this->responseRequestError($err);
            }

        }else{
            return $this->responseRequestError($err_file_not_found);
        }

    }

    public function actionTicketOut(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id'=>'required',
            // 'action_out'=>'required',
        ]);

        if ($validator->fails()){
          return response()->json($validator->errors());
        }

        $today = Carbon::now()->format('Y-m-d H:i:s');
        $today_date = Carbon::now()->format('Y-m-d');
        $today_time = Carbon::now()->format('H:i:s');

        $id = $request->id;
        $qcout = $today;

        $complaint_inprogress = Config('constants.status_complaint.inprogress');

        $err = Config('constants.messages.envidesk_action_fail');
        $ok = Config('constants.messages.envidesk_action_ok');
        $err_file_not_found = Config('constants.messages.file_not_found');
        $closed = 0;

        $qcprocess = Ticket::where('id', $id)->update([
            'closed'=>$closed,
            'status_komplain'=>$complaint_inprogress,
            'tanggal_out_action'=>$qcout
        ]);

        if($qcprocess>0){
            return $this->respond([
                'success' => 1,
                'message'=> $ok,
                'response_date' => $today_date,
                'response_time' => $today_time,
            ]);
        }else{
            return $this->respond([
                'success' => 0,
                'message'=> $err,
                'response_date' => $today_date,
                'response_time' => $today_time,
            ]);
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
