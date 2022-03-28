<?php

namespace App\Http\Controllers\PestControl\V1;

use Auth;
use Illuminate\Http\Request;
use DB;
use Validator;
use Carbon\Carbon;
use Config;

use App\Http\Controllers\Base\V1\BaseApiController;

use App\Models\Pestcontrol\V1\JosSts;
use App\Models\Pestcontrol\V1\JosStsDetil;

use App\Transformers\Pestcontrol\V1\StsTransformer;
use App\Transformers\Pestcontrol\V1\StsDetilTransformer;
use App\Transformers\Pestcontrol\V1\BahanAktifTransformer;



use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;



class StsController extends BaseApiController
{
    /**
     * @var Manager
     */
    private $fractal;
    /**
     * @var StsTransformer
     */
    private $stsTransformer;
    /**
     * @var StsDetilTransformer
     */
    private $stsdetilTransformer;
    /**
     * @var BahanAktifTransformer
     */
    private $bahanTransformer;

    function __construct(Manager $fractal, StsTransformer $sTransformer, StsDetilTransformer $sdTransformer, BahanAktifTransformer $baTransformer)
    {
        $this->fractal = $fractal;
        $this->stsTransformer = $sTransformer;
        $this->stsdetilTransformer = $sdTransformer;
        $this->bahanTransformer = $baTransformer;
    }

