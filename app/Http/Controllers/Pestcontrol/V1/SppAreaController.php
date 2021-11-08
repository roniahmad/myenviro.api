<?php
namespace App\Http\Controllers\Pestcontrol\V1;

use Auth;
use Illuminate\Http\Request;
use DB;

use App\Http\Controllers\Base\V1\BaseApiController;

use App\Models\Pestcontrol\V1\SppArea;

use App\Transformers\Pestcontrol\V1\SppAreaTransformer;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class SppAreaController extends BaseApiController
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var SppAreaTransformer
     */
    private $sppAreaTransformer;

    /**
     * Create a new ScheduleTreatmentController instance.
     *
     * @return void
     */
    public function __construct(Manager $fractal, SppAreaTransformer $st)
    {
        $this->middleware('auth:api', ['except' => ['login']]);
        $this->fractal = $fractal;
        $this->sppAreaTransformer = $st;
    }

    /**
     * Get SPP Area Information by Id.
     * @param SppId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAreaBySppId(Request $request)
    {

        /*
        select psa.id, psa.area_id , kap.nama, mr.deskripsi
        from pestcontrol.spp_area psa
        left join pestcontrol.spp ps on (ps.id=psa.spp_id)
        left join master.klien_area_pelayanan kap on (kap.id=psa.area_id)
        left join master.referensi mr on (mr.id=psa.io)
        where psa.spp_id =1 and mr.jenis=24
        */

        $spp_id = $request->id;
        $resource = SppArea::leftJoin('pestcontrol.spp as ps', function($joinPSP){
                        $joinPSP->on('ps.id','=','spp_area.spp_id');
                    })->leftJoin('master.klien_area_pelayanan as kap', function($joinPS){
                        $joinPS->on('kap.id','=','spp_area.area_id');
                    })->leftJoin('master.referensi as mr', function($joinMR){
                        $joinMR->on('mr.id','=','spp_area.io');
                    })->select(
                        'spp_area.id',
                        'spp_area.area_id',
                        'kap.nama',
                        'mr.deskripsi'
                    )->where('mr.jenis',24)
                    ->where('spp_area.id', $spp_id)->first();

        $spp = new Item($resource, $this->sppAreaTransformer);
        $spp = $this->fractal->createData($spp)->toArray(); // Transform data

        return $this->respond($spp);
    }

}
