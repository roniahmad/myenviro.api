<?php

namespace App\Http\Controllers\Layanan\V1;

use Illuminate\Http\Request;
use Config;
use DB;
use Validator;
use Illuminate\Support\Facades\Mail;

use App\Http\Controllers\Base\V1\BaseApiController;

use App\Models\Layanan\V1\ProdukLayanan;

use App\Transformers\Layanan\V1\ProdukLayananTransformer;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class ProdukLayananController extends BaseApiController
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var ProdukLayananTransformer
     */
    private $layananTransformer;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(Manager $fractal, ProdukLayananTransformer $st)
    {
        $this->middleware('auth:api', ['except' => ['getProdukLayanan']]);
        $this->fractal = $fractal;
        $this->layananTransformer = $st;
    }

    /**
     * Get All Services
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProdukLayanan()
    {
        /*
        select pl.id, pl.jenis_layanan as id_jenis_layanan, pl.nama, pl.narahubung, pl.telp, pl.email,
        pl.deskripsi
        from layanan.produk_layanan pl
        left join layanan.layanan ll on (ll.id=pl.jenis_layanan)
        where ll.status=1 and pl.status =1
        */

        $resource = ProdukLayanan::select(
                        'produk_layanan.id',
                        DB::raw('produk_layanan.jenis_layanan as id_jenis_layanan'),
                        'produk_layanan.nama', 'produk_layanan.narahubung', 'produk_layanan.telp',
                        'produk_layanan.email',
                        'produk_layanan.deskripsi'
                    )
                    ->leftJoin('layanan.layanan as ll', function($join){
                        $join->On('ll.id','=','produk_layanan.jenis_layanan');
                    })
                    ->where('ll.status',1)
                    ->where('produk_layanan.status', 1)
                    ->get();

        $services = new Collection($resource, $this->layananTransformer);
        $services = $this->fractal->createData($services)->toArray(); // Transform data

        return $this->respond($services);

    }

}
