<?php
namespace App\Http\Controllers\Pestcontrol\V1;

use Auth;
use Illuminate\Http\Request;
use Validator;
use DB;
use Config;

use App\Http\Controllers\Base\V1\BaseApiController;

use App\Models\Pestcontrol\V1\JosArea;

use App\Transformers\Pestcontrol\V1\JosAreaTransformer;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;


class JosAreaController extends BaseApiController
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var JosAreaTransformer
     */
    private $sppAreaTransformer;

    /**
     * Create a new ScheduleTreatmentController instance.
     *
     * @return void
     */
    public function __construct(Manager $fractal, JosAreaTransformer $st)
    {
        // $this->middleware('auth:api', ['except' => ['login']]);
        $this->fractal = $fractal;
        $this->sppAreaTransformer = $st;
    }

    /**
     * Get SPP Area Information by Id.
     * @param SppId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAreaByJosId(Request $request)
    {
        // $validator = Validator::make($request->all(),[
        //     'jos_id'=>'required',
        // ]);

        // if ($validator->fails()){
        //   return response()->json($validator->errors());
        // }

        /*
        select psa.id, psa.area_id , kap.nama, mr.deskripsi
        from cleaning.jos_area psa
        left join master.klien_area_pelayanan kap on (kap.id=psa.area_id)
        left join master.referensi mr on (mr.id=psa.io)
        where psa.jos_id =25 and mr.jenis=24
        */

        $jenis_inside_outside = Config('constants.referensi.jenis_inside_outside');
        $josId = $request->jos_id;
        $resource = JosArea::leftJoin('master.klien_area_pelayanan as kap', function($joinPS){
                        $joinPS->on('kap.id','=','jos_area.area_id');
                    })->leftJoin('master.referensi as mr', function($joinMR){
                        $joinMR->on('mr.id','=','jos_area.io');
                    })->select(
                        'jos_area.id',
                        'jos_area.area_id',
                        'kap.nama',
                        'mr.deskripsi'
                    )
                    ->where('mr.jenis',$jenis_inside_outside)
                    // ->where('jos_area.jos_id', $josId)
                    ->get();

        $spp = new Collection($resource, $this->sppAreaTransformer);
        $spp = $this->fractal->createData($spp)->toArray(); // Transform data

        return $this->respond($spp);
    }

}