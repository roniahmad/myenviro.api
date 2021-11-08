<?php
namespace App\Http\Controllers\Pestcontrol\V1;

use Auth;
use Illuminate\Http\Request;
use DB;

use App\Http\Controllers\Base\V1\BaseApiController;

use App\Models\Pestcontrol\V1\Spp;

use App\Transformers\Pestcontrol\V1\SppTransformer;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class SppController extends BaseApiController
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var SppTransformer
     */
    private $sppTransformer;

    /**
     * Create a new ScheduleTreatmentController instance.
     *
     * @return void
     */
    public function __construct(Manager $fractal, SppTransformer $st)
    {
        $this->fractal = $fractal;
        $this->sppTransformer = $st;
    }

    /**
     * Get SPP Information by Id.
     * @param SppId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSppById(Request $request)
    {

        /*
        select ps.id, ps.perusahaan_id, mp.nama perusahaan_nama, mp.alamat perusahaan_alamat,
        mp.kabkota kabkota_perusahaan, mk.deskripsi as deskripsi_kabkota_perusahaan,
        mp.kodepos perusahaan_kodepos,
        ps.klien_id,mkl.nama as klien_nama, mkl.alamat as klien_alamat, mkl.kabkota, mkk.deskripsi deskripsi_kabkota_klien,
        mkl.kodepos klien_kodepos,
        ps.no_spp, ps.produk_id, mr.deskripsi as produk,
        ps.frekuensi_pekerjaan, ps.tanggal_spp,
        ps.tgl_awal, ps.tgl_akhir, ps.nilai_kontrak
        from pestcontrol.spp ps
        left join master.perusahaan mp on (mp.id=ps.perusahaan_id)
        left join master.klien mkl on (mkl.id=ps.klien_id)
        left join master.kabkota mk on (mk.kode=mp.kabkota)
        left join master.kabkota mkk on (mkk.kode=mkl.kabkota)
        left join master.referensi mr on (mr.id=ps.produk_id)
        where mr.jenis =21 and ps.id =1
        */

        $spp_id = $request->id;
        $resource = Spp::leftJoin('master.perusahaan as mp', function($joinPSP){
                        $joinPSP->on('mp.id','=','spp.perusahaan_id');
                    })->leftJoin('master.klien as mkl', function($joinPS){
                        $joinPS->on('mkl.id','=','spp.klien_id');
                    })->leftJoin('master.kabkota as mk', function($joinMP){
                        $joinMP->on('mk.kode','=','mp.kabkota');
                    })->leftJoin('master.kabkota as mkk', function($joinMRE){
                        $joinMRE->on('mkk.kode','=','mkl.kabkota');
                    })->leftJoin('master.referensi as mr', function($joinMR){
                        $joinMR->on('mr.id','=','spp.produk_id');
                    })->select(
                        'spp.perusahaan_id', DB::raw('mp.nama as perusahaan_nama'), DB::raw('mp.alamat as perusahaan_alamat'),
                        DB::raw('mp.kabkota as kabkota_perusahaan'), DB::raw('mk.deskripsi as deskripsi_kabkota_perusahaan'),
                        DB::raw('mp.kodepos as perusahaan_kodepos'),
                        'spp.klien_id',
                        DB::raw('mkl.nama as klien_nama'), DB::raw('mkl.alamat as klien_alamat'),
                        'mkl.kabkota', DB::raw('mkk.deskripsi as deskripsi_kabkota_klien'),
                        DB::raw('mkl.kodepos as klien_kodepos'),
                        'spp.no_spp', 'spp.produk_id', DB::raw('mr.deskripsi as produk'),
                        'spp.frekuensi_pekerjaan', 'spp.tanggal_spp',
                        'spp.tgl_awal', 'spp.tgl_akhir', 'spp.nilai_kontrak'
                    )->where('mr.jenis',21)
                    ->where('spp.id', $spp_id)->first();

        if($resource){
            $spp = new Item($resource, $this->sppTransformer);
            $spp = $this->fractal->createData($spp)->toArray(); // Transform data
            return $this->respond($spp);
        }

        return $this->respond($resource);
    }

    public function getSppByUser(Request $request)
    {

    }

}
