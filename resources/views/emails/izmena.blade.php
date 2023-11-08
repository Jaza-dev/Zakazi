{{-- Autori: Mateja Milošević 2020/0487 --}}

@extends("emails.sablon")

@section("naslov", "Verifikujte Izmene Podataka")

@section("uvod", "Zdravo,")
@section("razrada", "Šaljemo Vam ovaj mejl u vezi sa Vašim zahtevom za izmenu podataka na sistemu Zakazi.")
@section("zakljucak", "Molimo potvrdite izmenu podataka klikom na dugme:")

@section("dugme", "Verifikacija")
@section("link", "verifikacijaIzmena/".$hash)

@section("napomena", "Ukoliko niste zatražili izmenu podataka na sistemu Zakazi, slobodno ignorišite ovaj mejl.")
