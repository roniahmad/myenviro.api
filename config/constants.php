<?php

return [
    'config' => [
            'base_url' => 'BASE_URL',
    ],
    'referensi' => [
        'jenis_layanan_enviro' => 29,
        'jenis_currency' => 141,
        'jenis_jabatan' => 18,
        'jenis_pekerjaan_cleaning' => 31,
        'jenis_inside_outside' => 24,

    ],

    'upload_dir' => [
        'daily_report' => './daily/report/',
    ],

    'upload_filename' => [
        'daily_report' => '/daily/report/',
    ],

    'upload_file_prefix' => [
        'daily_report' => 'DRI',
    ],

    'messages' => [
        'daily_report_added_ok' => 'Daily Report berhasil ditambahkan',
        'daily_report_added_fail' => 'Daily Report gagal ditambahkan',

        'daily_report_deleted_ok' => 'Daily Report berhasil dihapus',
        'daily_report_deleted_fail' => 'Daily Report gagal dihapus',

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


];
