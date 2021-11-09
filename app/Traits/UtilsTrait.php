<?php
namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait UtilsTrait
{
	/**
     * Generate random string
     * @param $length int
	 * @param $available_sets String
     * @return String
     * $this->generateRandomCode(6, 'ud')
     */
    function generateRandomCode($length, $available_sets)
    {
        $sets = array();
    	if(strpos($available_sets, 'l') !== false)
    		$sets[] = 'abcdefghjkmnpqrstuvwxyz';
    	if(strpos($available_sets, 'u') !== false)
    		$sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
    	if(strpos($available_sets, 'd') !== false)
    		$sets[] = '23456789';
    	if(strpos($available_sets, 's') !== false)
    		$sets[] = '!@#$%&*?';

    	$all = '';
    	$code = '';
    	foreach($sets as $set)
    	{
    		$code .= $set[array_rand(str_split($set))];
    		$all .= $set;
    	}

    	$all = str_split($all);
    	for($i = 0; $i < $length - count($sets); $i++)
    		$code .= $all[array_rand($all)];

    	$code = str_shuffle($code);

        return $code;
    }

    /**
     * return parameter value by config name
    */
    public function getValueByConfig($config_name)
    {
        $konpik = DB::table('master.config')
                ->select('parameter_value')
                ->where('nama', $config_name)
                ->first();
        $number = 0;
        if($konpik){
            $number = $konpik->parameter_value;
        }

        return $number;
    }

}
