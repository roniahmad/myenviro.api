<?php

namespace App\Http\Controllers\PestControl\V1;

use Auth;
use Illuminate\Http\Request;
use DB;
use Validator;
use Carbon\Carbon;
use Config;

use App\Http\Controllers\Base\V1\BaseApiController;

use App\Models\Pestcontrol\V1\JosPelayanan;

use App\Transformers\Pestcontrol\V1\PelayananTransformer;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

//pelayanan yaitu jadwal dari tretment pada pestcontrol

class PelayananController extends BaseApiController
{
    /**
     * @var Manager
     */
    private $fractal;
    /**
     * @var PelayananTransformer
     */
    private $pelayananTransformer;

    function __construct(Manager $fractal, PelayananTransformer $pTransformer)
    {
        $this->fractal = $fractal;
        $this->pelayananTransformer = $pTransformer;

    }

    public function getPelayanan(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'perusahaan_id'=>'required',
            'employee_id'=>'required',
            'jos_id'=>'required',
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors());
          }

          $perusahaanid = $request->perusahaan_id;
          $employeeid = $request->employee_id;
          $josid = $request->jos_id;

          /*
          SELECT jp.id, jp.waktu_mulai , jp.waktu_selesai , j.no_jos
            from pestcontrol.jos_pelayanan jp 
            left join sales.jos j on (j.id = jp.jos_id)
          */
        $resource = JosPelayanan::leftJoin('sales.jos as sj', function($joinJ){
            $joinJ->On('pestcontrol.jos_pelayanan.jos_id','=','sj.id');
        })
        ->leftJoin('sales.jos_man_power_detil as jmpd', function($joinJMPD){
            $joinJMPD->On('jmpd.jos_id','=','sj.id');
        })
        ->select(
            'pestcontrol.jos_pelayanan.id',
            'pestcontrol.jos_pelayanan.waktu_mulai',
            'pestcontrol.jos_pelayanan.waktu_selesai',
            'sj.no_jos',
            'sj.id as jos_id'
        )
        ->where('sj.perusahaan_id', $perusahaanid)
        ->where('jmpd.pegawai_id', $employeeid)
        ->where('sj.id', $josid)
        ->get();
        $pelayanan = new Collection($resource,$this->pelayananTransformer);
        $pelayanan = $this->fractal->createData($pelayanan)->toArray();;
        return $this->respond($pelayanan);
    }
}