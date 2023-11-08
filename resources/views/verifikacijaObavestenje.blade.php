<!--autori: Radosav Popadic 2020/0056 -->
@extends('sablon')

@section('naslov', 'Email verifikacija')

@section('sadrzaj')
<br><h1 class="display-6 text-center text-white">Verifikujte email</h1><br>
<div class="row justify-content-center">
    <div class="col-4">
        Poslali smo email na <b>{{$email}}</b><br>
        Kliknite na link u emailu da biste nastavili sa verifikacijom.<br><br>
        <form action="{{route('posaljiMejl', ['id' => $id])}}" method="get">
            @csrf
            <input type="submit" class="btn btn-secondary" value="Pošalji ponovo email"><br><br>
        </form>
        <a href="{{route('prijavaForma')}}" class="link-secondary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Prijava</a><br>
        <a href="{{route('registracijaKorisnika')}}" class="link-secondary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Registracija za korisnika</a><br>
        <a href="{{route('registracijaBiznisa')}}" class="link-secondary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Registracija za biznis</a><br><br>

    </div>
</div>
@endsection
