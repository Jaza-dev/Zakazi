{{-- Autori: Mateja Milošević 2020/0487 --}}

@extends("emails.sablon")

@section("naslov", "Verifikujte Mejl Adresu")

@section("uvod", "Zdravo,")
@section("razrada", "Šaljemo Vam ovaj mejl u vezi sa Vašim zahtevom za registraciju na sistemu Zakazi.")
@section("zakljucak", "Molimo potvrdite Vašu mejl adresu klikom na dugme:")

@section("dugme", "Verifikacija")
@section("link", "mejlVerifikacija/".$hash)

@section("napomena", "Ukoliko niste zatražili registraciju na sistemu Zakazi, slobodno ignorišite ovaj mejl.")
