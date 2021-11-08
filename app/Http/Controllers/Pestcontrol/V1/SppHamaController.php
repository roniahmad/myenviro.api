<?php
namespace App\Http\Controllers\Pestcontrol\V1;

use Auth;
use Illuminate\Http\Request;
use DB;

use App\Http\Controllers\Base\V1\BaseApiController;

use App\Models\Pestcontrol\V1\SppHama;

use App\Transformers\Pestcontrol\V1\SppHamaTransformer;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class SppHamaController extends BaseApiController
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var SppHamaTransformer
     */
    private $sppHamaTransformer;

    /**
     * Create a new ScheduleTreatmentController instance.
     *
     * @return void
     */
    public function __construct(Manager $fractal, SppHamaTransformer $st)
    {
        $this->middleware('auth:api', ['except' => ['login']]);
        $this->fractal = $fractal;
        $this->sppHamaTransformer = $st;
    }

    /**
     * Get SPP Hama Information by Id.
     * @param SppId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHamaBySppId(Request $request)
    {

        /*
        select psa.id, psa.jenis_hama , kap.deskripsi
        from pestcontrol.spp_hama psa
        left join pestcontrol.spp ps on (ps.id=psa.spp_id)
        left join pestcontrol.jenis_hama kap on (kap.id=psa.jenis_hama)
        where psa.spp_id =1
        */

        $spp_id = $request->id;
        $resource = SppHama::leftJoin('pestcontrol.spp as ps', function($joinPSP){
                        $joinPSP->on('ps.id','=','spp_hama.spp_id');
                    })
                    ->leftJoin('pestcontrol.jenis_hama as kap', function($joinPS){
                        $joinPS->on('kap.id','=','spp_hama.jenis_hama');
                    })
                    ->select(
                        'spp_hama.id',
                        'spp_hama.jenis_hama',
                        'kap.deskripsi')
                    ->where('spp_hama.spp_id', $spp_id)
                    ->first();

        $hama = new Item($resource, $this->sppHamaTransformer);
        $hama = $this->fractal->createData($hama)->toArray(); // Transform data

        return $this->respond($hama);
    }

}
