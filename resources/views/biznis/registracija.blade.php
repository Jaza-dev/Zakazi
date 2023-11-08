{{-- Autori: Zeljko Jazarevic 2020/0484 --}}
{{--         Radosav Popadic 2020/0056 --}}

@extends('sablon')

@section('naslov', 'Registracija biznisa')

@section('sadrzaj')
    <br>
    <h1 class="display-6 text-white text-center">Registrujte biznis na Zakazi</h1><br>
    <div class="row justify-content-center">
        <div class="col-4">
            <form action="{{route('registracijaBiznisaAkcija')}}" method="post">
                @csrf
                <div class="mb-3">
                    <label for="zvanicnoIme" class="form-label text-white">Zvanično ime biznisa</label>
                    <input type="text" name="zvanicnoIme" class="form-control" id="zvanicnoIme" value="{{old('zvanicnoIme')}}" placeholder="Unesite zvanično ime biznisa" required>
                </div>
                @error('zvanicnoIme')
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
                    <label for="imeVlasnika" class="form-label text-white">Ime vlasnika</label>
                    <input type="text" name="imeVlasnika" class="form-control" id="imeVlasnika" value="{{old('imeVlasnika')}}" placeholder="Unesite ime vlasnika" required>
                </div>
                @error('imeVlasnika')
                    <div class="mb-3 alert alert-danger">{{$message}}</div>
                @enderror
                <div class="mb-3">
                    <label for="prezimeVlasnika" class="form-label text-white">Prezime vlasnika</label>
                    <input type="text" name="prezimeVlasnika" class="form-control" id="prezimeVlasnika" value="{{old('prezimeVlasnika')}}" placeholder="Unesite prezime vlasnika" required>
                </div>
                @error('prezimeVlasnika')
                    <div class="mb-3 alert alert-danger">{{$message}}</div>
                @enderror
                <div class="mb-3">
                    <label for="tipBiznisa" class="form-label text-white">Tip biznisa</label>
                    <input type="text" name="tipBiznisa" class="form-control" id="tipBiznisa" value="{{old('tipBiznisa')}}" placeholder="Unesite tip biznisa" required>
                </div>
                @error('tipBiznisa')
                    <div class="mb-3 alert alert-danger">{{$message}}</div>
                @enderror
                <div class="mb-3">
                    <label for="telefon" class="form-label text-white">Broj telefona</label>
                    <input type="text" name="telefon" class="form-control" id="telefon" value="{{old('telefon')}}" placeholder="Unesite broj telefona" required>
                </div>
                @error('telefon')
                    <div class="mb-3 alert alert-danger">{{$message}}</div>
                @enderror
                <div class="mb-3">
                    <label for="opis" class="form-label text-white">Opis biznisa (mora sadržati adresu biznisa ako postoji)</label>
                    <input type="text" name="opis" class="form-control" id="opis" value="{{old('opis')}}" placeholder="Unesite opis biznisa" required>
                </div>
                @error('opis')
                    <div class="mb-3 alert alert-danger">{{$message}}</div>
                @enderror
                <div class="mb-3">
                    <label for="pib" class="form-label text-white">PIB (opciono)</label>
                    <input type="text" name="pib" class="form-control" id="pib" value="{{old('pib')}}" placeholder="Unesite PIB">
                </div>
                @error('pib')
                    <div class="mb-3 alert alert-danger">{{$message}}</div>
                @enderror
                @if(session("status"))
                    <div class="mb-3 alert alert-danger">
                        {{session("status")}}
                    </div>
                @endif
                <a href="{{route('prijavaForma')}}" class="link-secondary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Prijava</a><br>
                <a href="{{route('registracijaKorisnika')}}" class="link-secondary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Registracija za korisnika</a><br><br>
                <input type="submit" class="btn btn-secondary" value="Registracija">
            </form>
        </div>
    </div><br><br>
@endsection
