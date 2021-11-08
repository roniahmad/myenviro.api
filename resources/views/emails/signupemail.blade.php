@component('mail::message')

# Hai {{ $username }},

Selamat datang di MyEnviro<br>
Aktifkan Layanan MyEnviro dengan Kode di bawah ini:

>
> Kode Verifikasi : <strong>{{$code}}</strong>
>

Kami perlu memastikan bahwa email Anda benar dan tidak disalahgunakan oleh pihak yang tidak berkepentingan.


Terima Kasih<br>
{{ config('app.team') }}
@endcomponent
