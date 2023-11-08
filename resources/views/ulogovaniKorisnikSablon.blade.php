{{-- Autori: Zeljko Jazarevic 2020/0484 --}}

@extends('sablon')

@section("naslov", "Zakazi")

@push("styles")
    <style>
        a.nav-link.active:hover {
            color: #343a40;
        }
    </style>
@endpush

@section('zaglavlje')
    <div class="row sticky-top" data-bs-theme="light">
        <nav class="col-12 navbar navbar-expand-lg bg-secondary">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route("index") }}">
                    <img src="{{ asset('slike/zakazi.png') }}" alt="Logo" width="40" height="40" class="d-inline-block align-text-top">
                </a>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @yield("navigacija")
                </ul>

                @php($kor = \App\Http\Controllers\Utility\Helperi::dohvatiAuthKorisnika())

                <span class="dropdown form-inline my-2 my-lg-0" data-bs-theme="dark">
                    <button class="nav-link dropdown-toggle text-dark" data-bs-toggle="dropdown" aria-expanded="false">
                        @switch ($kor->tipKorisnika)
                            @case (TIP_ADMIN)
                            @default
                                @php($displej = $kor->email)

                                @break
                            @case (TIP_MUSTERIJA)
                            @case (TIP_ZAPOSLENI)
                                @php($displej = $kor->ime." ".$kor->prezime)

                                @break
                            @case (TIP_BIZNIS)
                                @php($displej = $kor->zvanicnoIme)

                                @break
                        @endswitch

                        {{ $displej }}
                    </button>
                    <ul class="dropdown-menu" style="left:-75px">
                        @if ($kor->tipKorisnika != TIP_ADMIN)
                            <li><a class="dropdown-item" href="{{route("profil")}}">Profil</a></li>
                        @endif
                        <li><a class="dropdown-item" href="{{ route("odjava") }}">Odjava</a></li>
                    </ul>
                </span>
            </div>
        </nav>
    </div>
@endsection
