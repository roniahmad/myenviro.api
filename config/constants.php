<?php

return [

    'app_version' => [
        'envihero' => 'ENVIHERO',
        'myenviro' => 'MYENVIRO',
    ],

    'config' => [
        'base_url' => 'BASE_URL',
    ],

    'referensi' => [
        'jenis_jabatan' => 18,
        'jenis_inside_outside' => 24,
        'jenis_status_komplain' => 26,
        'jenis_layanan_enviro' => 29,
        'jenis_pekerjaan_cleaning' => 31,
        'jenis_help_topic' => 35,
        'jenis_currency' => 141,
        'jenis_treatment' => 23,
        'jenis_merk_dagang' => 39,
        'jenis_status_kondisi_barang' => 10,
    ],

    'upload_dir' => [
        'daily_report' => './daily/report/',

        'envidesk_complaint' => './envidesk/complaint/',
        'envidesk_qc' => './envidesk/qc/',
        'envidesk_action' => './envidesk/action/',
        'envidesk_recomendation' => './envidesk/recomendation/',
    ],

    'upload_filename' => [
        'daily_report' => '/daily/report/',

        'envidesk_complaint' => '/envidesk/complaint/',
        'envidesk_qc' => '/envidesk/qc/',
        'envidesk_action' => '/envidesk/action/',
        'envidesk_recomendation' => '/envidesk/recomendation/',
    ],

    'upload_file_prefix' => [
        'daily_report' => 'DRI',
        'envidesk_complaint' => 'TIC',
        'envidesk_qc' => 'QC',
        'envidesk_action' => 'ACT',
        'envidesk_recomendation' => 'REC',
    ],

    'messages' => [
        'envidesk_complaint_ok' => 'Komplain berhasil ditambahkan',
        'envidesk_complaint_fail' => 'Komplain gagal ditambahkan',

        'envidesk_complaint_cancel_ok' => 'Komplain dibatalkan',
        'envidesk_complaint_cancel_fail' => 'Komplain gagal dibatalkan',

        'envidesk_recommendation_ok' => 'Rekomendasi berhasil ditambahkan',
        'envidesk_recommendation_fail' => 'Rekomendasi gagal ditambahkan',

        'envidesk_feedback_ok' => 'Terima kasih atas feedback Anda',
        'envidesk_feedback_fail' => 'Feedback gagal ditambahkan',

        'envidesk_action_ok' => 'Action Plan berhasil ditambahkan',
        'envidesk_action_fail' => 'Action Plan gagal ditambahkan',

        'envidesk_qc_ok' => 'Quality Check berhasil ditambahkan',
        'envidesk_qc_fail' => 'Quality Check gagal ditambahkan',

        'pestcontrol_sts_added_ok' => 'STS berhasil ditambahkan',
        'pestcontrol_sts_added_fail' => 'STS gagal ditambahkan',
        'pestcontrol_sts_slipnumber_fail'=>'No Slip Number Tidak Boleh Sama',

        'pestcontrol_sts_detail_added_ok' => 'STS Detail berhasil ditambahkan',
        'pestcontrol_sts_detail_added_fail' => 'STS Detail gagal ditambahkan',

        'pestcontrol_installation_added_ok' => 'Instalasi Monitoring berhasil ditambahkan',
        'pestcontrol_installation_added_fail' => 'Instalasi Monitoring gagal ditambahkan',

        'pestcontrol_monitoring_update_ok' => 'Monitoring berhasil terupdate',
        'pestcontrol_monitoring_update_fail' => 'Monitoring gagal terupdate',
        'pestcontrol_monitoring_tanggal_monitoring_fail'=>'Tanggal Monitoring Unit Ini Sudah Tersedia, Tidak Boleh Sama',

        'pestcontrol_monitoring_delete_ok' => 'Monitoring berhasil dihapus',
        'pestcontrol_monitoring_delete_fail' => 'Monitoring gagal dihapus',

        'daily_report_added_ok' => 'Daily Report berhasil ditambahkan',
        'daily_report_added_fail' => 'Daily Report gagal ditambahkan',

        'daily_report_recommendation_added_ok' => 'Rekomendasi Daily Report berhasil ditambahkan',
        'daily_report_recommendation_added_fail' => 'Rekomendasi Daily Report gagal ditambahkan',
        'daily_report_recommendation_not_found' => 'Rekomendasi Daily Report tidak ditemukan',

        'daily_report_feedback_added_ok' => 'Feedback Daily Report berhasil ditambahkan',
        'daily_report_feedback_added_fail' => 'Feedback Daily Report gagal ditambahkan',

        'daily_report_deleted_ok' => 'Daily Report berhasil dihapus',
        'daily_report_deleted_fail' => 'Daily Report gagal dihapus',

        'file_not_found' => 'File not found',
    ],

    'prefix' => [
        'laporan_dac_deskripsi' => 'Laporan Aktifitas Harian Job Order Sheet No ',
    ],

    'service' => [
            'cleaning' => 1,
            'landscaping' => 2,
            'security' => 3,
            'pestcontrol' => 4,
            'facility_management' => 5,
            'technical_maintenance' => 6,
            'reception' => 7,
            'energy_management' => 8,
    ],

    'service_db' => [
            'cleaning' => 'cleaning',
            'landscaping' => 'landscaping',
            'security' => 'security',
            'pestcontrol' => 'pestcontrol',
            'facility_management' => 'facility_management',
            'technical_maintenance' => 'technical_maintenance',
            'reception' => 'reception',
            'energy_management' => 'energy_management',
    ],

    'status_complaint' => [
            'open'          => 1,
            'inprogress'    => 2,
            'blocked'       => 3,
            'cancelled'     => 4,
            'completed'     => 5,
    ],

];
