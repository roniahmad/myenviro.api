<?php

namespace App\Http\Controllers\Layanan\V1;

use Illuminate\Http\Request;
use Config;
use DB;
use Validator;
use Illuminate\Support\Facades\Mail;

use App\Http\Controllers\Base\V1\BaseApiController;

use App\Models\Layanan\V1\Layanan;

use App\Transformers\Layanan\V1\LayananTransformer;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class LayananController extends BaseApiController
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var LayananTransformer
     */
    private $layananTransformer;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(Manager $fractal, LayananTransformer $st)
    {
        $this->middleware('auth:api', ['except' => ['getLayanan', 'getLayananDetail']]);
        $this->fractal = $fractal;
        $this->layananTransformer = $st;
    }

    /**
     * Get All Services
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLayanan()
    {
        /*
        select ll.id, ll.nama, ll.deskripsi, ll.jenis_layanan as id_jenis_layanan,
        mr.deskripsi as jenis_layanan, ll.gambar
        from layanan.layanan ll
        left join master.referensi mr on (ll.jenis_layanan=mr.id)
        where mr.jenis =29
        */
        $jenis_referensi = Config('constants.referensi.jenis_layanan_enviro');
        $resource = Layanan::select(
                        'layanan.layanan.id',
                        'layanan.layanan.nama',
                        DB::raw("CONCAT(LEFT(layanan.layanan.deskripsi, 150),'...') as deskripsi"),
                        'layanan.layanan.jenis_layanan as id_jenis_layanan',
                        'mr.deskripsi as jenis_layanan',
                        'layanan.layanan.gambar'
                    )
                    ->leftJoin('master.referensi as mr', function($join){
                        $join->On('layanan.layanan.jenis_layanan','=','mr.id');
                    })
                    ->where('mr.jenis',$jenis_referensi)
                    ->where('layanan.status', 1)
                    ->get();

        $services = new Collection($resource, $this->layananTransformer);
        $services = $this->fractal->createData($services)->toArray(); // Transform data

        return $this->respond($services);

    }

    public function getLayananDetail(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id'=>'required',
        ]);

        if ($validator->fails()){
          return response()->json($validator->errors());
        }

        $id = $request->id;

        $jenis_referensi = Config('constants.referensi.jenis_layanan_enviro');
        $resource = Layanan::select(
                        'layanan.layanan.id',
                        'layanan.layanan.nama',
                        'layanan.layanan.deskripsi as deskripsi',
                        'layanan.layanan.jenis_layanan as id_jenis_layanan',
                        'mr.deskripsi as jenis_layanan',
                        'layanan.layanan.gambar'
                    )
                    ->leftJoin('master.referensi as mr', function($join){
                        $join->On('layanan.layanan.jenis_layanan','=','mr.id');
                    })
                    ->where('mr.jenis',$jenis_referensi)
                    ->where('layanan.id', $id)
                    ->get();

        $services = new Collection($resource, $this->layananTransformer);
        $services = $this->fractal->createData($services)->toArray(); // Transform data

        return $this->respond($services);
    }

}
