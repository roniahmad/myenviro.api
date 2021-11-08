<?php
namespace App\Http\Controllers\Pestcontrol\V1;
use Auth;
use Illuminate\Http\Request;

use App\Http\Controllers\Base\V1\BaseApiController;
use App\Models\Pestcontrol\V1\SppPelayanan;
use App\Models\Pestcontrol\V1\SppPelayananPetugas;

use App\Traits\AccountTrait;

use App\Transformers\V1\MeTranformer;
use App\Transformers\Pestcontrol\V1\ScheduleTreatmentTransformer;
use App\Transformers\Pestcontrol\V1\TechniciansByScheduleTransformer;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class ScheduleTreatmentController extends BaseApiController
{
    use AccountTrait;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var MeTranformer
     */
    private $meTransformer;

    /**
     * @var ScheduleTreatmentTransformer
     */
    private $sttTransformer;

    /**
     * @var TechniciansByScheduleTransformer
     */
    private $tcnTransformer;


    /**
     * Create a new ScheduleTreatmentController instance.
     *
     * @return void
     */
    public function __construct(Manager $fractal, MeTranformer $mt, ScheduleTreatmentTransformer $stt,
        TechniciansByScheduleTransformer $tst)
    {
        $this->middleware('auth:api', ['except' => ['login']]);
        $this->fractal = $fractal;
        $this->meTransformer = $mt;
        $this->sttTransformer = $stt;
        $this->tcnTransformer = $tst;
    }

    /**
     * Get Scheduled Treatment.
     * @param Token
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSchedulesTreatment(Request $request)
    {
        //get current user logged in
        $email = Auth()->user()->email;
        $resource = $this->getLoggedUserInfo($email);

        $user = new Item($resource, $this->meTransformer);
        $user = $this->fractal->createData($user)->toArray(); // Transform data

        /*
        select psp.id, psp.spp_id,ps.no_spp, ps.produk_id, mr.deskripsi,
            psp.waktu_mulai, psp.waktu_selesai,
            ps.perusahaan_id
        from pestcontrol.spp_pelayanan psp
        left join pestcontrol.spp ps on (psp.spp_id=ps.id)
        left join master.referensi mr on (mr.id=ps.produk_id)
        where mr.jenis = 21 and ps.klien_id='9c9d2d41-8da5-42c2-81ac-5a5245913aa3'
        */

        $resource = array();
        if($user){
            foreach ($user as $key) {
                $org = $key['organization_id'];
            }
            $resource = SppPelayanan::leftJoin('pestcontrol.spp as ps', function($joinPS){
                            $joinPS->on('spp_pelayanan.spp_id','=','ps.id');
                        })->leftJoin('master.referensi as mr', function($joinMR){
                            $joinMR->on('mr.id','=','ps.produk_id');
                        })->select(
                            'spp_pelayanan.id', 'spp_pelayanan.spp_id','ps.no_spp', 'ps.produk_id', 'mr.deskripsi',
                            'spp_pelayanan.waktu_mulai', 'spp_pelayanan.waktu_selesai',
                            'ps.perusahaan_id'
                        )->where('mr.jenis',21)
                        ->where('ps.klien_id', $org)->get();

        }

        $schedules = new Collection($resource, $this->sttTransformer);
        $schedules = $this->fractal->createData($schedules)->toArray(); // Transform data

        return $this->respond($schedules);
    }

    /**
     * Get Scheduled Treatment.
     * @param SchedulesTreatmentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTechniciansBySchedulesTreatmentId(Request $request)
    {
        /*
        select pspp.pelayanan_id,
            mp.nip, mp.nama, mp.jenis_kelamin, mre.deskripsi, mp.avatar
        from pestcontrol.spp_pelayanan_petugas pspp
        left join pestcontrol.spp_pelayanan psp on (pspp.pelayanan_id=psp.id)
        left join pestcontrol.spp ps on (psp.spp_id=ps.id)
        left join master.pegawai mp on (mp.id=pspp.pegawai_id and mp.perusahaan=ps.perusahaan_id)
        left join master.referensi mre on (mre.id=mp.jenis_kelamin)
        where pspp.pelayanan_id =1 and mre.jenis=2
        */

        $schedule_id = $request->pelayanan_id;
        $resource = SppPelayananPetugas::leftJoin('pestcontrol.spp_pelayanan as psp', function($joinPSP){
                        $joinPSP->on('spp_pelayanan_petugas.pelayanan_id','=','psp.id');
                    })->leftJoin('pestcontrol.spp as ps', function($joinPS){
                        $joinPS->on('psp.spp_id','=','ps.id');
                    })->leftJoin('master.pegawai as mp', function($joinMP){
                        $joinMP->on('mp.id','=','spp_pelayanan_petugas.pegawai_id');
                        $joinMP->on('mp.perusahaan','=','ps.perusahaan_id');
                    })->leftJoin('master.referensi as mre', function($joinMRE){
                        $joinMRE->on('mre.id','=','mp.jenis_kelamin');
                    })->select(
                        'spp_pelayanan_petugas.pelayanan_id',
                        'mp.nip','mp.nama', 'mp.jenis_kelamin', 'mre.deskripsi',
                        'mp.avatar'
                    )->where('mre.jenis',2)
                    ->where('spp_pelayanan_petugas.pelayanan_id', $schedule_id)->get();

        $technicians = new Collection($resource, $this->tcnTransformer);
        $technicians = $this->fractal->createData($technicians)->toArray(); // Transform data

        return $this->respond($technicians);
    }
}
