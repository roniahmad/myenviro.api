<?php

namespace App\Models\Sales\V1;

use Illuminate\Database\Eloquent\Model;

class Jos extends Model
{
    /**
	* The table associated with the model.
	* if your table name is different then your class name,
	* define here
	* @var string
	*/
    protected $table = "sales.jos";

    protected $connection = 'db_sales';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "spk_id" ,
        "perusahaan_id" ,
        "klien_id" ,
        "no_jos" ,
        "tgl_jos" ,
        "remarks" ,
        "customer_type" ,
        "customer_group" ,
        "currency" ,
        "top" ,
        "mop" ,
        "tax_number" ,
        "market_segment" ,
        "customer_contact" ,
        "billing_contact" ,
        "project_no" ,
        "agency" ,
        "business_line_core" ,
        "business_line_local" ,
        "district_manager" ,
        "labor_supply" ,
        "location" ,
        "type_turnover" ,
        "start_date" ,
        "end_date" ,
        "scope_of_work" ,
        "layanan_id" ,
        "produk_id" ,
        "cc_emp_id" ,
        "cc_approved" ,
        "technical_emp_id" ,
        "technical_approved" ,
        "finance_emp_id" ,
        "finance_approved" ,
        "hr_emp_id" ,
        "hr_approved" ,
        "gm_emp_id" ,
        "gm_approved" ,
        "nilai_kontrak" ,
        "standar_gaji" ,
        "is_thr_ditagih" ,
        "hari_thr_ditagih" ,
        "is_bpjs_tk" ,
        "bpjs_tk_persen" ,
        "is_bpjs_jht" ,
        "bpjs_jht_persen" ,
        "is_bpjs_jp" ,
        "bpjs_jp_persen" ,
        "is_bpjs_kesehatan" ,
        "bpjs_kesehatan_persen" ,
        "is_potongan_mp" ,
        "potongan_mp_persen" ,
        "jml_shift" ,
        "hari_kerja" ,
        "is_lembur_ditagihkan" ,
        "is_lembur_pemerintah" ,
        "lembur_per_jam" ,
        "is_insentif" ,
        "insentif_per_bulan" ,
        "insentif_per_jam" ,
        "is_potongan_absen" ,
        "potongan_alpa" ,
        "potongan_sakit_ijin" ,
        "user_id" ,
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
    ];
}
