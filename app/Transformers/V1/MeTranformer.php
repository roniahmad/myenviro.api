<?php
namespace App\Transformers\V1;

use League\Fractal\TransformerAbstract;

class MeTranformer extends TransformerAbstract
{
    public function transform($me)
    {
        $comp=$emp=$email=$org=$tipe_user=null;
        if($me){
            foreach($me as $key)
            {
                $comp = $key->data_perusahaan;
                $emp = $key->data_pegawai;
                $email = $key->email;
                $org = $key->id_organisasi;
                $tipe_user = $key->tipe_user;
            }

            $comps = explode("#", $comp);
            $emps = explode("#", $emp);

            $comps_name = $comps[0];
            $comps_addr = $comps[1];
            $emp_name = $emps[0];
            $emp_addr = $emps[1];
        }else{
            $comps_name = null;
            $comps_addr = null;
            $emp_name = null;
            $emp_addr = null;
        }

        return [
            'tipe_user'         => $tipe_user,
        	'email'            	=> $email,
			'organization_id'	=> $org,
			'comp_name'			=> $comps_name,
			'comp_addr'			=> $comps_addr,
			'emp_name'		    => $emp_name,
			'emp_addr'			=> $emp_addr,
        ];
    }

}
