{{-- Autori: Zeljko Jazarevic 2020/0484 --}}

@extends("admin.sablon")

@section("naslov", "Izmena podataka")

@section("sadrzaj")
    <div class="row">
        <div class="col-xl-3 col-lg-12">
            <h4 class="h4">Izmena podataka</h4><br>
            <p>Unesite e-mail korisnika kako biste izmenili njegove podatke.</p>
            <form action="{{route('prikaziPodatkeKorisnika')}}" method="post">
                @csrf
                <div class="input-group mb-3">
                    <input type="text" name="email" class="form-control" placeholder="Email korisnika" required>
                    <input type="submit" class="btn btn-outline-secondary" type="button" id="button-addon2"
                           value="Pretrazi">
                    @error('email')
                    <br><span class="text-warning">{{$message}}</span>
                    @enderror
                </div>
            </form>
        </div>
        <div class="col-xl-9 col-lg-12">
            @if(isset($korisnik))
                <form action="{{route('izmeniPodatke')}}" method="post">
                    @csrf
                    <table class="table table-striped table-dark align-middle">
                        <tr>
                            <td>E-mail:</td>
                            <td><input type="text" name="email" class="form-control" value="{{$korisnik->email}}"></td>
                        </tr>
                        @if($korisnik->tipKorisnika == 1 || $korisnik->tipKorisnika == 3)
                            <tr>
                                <td>Korisničko ime:</td>
                                <td><input type="text" name="korisnickoIme" class="form-control"
                                           value="{{$korisnik->korisnickoIme}}"></td>
                            </tr>
                            <tr>
                                <td>Ime:</td>
                                <td><input type="text" name="ime" class="form-control" value="{{$korisnik->ime}}"></td>
                            </tr>
                            <tr>
                                <td>Prezime:</td>
                                <td><input type="text" name="prezime" class="form-control"
                                           value="{{$korisnik->prezime}}"></td>
                            </tr>
                        @elseif ($korisnik->tipKorisnika == 2)
                            <tr>
                                <td>Zvanično ime:</td>
                                <td><input type="text" name="zvanicnoIme" class="form-control"
                                           value="{{$korisnik->zvanicnoIme}}"></td>
                            </tr>
                            <tr>
                                <td>Broj telefona:</td>
                                <td><input type="text" name="brojTelefona" class="form-control"
                                           value="{{$korisnik->brojTelefona}}"></td>
                            </tr>
                            <tr>
                                <td>Ime vlasnika:</td>
                                <td><input type="text" name="imeVlasnika" class="form-control"
                                           value="{{$korisnik->imeVlasnika}}"></td>
                            </tr>
                            <tr>
                                <td>Prezime vlasnika:</td>
                                <td><input type="text" name="prezimeVlasnika" class="form-control"
                                           value="{{$korisnik->prezimeVlasnika}}"></td>
                            </tr>
                            <tr>
                                <td>PIB:</td>
                                <td><input type="text" name="PIB" class="form-control"
                                           value="{{$korisnik->PIB == null ? "" : $korisnik->PIB}}"></td>
                            </tr>
                        @endif
                        <tr>
                            <td>
                                <input type="submit" class="btn btn-success" value="Izmeni podatke">
                            </td>
                            <td>
                                <input type="hidden" name="idKor" value="{{$korisnik->idKor}}">

                            </td>
                        </tr>

                    </table>
                </form>
            @endif
            @if(session("status") == 1)
                <div class="alert alert-success alert-dismissible fade show">
                    Uspešna izmena podataka.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                {{session(["status" => 0])}}
            @elseif (session("status") == 2)
                <div class="alert alert-danger alert-dismissible fade show">
                    Ne postoji korisnik sa tom email adresom.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                {{session(["status" => 0])}}
            @endif
        </div>
    </div>
@endsection
