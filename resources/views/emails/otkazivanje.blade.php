{{-- Autori: Mateja Milošević 2020/0487 --}}

@extends("emails.sablonObavestenje")

@section("naslov", "Obavestenje o otkazanom terminu")

@section("uvod", "Zdravo,")
@section("razrada", "Šaljemo Vam ovaj mejl jer je Vaš termin zakazan ".$vreme.", sa zaposlenim ".$zaposleni.", nažalost otkazan od strane biznisa ".$biznis.".")
@section("zakljucak", "Ukoliko želite da zakažete novi termin, uvek možete to uraditi na zakazizen.rs")
