@component('mail::message')

# Hai {{{ $name }}},

#### Kenapa memilih kami untuk kenyamanan keseharian Anda?
> - Liability Insurance : Setiap Treatment yang dilakukan oleh Tim Enviro Pestcare dijamin oleh MSIG Insurance
> - Berstandar ISO 14001, ISO 4501, ISO 9001

#### Kami menyediakan SURVEY & KONSULTASI GRATIS !

Silahkan download aplikasi mobile Mobile Pestcare pada link di bawah ini.
@component('mail::button', ['url' => 'https://play.google.com/store/apps/details?id=com.enviro.pestcare'])
Download disini
@endcomponent

Ingin tahu lebih jauh tentang Enviro Pestcontrol? Silahkan download Company profile kami ;)

Terima Kasih<br>

{{ config('app.marketing_team') }}<br>
<small>+62 877 8128 9268</small><br>
<small>Email: roni.connect@gmail.com</small>
@endcomponent
