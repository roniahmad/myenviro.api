<?php
namespace App\Http\Controllers\Cleaning\Client\V1;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Validator;
use Response;
use Config;
use DB;
use Carbon\Carbon;

use App\Http\Controllers\Base\V1\BaseApiController;

use App\Models\Cleaning\V1\DailyReportDetilImages;

use App\Transformers\Cleaning\V1\DailyReportDetailImagesTransformer;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class DailyReportDetailImagesController extends BaseApiController
{

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var DailyReportDetailImagesTransformer
     */
    private $detailImagesTransformer;

    /**
     * Create a new ScheduleTreatmentController instance.
     *
     * @return void
     */
    public function __construct(Manager $fractal, DailyReportDetailImagesTransformer $st)
    {
        $this->fractal = $fractal;
        $this->detailImagesTransformer = $st;
    }

    public function deleteDailyReportImage(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id'=>'required',
        ]);

        if ($validator->fails()){
          return response()->json($validator->errors());
        }

        $deleted = $request->id;
        $row = DailyReportDetilImages::select('filename')->where('id',$deleted)->first();

        if($row){
            $filename = $row->filename;
            $afc = DB::table('cleaning.ldd_images')->where('id', $deleted)->delete();

            if($afc>0){

                if(File::exists('.'.$filename)){
                    File::delete('.'.$filename);
                }

                $resource = array(
                    'success' => 1,
                    'message' => 'do', //delete oke
                );

            }else{

                $resource = array(
                    'success' => 0,
                    'message' => 'df', //delete fail
                );


            }

        }else{

            $resource = array(
                'success' => 0,
                'message' => 'nf', //not found
            );

        }


        return $this->respond($resource);
    }

    public function getDailyReportImage(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'jos_id'=>'required',
            'date_report' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()){
          return response()->json($validator->errors());
        }

        /*
        select li.id, li.ldd_id, li.filename,
        DATE(li.created_at) as tgl_capture, TIME(li.created_at) as jam_capture
        from cleaning.ldd_images li
        left join cleaning.laporan_dac_detil ldd on (ldd.id=li.ldd_id)
        left join cleaning.laporan_dac ld on (ld.id=ldd.laporan_dac_id)
        where ld.jos_id = 1
        and ld.tanggal_lapor='2021-10-22'
        */
        $jos_id = $request->jos_id;
        // $date_report = Carbon::today()->format('Y-m-d');
        $date_report = $request->date_report;

        $resource = DailyReportDetilImages::select(
                        'ldd_images.id', 'ldd_images.ldd_id', 'ldd_images.filename',
                        DB::raw('DATE(ldd_images.created_at) as tgl_capture'),
                        DB::raw('TIME(ldd_images.created_at) as jam_capture')
                     )
                     ->leftJoin('cleaning.laporan_dac_detil as ldd', function($joinLDD){
                         $joinLDD->On('ldd.id','=','ldd_images.ldd_id');
                     })
                     ->leftJoin('cleaning.laporan_dac as ld', function($joinLD){
                         $joinLD->On('ld.id','=','ldd.laporan_dac_id');
                     })
                     ->where('ld.jos_id', $jos_id)
                     ->where('ld.tanggal_lapor', $date_report)
                     ->get();

         $detail = new Collection($resource, $this->detailImagesTransformer);
         $detail = $this->fractal->createData($detail)->toArray(); // Transform data

         return $this->respond($detail);
    }

    public function uploadDailyReportImage(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'ldd_id'=>'required',
        ]);

        if ($validator->fails()){
          return response()->json($validator->errors());
        }

        $ldd_id = $request->ldd_id;

        $response = null;
        $user = (object) ['image' => ""];

        if ($request->hasFile('image')) {
            $original_filename = $request->file('image')->getClientOriginalName();
            $original_filename_arr = explode('.', $original_filename);
            $file_ext = end($original_filename_arr);
            $destination_path = (Config('constants.upload_dir.daily_report'));
            $filename_path = Config('constants.upload_filename.daily_report');
            $file_prefix = Config('constants.upload_file_prefix.daily_report');

            $image = $file_prefix.'_' . time() . '.' . $file_ext;

            if ($request->file('image')->move($destination_path, $image)) {
                $user->image = $filename_path . $image;

                $dr = new DailyReportDetilImages;
                $dr->ldd_id        = $ldd_id;
                $dr->filename      = $user->image;
                $dr->save();
                $new_dr = $dr->id;
                $user->image_id = $new_dr;

                return $this->responseRequestSuccess($user);
            } else {
                return $this->responseRequestError('Cannot upload file');
            }
        } else {
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
        return response()->json(['success' => 0, 'error' => $message], $statusCode)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }


}
