{{-- Autori: Zeljko Jazarevic 2020/0484 --}}
{{--         Radosav Popadic 2020/0056 --}}

@extends('sablon')

@section('naslov', 'Prijava')

@section('sadrzaj')
<br><h1 class="display-6 text-center text-white">Dobro došli na Zakazi</h1><br>
<div class="row justify-content-center">
    <div class="col-4">
        <form action="{{ route('prijavaAkcija') }}" method="post">
            @csrf
            <input hidden name="redirect" value="{{ $redirect }}">

            <div class="mb-3">
                <label for="email" class="form-label text-white">Email adresa</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="Unesite email adresu" required  value="{{old('email')}}">
            </div>
            <div class="mb-3">
                <label for="lozinka" class="form-label text-white">Lozinka</label>
                <input type="password" name="lozinka" class="form-control" id="lozinka" placeholder="Unesite lozinku" required>
            </div>
            @if(session("status"))
                <div class="mb-3 alert alert-danger">
                    {{session("status")}}
                    @if(session("link"))
                        <br>
                        <a href="{{route('posaljiMejl', ['id' => session('link')])}}" class="link-secondary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">
                            Kliknite ovde da ponovo pošaljete email verifikacije.
                        </a>
                    @endif
                </div>
            @endif
            <a href="{{route('registracijaKorisnika')}}" class="link-secondary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Registracija za korisnika</a><br>
            <a href="{{route('registracijaBiznisa')}}" class="link-secondary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Registracija za biznis</a><br><br>
            <input type="submit" class="btn btn-secondary" value="Prijava">
        </form>
    </div>
</div><br><br>
@endsection
