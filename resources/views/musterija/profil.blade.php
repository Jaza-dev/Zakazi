{{-- Autori: Mateja Milošević 2020/0487 --}}

@extends("musterija.sablon")

@section("naslov", "Profil")

@section('sadrzaj')
    @php($kor = \App\Http\Controllers\Utility\Helperi::dohvatiAuthKorisnika())

    <div class="row justify-content-center">
        <div class="col-10">
            <h1 class="display-6">Profil</h1>

            <hr>

            @if ($kor->tipKorisnika == TIP_ZAPOSLENI)
                <h4>Vaša ukupna ocena: {{ $kor->dohvatiOcenu() }} <i class="bi bi-star-fill"></i></h4>
                <br>
                <h5>Biznisi u kojima ste zaposleni:</h5>
                <span>&emsp;&emsp;</span>

                @php($first = true)
                @php($str = "")
                @foreach ($kor->biznisi() as $b)
                    @php($str .= !$first ? ", ".$b->zvanicnoIme : $b->zvanicnoIme)

                    @if ($first)
                        @php($first = false)
                    @endif
                @endforeach

                <span>{{ $str }}</span>

                <hr>
            @endif

            <div>
                <form action="{{ route("izmeniPodatkeMusterija") }}" method="post">
                    @csrf
                    <table class="table table-striped table-dark align-middle">
                        <tr>
                            <td>Ime:</td>
                            <td>
                                <input type="text" name="ime" class="form-control" required value="{{ $kor->ime }}">
                                @error("ime")
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </td>
                        </tr>
                        <tr>
                            <td>Prezime:</td>
                            <td>
                                <input type="text" name="prezime" class="form-control" required value="{{ $kor->prezime }}">
                                @error("prezime")
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </td>
                        </tr>
                        <tr>
                            <td>Korisničko ime:</td>
                            <td>
                                <input type="text" name="korisnickoIme" class="form-control" required value="{{ $kor->korisnickoIme }}">
                                @error("korisnickoIme")
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </td>
                        </tr>
                        <tr><td height="27.25px"></td><td height="27.25px"></td></tr>
                        <tr>
                            <td>Email adresa:</td>
                            <td>
                                <input type="text" name="email" class="form-control" required value="{{ $kor->email }}">
                                @error("email")
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </td>
                        </tr>
                        <tr>
                            <td>Stara lozinka:</td>
                            <td>
                                <input type="password" name="staraLozinka" class="form-control">
                                @error("staraLozinka")
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </td>
                        </tr>
                        <tr>
                            <td>Nova lozinka:</td>
                            <td>
                                <input type="password" name="novaLozinka" class="form-control">
                                @error("novaLozinka")
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </td>
                        </tr>
                        <tr>
                            <td>Ponovljena nova lozinka:</td>
                            <td>
                                <input type="password" name="novaLozinkaP" class="form-control">
                                @error("novaLozinkaP")
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <input type="submit" class="btn btn-success float-end" value="Izmeni podatke">
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>

    @if (isset($status) && ($status === 0 || $status === 1))
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="rezToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <strong class="me-auto" id="rezToastNaslov">Uspeh!</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    <span>Uspešno izmenjeni podaci!</span>
                    @if ($status === 1)
                        <span> Verifikacioni link je poslat na Vašu novu email adresu. Vaša email adresa će biti promenjena nakon verifikacije.</span>
                    @endif
                </div>
            </div>
        </div>

        <script>
            bootstrap.Toast.getOrCreateInstance($("#rezToast")).show();
        </script>
    @endif
@endsection
