{{-- Autori: Zeljko Jazarevic 2020/0484 --}}

@extends("ulogovaniKorisnikSablon")

@section("navigacija")
    <li class="nav-item"><a href="{{ route("index") }}" class="nav-link active">Moji zaposleni</a></li>
    <li class="nav-item"><a href="{{route('zavrseniTermini')}}" class="nav-link active">Završeni termini</a></li>
@endsection