    public function getSTS(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'perusahaan_id'=>'required',
            'employee_id'=>'required',
            'klien_id'=>'required',
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors());
          }

          $perusahaanid = $request->perusahaan_id;
          $employeeid = $request->employee_id;
          $klienid = $request->klien_id;

    /*
    use pestcontrol:

    SELECT js.id, sj.no_jos, mk.nama as klient, js.slip_number ,
	   DATE(js.tanggal_mulai),
	   DATE(js.tanggal_selesai),
	   pl.nama as pelayanan
	   from pestcontrol.jos_sts js 
	   left join master.klien mk on (mk.id = js.klien_id)
	   left join pestcontrol.jos_pelayanan jp on (jp.id = js.pelayanan_id)
	   left join sales.jos sj on (sj.id = jp.jos_id)
	   left join layanan.produk_layanan pl on (pl.id = sj.produk_id)
	   where sj.perusahaan_id = 'e9b02594-6b45-46ce-bd8c-596019a6d5f8'
       */

       $resource = JosSts::leftJoin('master.klien as mk', function($joinMK){
                $joinMK->On('pestcontrol.jos_sts.klien_id','=','mk.id');
            })
            ->leftJoin('pestcontrol.jos_pelayanan as jp', function($joinJP){
                $joinJP->On('pestcontrol.jos_sts.pelayanan_id','=','jp.id');
            })
            ->leftJoin('sales.jos as sj', function($joinSJ){
                $joinSJ->On('jp.jos_id','=','sj.id');
            })
            ->leftJoin('layanan.produk_layanan as pl', function($joinPL){
                $joinPL->On('sj.produk_id','=','pl.id');
            })
            ->leftJoin('sales.jos_man_power_detil as jmpd', function($joinJMPD){
                $joinJMPD->On('jmpd.jos_id','=','sj.id');
            })
            ->select(
                'pestcontrol.jos_sts.id',
                'pestcontrol.jos_sts.pelayanan_id',
                'sj.no_jos',
                'mk.nama as klient',
                'pestcontrol.jos_sts.slip_number',
                DB::raw('DATE(pestcontrol.jos_sts.tanggal_mulai) as date_start'),
                DB::raw('DATE(pestcontrol.jos_sts.tanggal_selesai) as date_end'),
                'pl.nama as pelayanan'
            )
            ->where('sj.perusahaan_id', $perusahaanid)
            ->where('jmpd.pegawai_id', $employeeid)
            ->where('pestcontrol.jos_sts.klien_id', $klienid)
            ->get();
            $sts = new Collection($resource,$this->stsTransformer);
            $sts = $this->fractal->createData($sts)->toArray();; //Transform data
            return $this->respond($sts);
    }
       
    public function getSTSDetail(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'perusahaan_id'=>'required',
            'employee_id'=>'required',
            // 'sts_id' => 'required', 
            // 'jenis'=>'required',
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors());
          }

          $perusahaanid = $request->perusahaan_id;
          $employeeid = $request->employee_id;
        //   $stsid = $request->sts_id; 
        //   $jenis = $request->jenis;


        /*
        select jsd.id, kap.nama as area, rf. deskripsi as treatmen, jsd.remark,
            p.nama_produk as bahan_aktif, jsd.dosis, ds.nama as dosis_satuan,
            jsd.jumlah_pemakaian, jsd.keterangan, js.tanggal_mulai,js.tanggal_selesai,
            js.rekomendasi, mp.nama as petugas, js.feedback_klien, k.nama as klien
            from pestcontrol.jos_sts_detil jsd
            left join pestcontrol.jos_sts as js on js.id = jsd.sts_id
            left join pestcontrol.jos_pelayanan jp on jp.jos_id = js.pelayanan_id
            left join master.pegawai as mp on mp.id = js.pic_perusahaan
            left join sales.jos as jos on jos.id = jp.jos_id
            left join inventory.produk as p on p.id = jsd.bahan_aktif
            left join master.referensi as rf on rf.id = jsd.tipe_treatmen
            left join inventory.dosis as ds on ds.id = jsd.dosis
            left join pestcontrol.jos_area ja on jsd.jos_area_id = ja.id
            left join master.klien_area_pelayanan as kap on kap.id = ja.area_id
            left join master.klien as k on k.id = js.klien_id
            WHERE rf.jenis = 23

        */

        $resource = JosStsDetil::leftJoin('pestcontrol.jos_sts as js', function($joinJS){
            $joinJS->On('pestcontrol.jos_sts_detil.sts_id','=','js.id');
        })
        ->leftJoin('pestcontrol.jos_pelayanan as jp', function($joinJP){
            $joinJP->On('js.pelayanan_id','=','jp.jos_id');
        })
        ->leftJoin('sales.jos as sj', function($joinSJ){
            $joinSJ->On('jp.jos_id','=','sj.id');
        })
        ->leftJoin('sales.jos_man_power_detil as jmpd', function($joinJMPD){
            $joinJMPD->On('jmpd.jos_id','=','sj.id');
        })
        ->leftJoin('master.pegawai as mp', function($joinMP){
            $joinMP->On('js.pic_perusahaan','=','mp.id');
        })
        ->leftJoin('sales.jos as jos', function($joinJOS){
            $joinJOS->On('jp.jos_id','=','jos.id');
        })
        ->leftJoin('inventory.produk as p', function($joinP){
            $joinP->On('pestcontrol.jos_sts_detil.bahan_aktif','=','p.id');
        })
        ->leftJoin('master.referensi as rf', function($joinRF){
            $joinRF->On('pestcontrol.jos_sts_detil.tipe_treatmen','=','rf.id');
        })
        ->leftJoin('inventory.dosis as ds', function($joinDS){
            $joinDS->On('pestcontrol.jos_sts_detil.dosis','=','ds.id');
        })
        ->leftJoin('pestcontrol.jos_area as ja', function($joinJA){
            $joinJA->On('pestcontrol.jos_sts_detil.jos_area_id','=','ja.id');
        })
        ->leftJoin('master.klien_area_pelayanan as kap', function($joinKAP){
            $joinKAP->On('ja.area_id','=','kap.id');
        })
        ->leftJoin('master.klien as k', function($joinK){
            $joinK->On('js.klien_id','=','k.id');
        })
        ->select(
            'pestcontrol.jos_sts_detil.id',
            'pestcontrol.jos_sts_detil.sts_id',
            'kap.nama as area',
            'rf.deskripsi as treatmen',
            'pestcontrol.jos_sts_detil.remark',
            'p.nama_produk as bahan_aktif',
            'pestcontrol.jos_sts_detil.dosis',
            'ds.nama as dosis_satuan',
            'pestcontrol.jos_sts_detil.jumlah_pemakaian',
            'pestcontrol.jos_sts_detil.keterangan',
            'js.tanggal_mulai','js.tanggal_selesai',
            'js.rekomendasi',
            'mp.nama as petugas', 
            'js.feedback_klien', 
            'k.nama as klien'
        )
        ->where('rf.jenis','=','23')
        // ->where('pestcontrol.jos_sts_detil.sts_id', $stsid)
        ->where('sj.perusahaan_id', $perusahaanid)
        ->where('jmpd.pegawai_id', $employeeid)
        ->get();
        $stsdetail = new Collection($resource,$this->stsdetilTransformer);
        $stsdetail = $this->fractal->createData($stsdetail)->toArray();; 
        return $this->respond($stsdetail);
    }

    public function getBahanAktif(Request $request)
    {
        $validator = Validator::make($request->all(),[
            // 'id'=>'required',
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors());
          }

        //   $id = $request->id;

        $resource = JosSts::Join('jos_sts_detil as jsd', function($joinJSD){
            $joinJSD->On('jos_sts.id','=','jsd.sts_id');
        })
        ->Join('jos_pelayanan as jp', function($joinJP){
            $joinJP->On('jos_sts.pelayanan_id','=','jp.id');
        })  
        ->Join('sales.jos_material as jm', function($joinJM){
            $joinJM->On('jp.jos_id','=','jm.jos_id');
        })
        ->Join('inventory.produk as p', function($joinP){
            $joinP->On('jm.barang','=','p.id');
        })
        ->select(
            'p.id',
            'p.nama_produk',
            'jm.jos_id'
        )
        // ->where('jos_sts.id',$id)
        ->get();
        $bahanaktif = new Collection($resource, $this->bahanTransformer);
        $bahanaktif = $this->fractal->createData($bahanaktif)->toArray();; 
        return $this->respond($bahanaktif);
    }

    public function createSTSandDetail(Request $request)
    {
        $validator = Validator::make($request->all(),[
               //jos_sts
                'id' => 'required|integer',
                'pelayanan_id' => 'required|integer',
                // 'klien_id' => 'required',
                'slip_number' => 'required|max:6',
                'tanggal_mulai' => 'required|date_format:Y-m-d H:i:s',
                'tanggal_selesai' => 'required|date_format:Y-m-d H:i:s|after_or_equal:tanggal_mulai',
                'pic_perusahaan' => 'required',
                'pic_klien' => 'required',

                //jos_sts_detil
                'jos_area_id' => 'required',
                'tipe_treatmen' => 'required',
                'bahan_aktif' => 'required',
                'dosis' => 'required',
                'dosis_satuan' => 'required',
                'jumlah_pemakaian' => 'required',

            //

        ]);
        
        if ($validator->fails()){
            return response()->json([
                'success' => 0,
                'message' => $validator->messages()
            ],422);
        }     
        
        $id = $request->id;
        $pelayanan_id = $request->pelayanan_id;
        $klien_id = $request->klien_id;
        $slip_number = $request->slip_number;
        $tanggal_mulai = $request->tanggal_mulai;
        $tanggal_selesai = $request->tanggal_selesai;
        $pic_perusahaan = $request->pic_perusahaan;
        $pic_klien = $request->pic_klien;
        $rekomendasi = $request->rekomendasi;

        $jos_area_id = $request->jos_area_id;
        $tipe_treatmen = $request->tipe_treatmen;
        $remark = $request->remark;
        $bahan_aktif = $request->bahan_aktif;
        $dosis = $request->dosis;
        $dosis_satuan = $request->dosis_satuan;
        $jumlah_pemakaian =  $request->jumlah_pemakaian;
        $keterangan = $request->keterangan;

        //jika jos_sts belum ada
        $ldd = 0;
        if($id<=0){

            $avSts = JosSts::select('id')
                        ->where('pelayanan_id', $pelayanan_id)
                        ->where('klien_id', $klien_id)
                        ->where('slip_number', $slip_number)
                        ->first();

            $new_sts = 0;
            if($avSts){
                $new_sts = $avSts->id;
            }else{
                $st = new JosSts;
                $st->pelayanan_id     = $pelayanan_id;
                $st->klien_id         = $klien_id;
                $st->slip_number      = $slip_number;
                $st->tanggal_mulai    = $tanggal_mulai;
                $st->tanggal_selesai  = $tanggal_selesai;
                $st->pic_perusahaan   = $pic_perusahaan;
                $st->pic_klien        = $pic_klien;
                $st->rekomendasi      = $rekomendasi;
                $st->save();
                $new_sts = $st->id;
            }

            if($new_sts > 0){
                $ldd = new JosStsDetil;
                $ldd->sts_id            = $new_sts;
                $ldd->jos_area_id       = $jos_area_id;
                $ldd->tipe_treatmen     = $tipe_treatmen;
                $ldd->remark            = $remark;
                $ldd->bahan_aktif       = $bahan_aktif;
                $ldd->dosis             = $dosis;
                $ldd->dosis_satuan      = $dosis_satuan;
                $ldd->jumlah_pemakaian  = $jumlah_pemakaian;
                $ldd->keterangan        = $keterangan;
                $ldd->save();
            }
            
            
        }else{
                $ldd = new JosStsDetil;
                $ldd->sts_id            = $id;
                $ldd->jos_area_id       = $jos_area_id;
                $ldd->tipe_treatmen     = $tipe_treatmen;
                $ldd->remark            = $remark;
                $ldd->bahan_aktif       = $bahan_aktif;
                $ldd->dosis             = $dosis;
                $ldd->dosis_satuan      = $dosis_satuan;
                $ldd->jumlah_pemakaian  = $jumlah_pemakaian;
                $ldd->keterangan        = $keterangan;
                $ldd->save();
            }

            if($ldd){
                    return $this->respond([
                        'success' => 1,
                        'message'=> Config('constants.messages.pestcontrol_sts_added_ok')]
                    );
                } else{
                    return $this->respond([
                        'success' => 0,
                        'message'=> Config('constants.messages.pestcontrol_sts_added_fail')]
                    );
                }

    }

    public function createSTS(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'pelayanan_id' => 'required|integer',
            'klien_id' => 'required',
            'slip_number' => 'required|distinct|max:6',
            'tanggal_mulai' => 'required|date_format:Y-m-d H:i:s',
            'tanggal_selesai' => 'required|date_format:Y-m-d H:i:s|after_or_equal:tanggal_mulai',
            'pic_perusahaan' => 'required',
            'pic_klien' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json([
                'success' => 0,
                'message' => $validator->messages()
            ],422);
        } 

        $pelayanan_id = $request->pelayanan_id;
        $klienid = $request->klien_id;
        $slip_number = $request->slip_number;
        $tanggal_mulai = $request->tanggal_mulai;
        $tanggal_selesai = $request->tanggal_selesai;
        $pic_perusahaan = $request->pic_perusahaan;
        $pic_klien = $request->pic_klien;
        $rekomendasi = $request->rekomendasi;

        $check = JosSts::where([
            ['slip_number','=',$slip_number],
        ])->first();

        if($check)
        {
            return $this->respond([
                'success' => 0,
                'message'=> Config('constants.messages.pestcontrol_sts_slipnumber_fail')]
            );
        }
        else
        {
            $st = new JosSts;
            $st->pelayanan_id     = $pelayanan_id;
            $st->klien_id         = $klienid;
            $st->slip_number      = $slip_number;
            $st->tanggal_mulai    = $tanggal_mulai;
            $st->tanggal_selesai  = $tanggal_selesai;
            $st->pic_perusahaan   = $pic_perusahaan;
            $st->pic_klien        = $pic_klien;
            $st->rekomendasi      = $rekomendasi;
            $st->save();

            if($st){
                return $this->respond([
                    'success' => 1,
                    'message'=> Config('constants.messages.pestcontrol_sts_added_ok')]
                );
            } else{
                return $this->respond([
                    'success' => 0,
                    'message'=> Config('constants.messages.pestcontrol_sts_added_fail')]
                );
            }
        }
        

        

    }

    public function createSTSDetail (Request $request)
    {
        $validator = Validator::make($request->all(),[
            'sts_id' => 'required',
            'jos_area_id' => 'required',
            'tipe_treatmen' => 'required',
            'bahan_aktif' => 'required',
            'dosis' => 'required',
            'dosis_satuan' => 'required',
            'jumlah_pemakaian' => 'required',
        ]);

        if ($validator->fails()){
            return response()->json([
                'success' => 0,
                'message' => $validator->messages()
            ],422);
        }   

        $sts_id = $request->sts_id;
        $jos_area_id = $request->jos_area_id;
        $tipe_treatmen = $request->tipe_treatmen;
        $remark = $request->remark;
        $bahan_aktif = $request->bahan_aktif;
        $dosis = $request->dosis;
        $dosis_satuan = $request->dosis_satuan;
        $jumlah_pemakaian =  $request->jumlah_pemakaian;
        $keterangan = $request->keterangan;

        $ldd = new JosStsDetil;
        $ldd->sts_id            = $sts_id;
        $ldd->jos_area_id       = $jos_area_id;
        $ldd->tipe_treatmen     = $tipe_treatmen;
        $ldd->remark            = $remark;
        $ldd->bahan_aktif       = $bahan_aktif;
        $ldd->dosis             = $dosis;
        $ldd->dosis_satuan      = $dosis_satuan;
        $ldd->jumlah_pemakaian  = $jumlah_pemakaian;
        $ldd->keterangan        = $keterangan;
        $ldd->save();

        if($ldd){
            return $this->respond([
                'success' => 1,
                'message'=> Config('constants.messages.pestcontrol_sts_detail_added_ok')]
            );
        } else{
            return $this->respond([
                'success' => 0,
                'message'=> Config('constants.messages.pestcontrol_sts_detail_added_fail')]
            );
        }
        
    }

    
}
