{{-- Autori: Zeljko Jazarevic 2020/0484 --}}
{{--         Radosav Popadic 2020/0056 --}}

@extends('sablon')

@section('naslov', 'Registracija korisnika')

@section('sadrzaj')
    <br>
    <h1 class="display-6 text-white text-center">Registrujte se na Zakazi</h1><br>
    <div class="row justify-content-center">
        <div class="col-4">
            <form action="{{route('registracijaKorisnikaAkcija')}}" method="post">
                @csrf
                <div class="mb-3">
                    <label for="korisnickoIme" class="form-label text-white">Korisničko ime</label>
                    <input type="text" name="korisnickoIme" class="form-control" id="korisnickoIme" value="{{old('korisnickoIme')}}" placeholder="Unesite korisničko ime" required>
                </div>
                @error('korisnickoIme')
                    <div class="mb-3 alert alert-danger">{{$message}}</div>
                @enderror
                <div class="mb-3">
                    <label for="email" class="form-label text-white">Email adresa</label>
                    <input type="email" name="email" class="form-control" id="email" value="{{old('email')}}" placeholder="Unesite email adresu" required>
                </div>
                @error('email')
                    <div class="mb-3 alert alert-danger">{{$message}}</div>
                @enderror
                <div class="mb-3">
                    <label for="lozinka" class="form-label text-white">Lozinka</label>
                    <input type="password" name="lozinka" class="form-control" id="lozinka" placeholder="Unesite lozinku" required>
                </div>
                @error('lozinka')
                    <div class="mb-3 alert alert-danger">{{$message}}</div>
                @enderror
                <div class="mb-3">
                    <label for="ponovljenaLozinka" class="form-label text-white">Ponovljena lozinka</label>
                    <input type="password" name="ponovljenaLozinka" class="form-control" id="ponovljenaLozinka" placeholder="Ponovite lozinku" required>
                </div>
                @error('ponovljenaLozinka')
                    <div class="mb-3 alert alert-danger">{{$message}}</div>
                @enderror
                <div class="mb-3">
                    <label for="ime" class="form-label text-white">Ime</label>
                    <input type="text" name="ime" class="form-control" id="ime" value="{{old('ime')}}" placeholder="Unesite ime" required>
                </div>
                @error('ime')
                    <div class="mb-3 alert alert-danger">{{$message}}</div>
                @enderror
                <div class="mb-3">
                    <label for="prezime" class="form-label text-white">Prezime</label>
                    <input type="text" name="prezime" class="form-control" id="prezime" value="{{old('prezime')}}" placeholder="Unesite prezime" required>
                </div>
                @error('prezime')
                    <div class="mb-3 alert alert-danger">{{$message}}</div>
                @enderror
                @if(session("status"))
                    <div class="mb-3 alert alert-danger">
                        {{session("status")}}
                    </div>
                @endif
                <a href="{{route('prijavaForma')}}" class="link-secondary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Prijava</a><br>
                <a href="{{route('registracijaBiznisa')}}" class="link-secondary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Registracija za biznis</a><br><br>
                <input type="submit" class="btn btn-secondary" value="Registracija">
            </form>
        </div>
    </div><br><br>
@endsection
