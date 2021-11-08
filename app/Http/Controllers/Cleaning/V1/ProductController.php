<?php
namespace App\Http\Controllers\Cleaning\V1;

use Auth;
use Illuminate\Http\Request;
use DB;
use Validator;
use Illuminate\Support\Facades\Mail;

use App\Mail\ProductOffer;

use App\Http\Controllers\Base\V1\BaseApiController;

use App\Models\Cleaning\V1\ProdukLayanan;

use App\Transformers\Cleaning\V1\ProdukLayananTransformer;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class ProductController extends BaseApiController
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var ProdukLayananTransformer
     */
    private $produkTransformer;

    /**
     * Create a new ScheduleTreatmentController instance.
     *
     * @return void
     */
    public function __construct(Manager $fractal, ProdukLayananTransformer $st)
    {
        $this->fractal = $fractal;
        $this->produkTransformer = $st;
    }

    /**
     * Get SPP Information by Id.
     * @param SppId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProduct()
    {
        /*
        select pl.id, pl.nama, pl.deskripsi, pl.gambar,
        pl.narahubung, pl.telp, pl.email
        from pestcontrol.produk_layanan pl
        */
        $resource = ProdukLayanan::select(
                        'produk_layanan.id','produk_layanan.nama',
                        'produk_layanan.deskripsi',
                        'produk_layanan.gambar',
                        'produk_layanan.narahubung', 'produk_layanan.telp',
                        'produk_layanan.email',
                        'll.nama as layanan'
                    )
                    ->leftJoin('layanan.layanan as ll', function($join){
                        $join->On('produk_layanan.jenis_layanan','=','ll.id');
                    })
                    ->get();
        $products = new Collection($resource, $this->produkTransformer);
        $products = $this->fractal->createData($products)->toArray(); // Transform data

        return $this->respond($products);
    }

    public function sendMeOffer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'produk_id'=>'required',
            'email'=>'required|string',
            'name'=>'required|string',
        ]);

        if ($validator->fails()) {
          return response()->json($validator->errors());
        }

        $email = $request->email;
        $name = $request->name;
        try {
            $details = [
                'name' => $name,
                'email' => $email
            ];

            Mail::to($email)
                ->send(new ProductOffer($details));

        } catch (\Exception $e) {
            //Return with error
            $error_message = $e->getMessage();
            return response()->json(['success' => 0, 'message' => $error_message], 401);
        }

        return response()->json([
            'success' => 1,
            'message'=> 'Kami telah mengirimkan penawaran kepada Anda! Silahkan cek email Anda'
        ], 200);

    }

}
