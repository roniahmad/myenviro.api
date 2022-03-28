<?php
namespace App\Http\Controllers\Inventory\V1;

use Auth;
use Illuminate\Http\Request;
use DB;
use Validator;
use Carbon\Carbon;
use Config;

use App\Http\Controllers\Base\V1\BaseApiController;

use App\Models\Inventory\V1\Dosis;

use App\Transformers\Inventory\V1\DosisTransformer;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class DosisController extends BaseApiController
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var DosisTransformer
     */
    private $dosisTransformer;

    /**
     * Create a new ScheduleTreatmentController instance.
     *
     * @return void
     */
    public function __construct(Manager $fractal, DosisTransformer $dt)
    {
        $this->middleware('auth:api', ['except' => ['login']]);
        $this->fractal = $fractal;
        $this->dosisTransformer = $dt;
    }

    public function getDosis(Request $request)
    {
        $resource = Dosis::select(
            'dosis.id',
            'dosis.nama',
            'dosis.deskripsi',
            'status'
        )->get();

        $dosis = new Collection($resource,$this->dosisTransformer);
        $dosis = $this->fractal->createData($dosis)->toArray();;
        return $this->respond($dosis);
    }
}