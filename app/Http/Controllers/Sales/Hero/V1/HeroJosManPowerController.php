<?php
namespace App\Http\Controllers\Sales\Hero\V1;

use Auth;
use Illuminate\Http\Request;
use DB;
use Validator;
use Carbon\Carbon;
use Config;

use App\Http\Controllers\Base\V1\BaseApiController;

use App\Models\Sales\V1\JosManPowerDetil;

use App\Transformers\Jos\V1\JosMPDTransformer;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

/*
    Job Order Sheet
    Man Power
*/
class HeroJosManPowerController extends BaseApiController
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var JosMPDTransformer
     */
    private $josTransformer;

    /**
     * Create a new ScheduleTreatmentController instance.
     *
     * @return void
     */
    public function __construct(Manager $fractal, JosMPDTransformer $st)
    {
        $this->fractal = $fractal;
        $this->josTransformer = $st;
    }

    /**
     * Get Jos Man Power Detail.
     *
     * @return void
     */
    public function getJosMPDByJosId(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'jos_id'=>'required',
        ]);

        if ($validator->fails()){
          return response()->json($validator->errors());
        }

        $josid = $request->jos_id;
        /*
        SELECT jmpd.pegawai_id, mp.nip,
        CONCAT(IFNULL(CONCAT(mp.gelar_depan,' ') ,''), mp.nama, IFNULL(CONCAT(' ',mp.gelar_belakang) ,'')) as nama_pegawai,
        mr.deskripsi as jabatan, mp.status, mp.avatar
        from sales.jos_man_power_detil jmpd
        left join master.pegawai mp on (mp.id=jmpd.pegawai_id)
        LEFT join master.referensi mr on (mr.id=mp.jabatan)
        where jmpd.jos_id=1
        and mr.jenis =18;
        */

        $jenis_jabatan = Config('constants.referensi.jenis_jabatan');
        $resource = JosManPowerDetil::select(
                        'jos_man_power_detil.id',
                        'jos_man_power_detil.pegawai_id',
                        'mp.nip',
                        DB::raw("CONCAT(IFNULL(CONCAT(mp.gelar_depan,' '),''), mp.nama, IFNULL(CONCAT(' ',mp.gelar_belakang) ,'')) as nama_pegawai"),
                        DB::raw('mp.jabatan as jabatan_id'),
                        DB::raw('mr.deskripsi as jabatan'),
                        'mp.status', 'mp.avatar'
                    )
                    ->leftJoin('master.pegawai as mp', function($join){
                        $join->On('mp.id','=','jos_man_power_detil.pegawai_id');
                    })
                    ->leftJoin('master.referensi as mr', function($joinMR){
                        $joinMR->On('mr.id','=','mp.jabatan');
                    })
                    ->where('jos_man_power_detil.jos_id', $josid)
                    ->where('mr.jenis', $jenis_jabatan)
                    ->get();

        $jos = new Collection($resource, $this->josTransformer);
        $jos = $this->fractal->createData($jos)->toArray(); // Transform data

        return $this->respond($jos);

    }


}
