@component('mail::message')

Please do not reply!

Mohon untuk tidak membalas ke email ini!

# Hai {{ $username }},

Password Akun anda sudah direset. Berikut adalah informasi akun anda :
    * **username** : {{ $username }}
    * **password** : {{ $newpassword }}

Demi keamanan akun Anda, segera ganti password lama Anda dengan password baru.

Silahkan download aplikasi mobile Mobile Pestcare pada link di bawah ini.

@component('mail::button', ['url' => 'https://play.google.com/store/apps/details?id=com.enviro.pestcare'])
Download disini
@endcomponent

Terima Kasih<br>
{{ config('app.team') }}
@endcomponent
