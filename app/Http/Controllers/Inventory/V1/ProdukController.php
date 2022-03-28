<?php
namespace App\Http\Controllers\Inventory\V1;

use Auth;
use Illuminate\Http\Request;
use DB;
use Validator;
use Carbon\Carbon;
use Config;

use App\Http\Controllers\Base\V1\BaseApiController;

use App\Models\Inventory\V1\Produk;

use App\Transformers\Inventory\V1\ProdukTransformer;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class ProdukController extends BaseApiController
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var ProdukTransformer
     */
    private $produkTransformer;

    /**
     * Create a new ScheduleTreatmentController instance.
     *
     * @return void
     */
    public function __construct(Manager $fractal, ProdukTransformer $pt)
    {
        $this->middleware('auth:api', ['except' => ['login']]);
        $this->fractal = $fractal;
        $this->produkTransformer = $pt;
    }

    public function getProdukByPerusahaanId(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'perusahaan_id'=>'required',
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors());
          }

        $perusahaanId = $request->perusahaan_id;
        /*
          select ip.id, ig.nama, ik.nama, ip.kode_produk, ip.nama_produk, s.nama, mp.nama
            from inventory.produk ip 
            left join inventory.gudang ig on (ig.id=ip.gudang_id)
            left join master.perusahaan mp on(mp.id = ig.perusahaan_id)
            left join inventory.satuan s on (s.id = ip.satuan)
            left join inventory.kategori ik on(ik.id=ip.kategori_produk) 
            where ip.kategori_produk = 1
        */

        $resource = Produk::leftJoin('inventory.gudang as ig', function($joinIG){
                        $joinIG->on('ig.id','=','produk.gudang_id');
                    })->leftJoin('master.perusahaan as mp', function($JoinMP){
                        $JoinMP->on('mp.id','=','ig.perusahaan_id');
                    })->leftJoin('inventory.kategori as ik', function($JoinIK){
                        $JoinIK->on('ik.id','=','produk.kategori_produk');
                    })->leftJoin('inventory.satuan as s', function($JoinS){
                        $JoinS->on('s.id','=','produk.satuan');
                    })->select(
                        'produk.id',
                        'ig.nama as gudang',
                        'produk.kode_produk',   
                        'produk.nama_produk',
                        's.nama as satuan',
                        'mp.nama as perusahaan'
                    )
                    ->where('produk.kategori_produk','=','3')
                    ->where('ig.perusahaan_id', $perusahaanId)
                    ->get();
                    $produk = new Collection($resource,$this->produkTransformer);
                    $produk = $this->fractal->createData($produk)->toArray();;
                    return $this->respond($produk);
    }

}
