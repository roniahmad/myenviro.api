<?php
namespace App\Http\Controllers\Master\V1;

use Auth;
use Illuminate\Http\Request;
use DB;
use Config;
use Validator;

use App\Http\Controllers\Base\V1\BaseApiController;

use App\Models\Master\V1\Referensi;

use App\Transformers\Master\V1\ReferensiTransformer;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class ReferensiController extends BaseApiController
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var ReferensiTransformer
     */
    private $referensiTransformer;

    /**
     * Create a new ScheduleTreatmentController instance.
     *
     * @return void
     */
    public function __construct(Manager $fractal, ReferensiTransformer $st)
    {
        $this->fractal = $fractal;
        $this->referensiTransformer = $st;
    }

    /**
     * Get SPP Information by Id.
     * @param SppId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReferensiCleaning()
    {
        $jenis = Config('constants.referensi.jenis_pekerjaan_cleaning');
        $resource = Referensi::select(
                        'id','deskripsi'
                    )
                    ->where('jenis', $jenis)
                    ->get();
        $products = new Collection($resource, $this->referensiTransformer);
        $products = $this->fractal->createData($products)->toArray(); // Transform data

        return $this->respond($products);
    }

    /**
     * Get
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReferensiHelpTopic()
    {
        $jenis = Config('constants.referensi.jenis_help_topic');
        $resource = Referensi::select(
                        'id','deskripsi'
                    )
                    ->where('jenis', $jenis)
                    ->get();
        $products = new Collection($resource, $this->referensiTransformer);
        $products = $this->fractal->createData($products)->toArray(); // Transform data

        return $this->respond($products);
    }


}
