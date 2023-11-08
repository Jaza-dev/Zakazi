{{-- Autori: Zeljko Jazarevic 2020/0484 --}}

@extends("ulogovaniKorisnikSablon")

@section("navigacija")
    <li class="nav-item"><a href="{{ route("index") }}" class="nav-link active">Verifikacija</a></li>
    <li class="nav-item"><a href="{{ route("izmenaPodataka") }}" class="nav-link active">Izmena podataka</a></li>
@endsection
