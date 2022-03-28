<?php
namespace App\Http\Controllers\Pestcontrol\V1;

use Auth;
use Illuminate\Http\Request;
use Validator;
use DB;
use Config;

use Carbon\Carbon;

use App\Http\Controllers\Base\V1\BaseApiController;

use App\Models\Pestcontrol\V1\JosMonitoringHama;
use App\Models\Pestcontrol\V1\JosInstallation;
use App\Models\Pestcontrol\V1\JenisHama;
use App\Models\Pestcontrol\V1\Hama;
use App\Models\Sales\V1\JosPenerimaan;

use App\Transformers\Pestcontrol\V1\MonitoringHamaTransformer;
use App\Transformers\Pestcontrol\V1\InstallationTransformer;
use App\Transformers\Pestcontrol\V1\JosPenerimaanTransformer;
use App\Transformers\Pestcontrol\V1\TanggalMonitoringTransformer;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class JosMonitoringHamaController extends BaseApiController
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var MonitoringHamaTransformer
     */
    private $mhTransformer;

     /**
     * @var InstallationTransformer
     */
    private $ilTransformer;

     /**
     * @var JosPenerimaanTransformer
     */
    private $jpTransformer;

    /**
     * @var TanggalMonitoringTransformer
     */
    private $tmTransformer;
    
    /**
     * Get SPP Area Information by Id.
     * @param SppId
     * @return \Illuminate\Http\JsonResponse
     */

    function __construct(Manager $fractal, MonitoringHamaTransformer $mTransformer, InstallationTransformer $iTransformer, JosPenerimaanTransformer $jTransformer, TanggalMonitoringTransformer $tTransformer)
    {
        $this->fractal = $fractal;
        $this->mhTransformer = $mTransformer;
        $this->ilTransformer = $iTransformer;
        $this->jpTransformer = $jTransformer;
        $this->tmTransformer = $tTransformer;
    }

    public function getMonitoring(Request $request){
        $validator = Validator::make($request->all(),[
            'tanggal_monitoring'=>'required',
            'no_unit'=>'required',
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors());
          }
        
        $tanggal_monitoring = $request->tanggal_monitoring;
        $no_unit = $request->no_unit;
        // SELECT jmh.id, jmh.jos_installation_id , jmh.jenis_hama, jh.deskripsi as desk_jenis_hama,
        // h.deskripsi as nama_hama, jmh.jumlah
        // FROM pestcontrol.jenis_hama jh 
        // left Join pestcontrol.hama as h on (h.jenis = jh.id)
        // left join pestcontrol.jos_monitoring_hama as jmh on (jmh.jenis_hama = h.jenis AND jmh.hama = h.id)


        $resource = JenisHama::leftJoin('hama as h', function($joinH){
            $joinH->On('jenis_hama.id','=','h.jenis');
        })
        ->leftJoin('jos_monitoring_hama as jmh', function($joinJMH){
            $joinJMH->On('jmh.jenis_hama','=','h.jenis');
            $joinJMH->On('jmh.hama','=','h.id');
        })->leftJoin('jos_installation as ji', function($joinJI){
            $joinJI->On('jmh.jos_installation_id','=','ji.id');
        })
        ->select(
            'jmh.id',
            'jmh.jos_installation_id',
            'jenis_hama.deskripsi as jenis_hama',
            'h.deskripsi as hama',
            'jmh.jumlah',
            'jmh.tanggal_monitoring'
        )->where('jmh.tanggal_monitoring', $tanggal_monitoring)
        ->where('ji.no_unit', $no_unit)
        ->get();
        $monitoring = new Collection($resource,$this->mhTransformer);
        $monitoring = $this->fractal->createData($monitoring)->toArray();;
        return $this->respond($monitoring);
    }

    public function getTanggalMonitoring(Request $request)
    {
        // $validator = Validator::make($request->all(),[
        //     'jos_installation_id'=>'required',
        // ]);

        // if ($validator->fails()){
        //     return response()->json($validator->errors());
        //   }
        // $jos_installation_id = $request->jos_installation_id;
        $resource = JosMonitoringHama::leftJoin('jos_installation as ji', function($joinJI){
            $joinJI->On('jos_monitoring_hama.jos_installation_id','=','ji.id');
        })->select(
            DB::raw('MIN(jos_monitoring_hama.id) as id'),
            'jos_monitoring_hama.jos_installation_id',
            'ji.no_unit',
            // 'jos_monitoring_hama.tanggal_monitoring',
            DB::raw('DATE(jos_monitoring_hama.tanggal_monitoring) as date_monitoring')
        )
        // ->where('jos_installation_id', $jos_installation_id)
        ->groupBy('tanggal_monitoring', 'jos_installation_id')
        ->get();
        $tglmonitoring = new Collection($resource,$this->tmTransformer);
        $tglmonitoring = $this->fractal->createData($tglmonitoring)->toArray();;
        return $this->respond($tglmonitoring);

    }

    public function getTanggalMonitoringDetail(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'tanggal_monitoring'=>'required',
            'no_unit'=>'required',
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors());
          }
        
        $tanggal_monitoring = $request->tanggal_monitoring;
        $no_unit = $request->no_unit;
        // $validator = Validator::make($request->all(),[
        //     'jos_installation_id'=>'required',
        // ]);

        // if ($validator->fails()){
        //     return response()->json($validator->errors());
        //   }
        // $jos_installation_id = $request->jos_installation_id;
        $resource = JosMonitoringHama::leftJoin('jos_installation as ji', function($joinJI){
            $joinJI->On('jos_monitoring_hama.jos_installation_id','=','ji.id');
        })
        ->leftJoin('sales.jos_penerimaan as jp', function($joinJP){
            $joinJP->On('ji.no_unit','=','jp.id');
        })->leftJoin('master.klien_area_pelayanan as kap', function($joinKAP){
            $joinKAP->On('ji.area_id','=','kap.id');
        })
        ->select(
            DB::raw('MIN(jos_monitoring_hama.id) as id'),
            'jos_monitoring_hama.jos_installation_id',
            // 'jos_monitoring_hama.tanggal_monitoring',
            'ji.no_unit',
            'kap.nama as area',
            DB::raw('DATE(jos_monitoring_hama.tanggal_monitoring) as date_monitoring'),
            'jp.nomor_registrasi',
        )
        ->where('jos_monitoring_hama.tanggal_monitoring', $tanggal_monitoring)
        ->where('ji.no_unit', $no_unit)
        ->groupBy('tanggal_monitoring', 'jos_installation_id')
        ->get();
        $tglmonitoring = new Collection($resource,$this->tmTransformer);
        $tglmonitoring = $this->fractal->createData($tglmonitoring)->toArray();;
        return $this->respond($tglmonitoring);

    }
    
    public function getMonitoringById(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id'=>'required',
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors());
          }
        
        $id = $request->id;

        // SELECT jmh.id, jmh.jos_installation_id , jmh.jenis_hama, jh.deskripsi as desk_jenis_hama,
        // h.deskripsi as nama_hama, jmh.jumlah
        // FROM pestcontrol.jenis_hama jh 
        // left Join pestcontrol.hama as h on (h.jenis = jh.id)
        // left join pestcontrol.jos_monitoring_hama as jmh on (jmh.jenis_hama = h.jenis AND jmh.hama = h.id)


        $resource = JenisHama::leftJoin('hama as h', function($joinH){
            $joinH->On('jenis_hama.id','=','h.jenis');
        })
        ->leftJoin('jos_monitoring_hama as jmh', function($joinJMH){
            $joinJMH->On('jmh.jenis_hama','=','h.jenis');
            $joinJMH->On('jmh.hama','=','h.id');
        })
        ->select(
            'jmh.id',
            'jmh.jos_installation_id',
            'jenis_hama.deskripsi as jenis_hama',
            'h.deskripsi as hama',
            'jmh.jumlah',
            'jmh.tanggal_monitoring',
        )
        ->where('jmh.id', $id)
        ->get();
        $monitoring = new Collection($resource,$this->mhTransformer);
        $monitoring = $this->fractal->createData($monitoring)->toArray();;
        return $this->respond($monitoring);
    }

    public function getInstallation(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'perusahaan_id'=>'required',
            'employee_id'=>'required',
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors());
          }
        
        $perusahaanid = $request->perusahaan_id;
        $employeeid = $request->employee_id;
        
        /*
        use pestcontrol:

        -----------------------------------------------------------------
        select ji.id , ji.tanggal_instalasi , ji.tube_change , ji.glue_change 
        , jp.nomor_registrasi , kap.nama as NamaKap , k.nama as NamaKlien 
        , jp.id as no_unit , kap.id as area_id , ji.maintenance_number 
        , k.telepon as TlpnKlien , jmh.tanggal_monitoring  
        from pestcontrol.jos_installation ji
        left join master.klien_area_pelayanan as kap on kap.id = ji.area_id
        left join master.klien as k on (k.id = kap.klien_id)
        left join pestcontrol.jos_monitoring_hama as jmh on (jmh.jos_installation_id = ji.id)
        left join sales.jos_penerimaan as jp on (jp.id = ji.no_unit) 
        where ji.id = 1
        ------------------------------------------------------------------
        select ji.id , kap.nama , ji.no_unit ,ji.tanggal_instalasi , ji.maintenance_number  
        from pestcontrol.jos_installation ji
        left join master.klien_area_pelayanan as kap on kap.id = ji.area_id
        */
        // leftJoin('jos_monitoring_hama as jm', function($joinJM){
        //     $joinJM->On('jos_installation.id','=','jm.jos_installation_id');
        // })
        $resource = JosInstallation::leftJoin('master.klien_area_pelayanan as kap', function($joinKAP){
            $joinKAP->On('jos_installation.area_id','=','kap.id');
        })
        ->leftJoin('jos_area as ja', function($joinJA){
            $joinJA->On('kap.id','=','ja.area_id');
        })
        ->leftJoin('sales.jos as j', function($joinJ){
            $joinJ->On('ja.jos_id','=','j.id');
        })
        ->leftJoin('master.klien as k', function($joinK){
            $joinK->On('j.klien_id','=','k.id');
        })
        ->leftJoin('sales.jos_man_power_detil as jmpd', function($joinJMPD){
            $joinJMPD->On('jmpd.jos_id','=','j.id');
        })
        ->leftJoin('sales.jos_penerimaan as jp', function($joinJP){
            $joinJP->On('jp.id','=','jos_installation.no_unit');
        })
        // ->leftJoin('jos_monitoring_hama as jmh', function($joinJMH){
        //     $joinJMH->On('jos_installation.id','=','jmh.jos_installation_id');
        // })
        // ->leftJoin('hama as h', function($joinH){
        //     $joinH->On('jmh.jenis_hama','=','h.jenis');
        //     $joinH->On('jmh.hama','=','h.id');
        // })
        
        ->select(
            'jos_installation.id',
            'j.no_jos',
            'k.nama as klien',
            'k.telepon',
            'kap.nama as area',
            'jos_installation.no_unit',
            'jp.nomor_registrasi',
            // 'jos_installation.tanggal_instalasi',
             DB::raw('DATE(jos_installation.tanggal_instalasi) as date_instalasi'),
            // 'jm.tanggal_monitoring',
            'jos_installation.maintenance_number',
            'jos_installation.tube_change',
            'jos_installation.glue_change',
            // DB::raw('DATE(jmh.tanggal_monitoring) as date_monitoring'),

        )
        ->where('j.perusahaan_id', $perusahaanid)
        ->where('jmpd.pegawai_id', $employeeid)
        ->get();
        $installation = new Collection($resource,$this->ilTransformer);
        $installation = $this->fractal->createData($installation)->toArray();;
        return $this->respond($installation);

    }

    public function getNoUnit(Request $request)
    {
        // $searchParams = $request->all();
        // $clause = [
        //     'jos_penerimaan.nomor_registrasi' => $searchParams['no_registrasi'],
        // ];
        // $fields = array_keys($clause);
        // $index = 0;
        $jenis_merk_dagang = Config('constants.referensi.jenis_merk_dagang');
        $jenis_status_kondisi_barang = Config('constants.referensi.jenis_status_kondisi_barang');
        $resource = JosPenerimaan::leftJoin('pestcontrol.jos_installation as ji', function($joinJI){
                        $joinJI->on('ji.no_unit','=','jos_penerimaan.id');
                    })->leftJoin('inventory.produk as ip', function($joinIP){
                        $joinIP->on('ip.id','=','jos_penerimaan.barang');
                    })->leftJoin('master.referensi as mrk', function($joinMRK){
                        $joinMRK->on('mrk.id','=','jos_penerimaan.merk');
                    })->leftJoin('master.referensi as tus', function($joinTUS){
                        $joinTUS->on('tus.id','=','jos_penerimaan.status');
                    })->select(
                        'jos_penerimaan.id',
                        'jos_penerimaan.nomor_registrasi',
                        'ip.nama_produk as barang',
                        'jos_penerimaan.masa_depresiasi',
                        'mrk.deskripsi as merk',
                        'tus.deskripsi as status'
                    )
                    // ->where('ji.no_unit','=',null)
                    ->where('mrk.jenis',$jenis_merk_dagang)
                    ->where('tus.jenis',$jenis_status_kondisi_barang)
                    ->get();

        $unit = new Collection($resource, $this->jpTransformer);
        $unit = $this->fractal->createData($unit)->toArray();

        return $this->respond($unit);
        
    }
    
    // public function createMonitoringHama (Request $request)
    // {
    //     $carbon = Carbon::now();
    //     $tanggal = $carbon->format('Y-m-d h:i:s');
        
    //     $hama = Hama::query()->select('jenis','id','deskripsi','status')->get();
    //     foreach($hama as $hama)
    //     {
    //         $monitoring = JosMonitoringHama::create([
    //             'tanggal_monitoring' => $tanggal,
    //             'jos_installation_id' => $request['jos_installation_id'],
    //             'jenis_hama' => $hama['jenis'],
    //             'hama' => $hama['id'],
    //             'jumlah' => 0,
    //         ]); 
    //     }

    //         if($monitoring){
    //             return $this->respond([
    //                 'success' => 1,
    //                 'message'=> Config('constants.messages.pestcontrol_installation_added_ok')]
    //             );
    //         } else{
    //             return $this->respond([
    //                 'success' => 0,
    //                 'message'=> Config('constants.messages.pestcontrol_installation_added_fail')]
    //             );
    //         }
    // }

    // public function createMonitoringHama (Request $request)
    // {
    //     $validator = Validator::make($request->all(),[
    //         'tanggal_monitoring' => 'required|date_format:Y-m-d H:i:s',
    //         'jos_installation_id' => 'required|integer',
    //     ]);

    //     if ($validator->fails()){
    //         return response()->json([
    //             'success' => 0,
    //             'message' => $validator->messages()
    //         ],422);
    //     }

    //     $tanggal_monitoring = $request->tanggal_monitoring;
    //     $jos_installation_id = $request->jos_installation_id;

    //     $check = JosMonitoringHama::where([
    //         ['tanggal_monitoring','=',$tanggal_monitoring],
    //     ])->first();

    //     if($check)
    //     {
    //         return $this->respond([
    //             'success' => 0,
    //             'message'=> Config('constants.messages.pestcontrol_monitoring_tanggal_monitoring_fail')]
    //         );
    //     }
    //     else{

    //         $hama = Hama::query()->select('jenis','id','deskripsi','status')->get();
    //         foreach($hama as $hama)
    //         {
    //             $monitoring = JosMonitoringHama::create([
    //                 'tanggal_monitoring' => $tanggal_monitoring,
    //                 'jos_installation_id' => $jos_installation_id,
    //                 'jenis_hama' => $hama['jenis'],
    //                 'hama' => $hama['id'],
    //                 'jumlah' => 0,
    //             ]); 
    //         }

    //         if($monitoring){
    //             return $this->respond([
    //                 'success' => 1,
    //                 'message'=> Config('constants.messages.pestcontrol_installation_added_ok')]
    //             );
    //         } else{
    //             return $this->respond([
    //                 'success' => 0,
    //                 'message'=> Config('constants.messages.pestcontrol_installation_added_fail')]
    //             );
    //         }
    //     }
        
        
    // }
    
    public function createMonitoringHama (Request $request)
    {
        $validator = Validator::make($request->all(),[
            'tanggal_monitoring' => 'required|date_format:Y-m-d',
            'jos_installation_id' => 'required|integer',
        ]);

        if ($validator->fails()){
            return response()->json([
                'success' => 0,
                'message' => $validator->messages()
            ],422);
        }

        $tanggal_monitoring = $request->tanggal_monitoring;
        $jos_installation_id = $request->jos_installation_id;

        $check = JosMonitoringHama::where([
            ['tanggal_monitoring','=',$tanggal_monitoring],
            // [DB::raw('DATE(tanggal_monitoring)'),'=',$tanggal_monitoring],
        ])->where([
            ['jos_installation_id','=',$jos_installation_id],
            // [DB::raw('DATE(tanggal_monitoring)'),'=',$tanggal_monitoring],
        ])->first();

        if($check)
        {
            return $this->respond([
                'success' => 0,
                'message'=> Config('constants.messages.pestcontrol_monitoring_tanggal_monitoring_fail')]
            );
        }
        else{

            $hama = Hama::query()->select('jenis','id','deskripsi','status')->get();
            foreach($hama as $hama)
            {
                $monitoring = JosMonitoringHama::create([
                    'tanggal_monitoring' => $tanggal_monitoring,
                    'jos_installation_id' => $jos_installation_id,
                    'jenis_hama' => $hama['jenis'],
                    'hama' => $hama['id'],
                    'jumlah' => 0,
                ]); 
            }

            if($monitoring){
                return $this->respond([
                    'success' => 1,
                    'message'=> Config('constants.messages.pestcontrol_installation_added_ok')]
                );
            } else{
                return $this->respond([
                    'success' => 0,
                    'message'=> Config('constants.messages.pestcontrol_installation_added_fail')]
                );
            }
        }
        
    }


    public function createInstallationMonitoring(Request $request)
    {
        $validator = Validator::make($request->all(),[
            //jos_installation
            // 'id' => 'required|integer',
            'area_id' => 'required|integer',
            'no_unit' => 'required',
            'tanggal_instalasi' => 'required|date_format:Y-m-d H:i:s',
            'maintenance_number' => 'required|integer',

            //jos_monitoring
            'jenis_hama' => 'required|integer',
            'hama' => 'required|integer',
            'jumlah' => 'required|integer',
            'tanggal_monitoring' => 'required|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()){
            return response()->json([
                'success' => 0,
                'message' => $validator->messages()
            ],422);
        }
        $id = $request->id;
        $area_id = $request->area_id;
        $no_unit = $request->no_unit;
        $tanggal_instalasi = $request->tanggal_instalasi;
        $maintenance_number = $request->maintenance_number;
        $tube_change = $request->tube_change;
        $glue_change = $request->glue_change;

        $jenis_hama = $request->jenis_hama;
        $hama = $request->hama;
        $jumlah = $request->jumlah;
        $tanggal_monitoring = $request->tanggal_monitoring;

        $ldd = 0;
        if($id<=0){
            $avInstalasi = JosInstallation::select('id')
                                ->where('maintenance_number', $maintenance_number)
                                ->first();

            $new_instalasi = 0;
            if($avInstalasi){
                $new_instalasi = $avInstalasi->id;
            }else{
                $it = new JosInstallation;
                $it->area_id            = $area_id;
                $it->no_unit            = $no_unit;
                $it->tanggal_instalasi  = $tanggal_instalasi;
                $it->maintenance_number  = $maintenance_number;
                $it->tube_change        = $tube_change;
                $it->glue_change        = $glue_change;
                $it->save();
                $new_instalasi = $it->id;
            }

            if($new_instalasi > 0){
                $ldd = new JosMonitoringHama;
                $ldd->jos_installation_id    = $new_instalasi;
                $ldd->jenis_hama            = $jenis_hama;
                $ldd->hama                  = $hama;
                $ldd->jumlah                = $jumlah;
                $ldd->tanggal_monitoring    = $tanggal_monitoring;
                $ldd->save();
            }
            
        }else{
            $ldd = new JosMonitoringHama;
                $ldd->jos_installation_id    = $id;
                $ldd->jenis_hama            = $jenis_hama;
                $ldd->hama                  = $hama;
                $ldd->jumlah                = $jumlah;
                $ldd->tanggal_monitoring    = $tanggal_monitoring;
                $ldd->save();
        }

        if($ldd){
            return $this->respond([
                'success' => 1,
                'message'=> Config('constants.messages.pestcontrol_installation_added_ok')]
            );
        } else{
            return $this->respond([
                'success' => 0,
                'message'=> Config('constants.messages.pestcontrol_installation_added_fail')]
            );
        }

    }

    public function createInstallation(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'area_id' => 'required|integer',
            'no_unit' => 'required',
            'tanggal_instalasi' => 'required|date_format:Y-m-d H:i:s',
            // 'maintenance_number' => 'required|integer',
        ]);

        if ($validator->fails()){
            return response()->json([
                'success' => 0,
                'message' => $validator->messages()
            ],422);
        } 

        $area_id = $request->area_id;
        $no_unit = $request->no_unit;
        $tanggal_instalasi = $request->tanggal_instalasi;
        // $maintenance_number = $request->maintenance_number;
        $tube_change = $request->tube_change;
        $glue_change = $request->glue_change;

        $ldd = new JosInstallation;
        $ldd->area_id            = $area_id;
        $ldd->no_unit            = $no_unit;
        $ldd->tanggal_instalasi  = $tanggal_instalasi;
        // $ldd->maintenance_number  = $maintenance_number;
        $ldd->tube_change        = $tube_change;
        $ldd->glue_change        = $glue_change;
        $ldd->save();

        if($ldd){
            return $this->respond([
                'success' => 1,
                'message'=> Config('constants.messages.pestcontrol_installation_added_ok')]
            );
        } else{
            return $this->respond([
                'success' => 0,
                'message'=> Config('constants.messages.pestcontrol_installation_added_fail')]
            );
        }
    }

    public function createMonitoring(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'jos_installation_id' => 'required|integer',
            'jenis_hama' => 'required|integer',
            'hama' => 'required|integer',
            'jumlah' => 'required|integer',
            'tanggal_monitoring' => 'required|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()){
            return response()->json([
                'success' => 0,
                'message' => $validator->messages()
            ],422);
        } 

        $jos_installation_id = $request->jos_installation_id;
        $jenis_hama = $request->jenis_hama;
        $hama = $request->hama;
        $jumlah = $request->jumlah;
        $tanggal_monitoring = $request->tanggal_monitoring;
        
        $ldd = new JosMonitoringHama;
                $ldd->jos_installation_id    = $jos_installation_id;
                $ldd->jenis_hama            = $jenis_hama;
                $ldd->hama                  = $hama;
                $ldd->jumlah                = $jumlah;
                $ldd->tanggal_monitoring    = $tanggal_monitoring;
                $ldd->save();

                if($ldd){
                    return $this->respond([
                        'success' => 1,
                        'message'=> Config('constants.messages.pestcontrol_installation_added_ok')]
                    );
                } else{
                    return $this->respond([
                        'success' => 0,
                        'message'=> Config('constants.messages.pestcontrol_installation_added_fail')]
                    );
                }
    }

    public function updateHama(Request $request)
    {
        // dd($request);
        $validator = Validator::make($request->all(),[
            'jumlah' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json([
                'success' => 0,
                'message' => $validator->messages()
            ],422);
        } 
        $id = $request->id;
        $jumlah = $request->jumlah;

        $josMonitoringHama = JosMonitoringHama::find($id);
        $josMonitoringHama->jumlah = $jumlah;
        $josMonitoringHama->save();

        if($josMonitoringHama){
            return $this->respond([
                'success' => 1,
                'message'=> Config('constants.messages.pestcontrol_monitoring_update_ok')]
            );
        } else{
            return $this->respond([
                'success' => 0,
                'message'=> Config('constants.messages.pestcontrol_monitoring_update_fail')]
            );
        }

    }

    public function deleteHama(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id' => 'required',
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors());
        }

        $id = $request->id;

        $affectedRows = JosMonitoringHama::where('id', $id)->delete();

        if($affectedRows>0){
            return $this->respond([
                'success' => 1,
                'message'=> Config('constants.messages.pestcontrol_monitoring_delete_ok')]
            );
        }else{
            return $this->respond([
                'success' => 0,
                'message'=> Config('constants.messages.pestcontrol_monitoring_delete_fail')]
            );
        }
    }
        // public function getMonitoringHama(Request $request)

    // {
    //     $validator = Validator::make($request->all(),[
    //         // 'jenis' =>'required',
    //         'perusahaan_id'=>'required',
    //         'employee_id'=>'required',
    //     ]);

    //     if ($validator->fails()){
    //         return response()->json($validator->errors());
    //       }
        
    //     // $jenis = $request->jenis;
    //     $perusahaanid = $request->perusahaan_id;
    //     $employeeid = $request->employee_id;
        
    //     /*
    //     use pestcontrol:

    //     select jmh.id, j.no_jos, k.nama, k.telepon, jmh.jos_installation_id,
	// 	ji.maintenance_number as nomer_pemeliharaan, kap.nama, jh.deskripsi as jenis_hama, 
	// 	h.deskripsi as hama, jmh.jumlah, ji.tanggal_instalasi, jmh.tanggal_monitoring
    //     FROM pestcontrol.jos_monitoring_hama jmh
    //     left join jenis_hama as jh on jh.id = jmh.jenis_hama 
    //     left join jos_hama as jh2 on jh2.jenis_hama  = jh.id
    //     left join sales.jos as j on j.id = jh2.jos_id 
    //     left join master.klien as k on k.id = j.klien_id 
    //     left join hama as h on h.id = jmh.hama
    //     left join jos_installation as ji on ji.id = jmh.jos_installation_id
    //     left join master.klien_area_pelayanan as kap on kap.id = ji.area_id  
    //     WHERE h.jenis =1
    //     And j.id =1
    //     */

    //     $resource = JosMonitoringHama::leftJoin('jenis_hama as jh', function($joinJH){
    //         $joinJH->On('jos_monitoring_hama.jenis_hama','=','jh.id');
    //     })
    //     ->leftJoin('jos_hama as jh2', function($joinJH2){
    //         $joinJH2->On('jh.id','=','jh2.jenis_hama');
    //     })
    //     ->leftJoin('sales.jos as j', function($joinJ){
    //         $joinJ->On('jh2.jos_id','=','j.id');
    //     })
    //     ->leftJoin('sales.jos_man_power_detil as jmpd', function($joinJMPD){
    //         $joinJMPD->On('jmpd.jos_id','=','j.id');
    //     })
    //     // ->leftJoin('master.klien as k', function($joinK){
    //     //     $joinK->On('j.klien_id','=','k.id');
    //     // })
    //     ->leftJoin('hama as h', function($joinH){
    //         $joinH->On('jos_monitoring_hama.hama','=','h.id');
    //     })
    //     ->leftJoin('jos_installation as ji', function($joinJI){
    //         $joinJI->On('jos_monitoring_hama.jos_installation_id','=','ji.id');
    //     })
    //     // ->leftJoin('master.klien_area_pelayanan as kap', function($joinKAP){
    //     //     $joinKAP->On('ji.area_id','=','kap.id');
    //     // })
    //     ->select(
    //         'jos_monitoring_hama.id',
    //         'jos_monitoring_hama.jos_installation_id',
    //         // 'j.no_jos',
    //         // 'k.nama as klien',
    //         // 'k.telepon',
    //         // 'ji.no_unit',
    //         // 'ji.maintenance_number as nomer_pemeliharaan',
    //         // 'kap.nama as area',
    //         'jh.deskripsi as jenis_hama',
    //         'h.deskripsi as hama',
    //         'jos_monitoring_hama.jumlah',
    //         // DB::raw('DATE(ji.tanggal_instalasi) as date_installasi'),
    //         // DB::raw('DATE(jos_monitoring_hama.tanggal_monitoring) as date_monitoring')

    //     )
    //     // ->where('h.jenis', $jenis)
    //     ->where('j.perusahaan_id', $perusahaanid)
    //     ->where('jmpd.pegawai_id', $employeeid)
    //     ->get();
    //     $monitoring = new Collection($resource,$this->mhTransformer);
    //     $monitoring = $this->fractal->createData($monitoring)->toArray();;
    //     return $this->respond($monitoring);
    // }

    
}