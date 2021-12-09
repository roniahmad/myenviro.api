<?php

namespace App\Http\Controllers\Account\V1;

use App\Http\Controllers\Base\V1\BaseApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

use JWTAuth;
use JWTAuthException;
use Lang;
use DB;

use App\Mail\ResetPassword;
use App\Mail\SignupEmail;

use App\User;

use App\Models\Master\V1\UserOrganisasi;

use App\Traits\UtilsTrait;
use App\Traits\AccountTrait;

use App\Transformers\V1\MeTranformer;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class ClientAuthController extends BaseApiController
{
    use UtilsTrait;
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
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(Manager $fractal, MeTranformer $mt)
    {
        $this->middleware('auth:api', ['except' => ['login', 'relogin', 'register', 'verify']]);
        $this->fractal = $fractal;
        $this->meTransformer = $mt;
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
     public function relogin(Request $request)
     {
         $credentials = $request->only('email', 'password');
         $rules = [
             'email' => 'required|string|email|max:255',
             'password' => 'required|min:6',
         ];

         $validator = Validator::make($credentials, $rules);

         if ($validator->fails()) {
             return response()->json([
                 'status' => 'error',
                 'message' => $validator->messages()
             ],422);
         }

         try {
             if (! $token = JWTAuth::attempt($credentials)) {
             	return $this->respondUnAuthorized();
             }
         } catch (JWTException $e) {
         	return $this->respondInternalError();
         }

         $resource = array(
             'success' => 1,
             'message' => 'tus',
             'access_token' => $token,
         );

         return $this->respond($resource);

     }


    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $rules = [
            'email' => 'required|string|email|max:255',
            'password' => 'required|min:6',
        ];

        $validator = Validator::make($credentials, $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->messages()
            ],422);
        }

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
            	return $this->respondUnAuthorized();
            }
        } catch (JWTException $e) {
        	return $this->respondInternalError();
        }

        $user = Auth()->user();

        /*
        select u.verified,u.email,
        uo.tipe_user, uo.organisasi_id,uo.pegawai_id,
        mp.nip, mp.nama, mp.gelar_depan, mp.gelar_belakang,
        mr.kode, mr.nama
        from aplikasi.users u
        left join master.user_organisasi uo on (u.email=uo.email)
        left join master.klien_pegawai mp on (uo.pegawai_id=mp.id)
        left join master.klien mr on (mr.id=uo.organisasi_id)
        where u.email ='roni.ahmad@myenviro.id'
        */
        $res = User::leftJoin('master.user_organisasi as uo', function($joinU){
                        $joinU->On('aplikasi.users.email','=','uo.email');
                    })
                    ->leftJoin('master.klien_pegawai as mp',function($joinMP){
                        $joinMP->On('uo.pegawai_id','=','mp.id');
                    })
                    ->leftJoin('master.klien as mr', function($joinMR){
                        $joinMR->On('mr.id','=','uo.organisasi_id');
                    })
                    ->select(
                        'aplikasi.users.verified','aplikasi.users.email',
                        'uo.tipe_user', 'uo.organisasi_id','uo.pegawai_id',
                        DB::raw("'' as nip"), 'mp.nama', 'mp.gelar_depan', 'mp.gelar_belakang',
                        'mr.kode', DB::raw('mr.nama as perusahaan')
                    )
                    ->where('aplikasi.users.email',$request->email)
                    ->first();

        $loggedHero = array(
            'verified'=>$res->verified,
            'username'=>$res->email,
            'tipe_user'=>$res->tipe_user,
            'pegawai_id' => $res->pegawai_id,
            'nip' => $res->nip,
            'nama'=> $res->nama,
            'gelar_depan' => $res->gelar_depan,
            'gelar_belakang' => $res->gelar_belakang,
        );

        $organization = array(
            'uuid' => $res->organisasi_id,
            'kode' => $res->kode,
            'perusahaan' => $res->perusahaan,
        );

        $mytoken = array(
            'token' => $token,
            'type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        );

        $config_base_url = Config('constants.config.base_url');

        $base_url = $this->getValueByConfig($config_base_url);
        
        return $this->respond([
            'success'  => 1,
            'message'  => 'Welcome to MyEnviro',
            'token'    => $mytoken,
            'hero'     => $loggedHero,
            'org'      => $organization,
            'base_url' => $base_url,
        ]);

    }

    /**
     * Log the user out (Invalionlinedate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $token = JWTAuth::getToken();
        try {
            JWTAuth::invalidate(JWTAuth::parseToken());
            // JWTAuth::invalidate($token);
            return $this->respond([
                'success' => 1,
                'message' => 'You have successfully logged out.',
                'token'   => null]);

        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return $this->respondInternalError(Lang::get('apiresponse.logout_fail'));
        }
    }

    /**
     * Change user password.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        $credentials = $request->only('old_password', 'new_password', 'confirm_password');

        $rules = [
            'old_password' => 'required',
            'new_password' => 'required|string|min:6',
            'confirm_password' => 'required|same:new_password'
        ];

        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'message' => $validator->messages()
            ],406);
        }

        if (!(Hash::check($request->get('old_password'), Auth::user()->password))) {
            // The passwords not matches
            return response()->json([
                'success' => 0,
                'message' => 'Password lama Anda salah!'
            ],406);
        }

        //uncomment this if you need to validate that the new password is same as old one
        if(strcmp($request->get('old_password'), $request->get('new_password')) == 0){
            //Current password and new password are same
            return response()->json([
                'success' => 0,
                'message' => 'Password baru dan password lama tidak boleh sama'
            ], 406);
        }

        //Change Password
        $user = Auth::user();
        $user->password = Hash::make($request->get('new_password'));
        $user->save();

        if($user){
            return response()->json([
                'success' => 1,
                'message' => 'Password Anda berhasil dirubah',
            ],200);
        }else{
            return response()->json([
                'success' => 0,
                'message' => 'Ganti password gagal, silahkan dicoba lagi!',
            ],406); //Not Acceptable
        }

    }

    /**
     * Reset user password.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $useremail = $request->email;
        $newpassword = $this->generateRandomCode (8,'lud');
        $hashpassword =  Hash::make($newpassword);

        $user = User::where('email', $useremail)->first();

        if (!$user) {
            return $this->respondUnAuthorized('Alamat email anda tidak ditemukan');
        }else{
            //reset the password
            $user->password = $hashpassword;
            $user->save();

            try {
                $details = [
                    'username' => $useremail,
                    'newpassword' => $newpassword
                ];

                Mail::to($useremail)
                    ->send(new ResetPassword($details));

            } catch (\Exception $e) {
                //Return with error
                $error_message = $e->getMessage();
                return response()->json(['success' => 0, 'message' => $error_message], 401);
            }
        }

        return response()->json([
            'success' => 1,
            'message'=> 'Reset password email telah dikirim! Silahkan cek email anda.'
        ], 200);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(JWTAuth::refresh());
    }

    /**
     * Change user password.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
         $credentials = $request->only('name', 'email', 'password');

         $rules = [
             'name' => 'required|string|max:255',
             'email' => 'required|string|email|max:255',
             'password' => 'required|min:6',
         ];

         $validator = Validator::make($credentials, $rules);

         if ($validator->fails()) {
             return response()->json([
                 'status' => 'error',
                 'message' => $validator->messages()
             ],422);
         }

         $vercode = $this->generateRandomCode (8,'ud');

         $user = User::create([
             'name' => $request->name,
             'email' => $request->email,
             'password' => Hash::make($request->password),
             'verification_code' => $vercode,
         ]);

         if($user != null){
             try {
                 $details = [
                     'username' => $request->email,
                     'code' => $vercode,
                 ];
                 Mail::to($request->email)
                     ->send(new SignupEmail($details));

             } catch (\Exception $e) {
                 //Return with error
                 $error_message = $e->getMessage();
                 return response()->json(['success' => 0, 'message' => $error_message], 401);
             }
         }

         $token = auth()->login($user);

         return response()->json([
             'success' => 1,
             'access_token' => $token,
             'validated' => 0,
             'message'=> 'Kode Verifikasi telah dikirim! Silahkan cek email anda.'
         ], 200);
    }

    /**
    * Verify user user password.
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function verify(Request $request)
    {
        $credentials = $request->only('email', 'code');
        $rules = [
            'email' => 'required|string|email|max:255',
            'code' => 'required|string|min:6',
        ];

        $validator = Validator::make($credentials, $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->messages()
            ],422);
        }

        $verification_code = $request->code;
        $username = $request->email;

        $user = User::where('verification_code', $verification_code)
                ->where('email', $username)
                ->first();

        if($user != null){
            if($user->verified==1){
                return response()->json([
                    'success' => 0,
                    'validated' => 1,
                    'message'=> 'User sudah divalidasi'
                ], 422);
            }

            $user->verified = 1;
            $user->save();
            return response()->json([
                'success' => 1,
                'validated' => 1,
                'message'=> 'Verifikasi berhasil'
            ], 200);
        }

        return response()->json([
            'success' => 0,
            'validated' => 0,
            'message'=> 'Verifikasi gagal'
        ], 401);
    }

    /**
     * Get Logged User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        $resource = $this->getLoggedUserInfo($request->email);

        $me = new Item($resource, $this->meTransformer);
        $me = $this->fractal->createData($me)->toArray(); // Transform data

        return $this->respond($me);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function whoAmI()
    {
        return $this->respond(auth()->user());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return $this->respond([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
