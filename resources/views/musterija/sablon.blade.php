{{-- Autori: Zeljko Jazarevic 2020/0484 --}}

@extends("ulogovaniKorisnikSablon")

@section("navigacija")
    <li class="nav-item"><a href="{{ session("tipKorisnika") == TIP_MUSTERIJA ? route("index") : route("musterijaZakazi") }}" class="nav-link active">Zakaži</a></li>
    <li class="nav-item"><a href="{{ route("zakazaniTermini") }}" class="nav-link active">Zakazani termini</a></li>
    <li class="nav-item"><a href="{{ route("odradjeniTermini") }}" class="nav-link active">Odrađeni termini</a></li>
    @if (session("tipKorisnika") == TIP_ZAPOSLENI)
        <li class="nav-item"><a href="{{ route('index') }}" class="nav-link active">Moj kalendar</a></li>
    @endif
@endsection
