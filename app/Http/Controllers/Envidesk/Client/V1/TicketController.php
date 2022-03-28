<?php
namespace App\Http\Controllers\Envidesk\Client\V1;

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
        et.gambar_action

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
                    ->leftJoin('sales.jos as sj', function($joinSJ){
                        $joinSJ->On('envidesk.tiket.jos_id','=','sj.id');
                    })
                    ->leftJoin('master.klien_pegawai as kp', function($joinKP){
                        $joinKP->On('envidesk.tiket.pic_klien','=','kp.id');
                    })
                    ->select(
                        'envidesk.tiket.id', 'envidesk.tiket.nomor_tiket', 'envidesk.tiket.tanggal_pelayanan',
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
            'klien_id'=>'required',
        ]);

        if ($validator->fails()){
          return response()->json($validator->errors());
        }

        $klienid = $request->klien_id;
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
        where et.klien_id = '9c9d2d41-8da5-42c2-81ac-5a5245913aa3'
        and kp.perusahaan= '9c9d2d41-8da5-42c2-81ac-5a5245913aa3'
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
                        'sj.no_jos',
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
                    ->where('envidesk.tiket.klien_id', $klienid)
                    ->where('kp.perusahaan', $klienid)
                    ->where('mr.jenis', $jenis_help_topic)
                    ->where('mr2.jenis', $jenis_status_komplain)->get();

        $tickets = new Collection($resource, $this->ticketTransformer);
        $tickets = $this->fractal->createData($tickets)->toArray(); // Transform data

        return $this->respond($tickets);

    }

    public function createTicket(Request $request)
    {
        // dd($request);
        $validator = Validator::make($request->all(),[
            'topik'=>'required',
            'jos_id'=>'required',
            'klien_id'=>'required',
            'pic_klien'=>'required',
            'tanggal_pelayanan'=>'required|date_format:Y-m-d',
            'komplain'=>'required',
            // 'gambar_komplain' => 'required',
        ]);

        if ($validator->fails()){
          return response()->json($validator->errors());
        }

        $topik = $request->topik;
        $josid = $request->jos_id;
        $klienid = $request->klien_id;
        $picklien = $request->pic_klien;
        $tglpelayanan = $request->tanggal_pelayanan;
        $komplain = $request->komplain;
        $gambar = $request->gambar_komplain;

        $user = (object) ['image' => ""];

        if($request->hasFile('image')){
            $original_filename = $request->file('image')->getClientOriginalName();
            $original_filename_arr = explode('.', $original_filename);
            $file_ext = end($original_filename_arr);
            $destination_path = (Config('constants.upload_dir.envidesk_complaint'));
            $filename_path = Config('constants.upload_filename.envidesk_complaint');
            $file_prefix = Config('constants.upload_file_prefix.envidesk_complaint');

            $complaint_open = Config('constants.status_complaint.open');

            $client_code = $this->getCompanyCodeByJos($josid);
            $ticket_number = $this->generateTicketNumber($client_code);
            $today = Carbon::now()->format('Y-m-d H:i:s');

            $err = Config('constants.messages.envidesk_complaint_fail');
            $err_file_not_found = Config('constants.messages.file_not_found');

            $image = $file_prefix.'_' . time() . '.' . $file_ext;

            if ($request->file('image')->move($destination_path, $image)) {
                $user->image = $filename_path . $image;

                $ticket = new Ticket;
                $ticket->nomor_tiket        = $ticket_number;
                $ticket->topik              = $topik;
                $ticket->jos_id             = $josid;
                $ticket->klien_id           = $klienid;
                $ticket->pic_klien          = $picklien;
                $ticket->tanggal_pelayanan  = $tglpelayanan;
                $ticket->komplain           = $komplain;
                $ticket->tanggal_komplain   = $today;
                $ticket->status_komplain    = $complaint_open;
                $ticket->gambar_komplain    = $user->image;
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

    public function cancelTicket(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id'=>'required',
        ]);

        if ($validator->fails()){
          return response()->json($validator->errors());
        }

        $id = $request->id;
        $complaint_cancel = Config('constants.status_complaint.cancelled');
        $closed = 1;
        $today = Carbon::now()->format('Y-m-d H:i:s');

        $cancel = Ticket::where('id', $id)->update(['closed'=>$closed, 'status_komplain'=>$complaint_cancel, 'tanggal_closed'=>$today]);

        if($cancel>0){

            return $this->respond([
                'success' => 1,
                'message'=> Config('constants.messages.envidesk_complaint_cancel_ok')]
            );

        }else{

            return $this->respond([
                'success' => 0,
                'message'=> Config('constants.messages.envidesk_complaint_cancel_fail')]
            );

        }

    }

    public function rateTicket(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id'=>'required',
            'rating'=>'required',
            // 'feedback' => 'required',
        ]);

        if ($validator->fails()){
          return response()->json($validator->errors());
        }

        $id = $request->id;
        $rating = $request->rating;
        $feedback = $request->feedback;

        $complaint_completed = Config('constants.status_complaint.completed');
        $closed = 1;
        $today = Carbon::now()->format('Y-m-d H:i:s');
        $today_date = Carbon::now()->format('Y-m-d');
        $today_time = Carbon::now()->format('H:i:s');

        $feedback ="";
        if($request->has('feedback')) $feedback = $request->feedback;

        $rate = Ticket::where('id', $id)->update(['closed'=>$closed, 'status_komplain'=>$complaint_completed,
                  'rating'=>$rating, 'feedback'=>$feedback, 'tanggal_closed'=>$today]);

        if($rate>0){

            return $this->respond([
                'success' => 1,
                'message'=> Config('constants.messages.envidesk_feedback_ok'),
                'response_date' =>$today_date,
                'response_time' =>$today_time,
                'closed' => $closed,
                'id_komplain'=>$complaint_completed,
                'status_komplain'=>'Completed',
            ]
            );

        }else{

            return $this->respond([
                'success' => 0,
                'message'=> Config('constants.messages.envidesk_feedback_fail'),
                'response_date' =>$today_date,
                'response_time' =>$today_time,
                'closed' => $closed,
                'id_komplain'=>$complaint_completed,
                'status_komplain'=>'Completed',
            ]
            );

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
