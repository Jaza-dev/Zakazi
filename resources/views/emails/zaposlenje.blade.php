{{-- Autori: Mateja Milošević 2020/0487 --}}

@extends("emails.sablon")

@section("naslov", "Zahtev za Zaposlenje")

@section("uvod", "Zdravo,")
@section("razrada", "Šaljemo Vam ovaj mejl jer ste dobili zahtev za zaposlenje od biznisa ".$biznis.". ".
                    "Da biste prihvatili zahtev za zaposlenje, potreban Vam je nalog na sistemu Zakazi.")
@section("zakljucak", "Ukoliko želite da prihvatite zahtev za zaposlenje, to možete uraditi klikom na dugme:")

@section("dugme", "Prihvati")
@section("link", "prihvatiZaposlenje/".$hash)

@section("napomena", "Ukoliko ne želite da prihvatite ovaj zahtev za zaposlenje, slobodno ignorišite ovaj mejl.")
