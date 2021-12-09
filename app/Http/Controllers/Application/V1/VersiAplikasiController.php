<?php
namespace App\Http\Controllers\Application\V1;

use Auth;
use Illuminate\Http\Request;
use DB;
use Validator;
use Carbon\Carbon;

use App\Http\Controllers\Base\V1\BaseApiController;

use App\Models\Aplikasi\V1\VersiAplikasi;

use App\Traits\VersionTrait;

class VersiAplikasiController extends BaseApiController
{

    use VersionTrait;

    public function __construct()
    {
        //
    }

    /**
     * Get Apllication version
     * @param appName
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNewVersion(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'app_name'=>'required',
        ]);

        if ($validator->fails()){
          return response()->json($validator->errors());
        }

        $app_name = $request->app_name;

        $new_version = $this->getNewVersion($app_name);

        $ver = array(
            'new_version' => $new_version,
        );

        return $this->respond($ver);
    }

}
