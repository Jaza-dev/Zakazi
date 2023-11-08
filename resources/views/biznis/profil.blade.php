<!-- autori: Radosav Popadic 2020/0056 -->

@extends("biznis.sablon")

@section("naslov", "Profil")

@section('sadrzaj')
    @php($kor = \App\Http\Controllers\Utility\Helperi::dohvatiAuthKorisnika())

    <div class="row justify-content-center">
        <div class="col-10">
            <h1 class="display-6">Profil</h1>

            <hr>

            <div>
                <form action="{{ route("izmeniPodatkeBiznis") }}" method="post">
                    @csrf
                    <table class="table table-striped table-dark align-middle">
                        <tr>
                            <td>Ime vlasnika:</td>
                            <td>
                                <input type="text" name="imeVlasnika" class="form-control" required value="{{ $kor->imeVlasnika }}">
                                @error("imeVlasnika")
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </td>
                        </tr>
                        <tr>
                            <td>Prezime vlasnika:</td>
                            <td>
                                <input type="text" name="prezimeVlasnika" class="form-control" required value="{{ $kor->prezimeVlasnika }}">
                                @error("prezimeVlasnika")
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </td>
                        </tr>
                        <tr>
                            <td>Broj telefona:</td>
                            <td>
                                <input type="text" name="brojTelefona" class="form-control" required value="{{ $kor->brojTelefona }}">
                                @error("brojTelefona")
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </td>
                        </tr>
                        <tr>
                            <td>Zvanično ime:</td>
                            <td>
                                <input type="text" name="zvanicnoIme" class="form-control" required value="{{ $kor->zvanicnoIme }}">
                                @error("zvanicnoIme")
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </td>
                        </tr>
                        <tr>
                            <td>Tip biznisa:</td>
                            <td>
                                <input type="text" readonly name="tipBiznisa" class="form-control-plaintext" value="{{ $kor->tipBiznisa->naziv }}">
                            </td>
                        </tr>
                        <tr>
                            <td>Opis biznisa (mora sadržati adresu biznisa ako postoji):</td>
                            <td>
                                <input type="text" name="opis" class="form-control" required value="{{ $kor->opis }}">
                                @error("opis")
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </td>
                        </tr>
                        <tr>
                            <td>PIB (opciono):</td>
                            <td>
                                <input type="text" name="PIB" class="form-control" value="{{ $kor->PIB }}">
                                @error("PIB")
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </td>
                        </tr>
                        <tr><td height="27.25px"></td><td height="27.25px"></td></tr>
                        <tr>
                            <td>E-mail:</td>
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
    <!-- && ($status === 0 || $status === 1) -->
    @if (isset($status))
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="rezToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="15000">
                <div class="toast-header">
                    <strong class="me-auto" id="rezToastNaslov">Uspeh!</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    <span>Uspešno izmenjeni podaci!</span>
                    @if ($status === 1)
                        <span>
                            Verifikacioni link je poslat na Vašu novu email adresu. Vaša email adresa će biti promenjena nakon verifikacije i potvrde administratora.
                        </span>
                    @elseif ($status===2)
                        <span>
                            Vaše zvanično ime će biti promenjeno nakon potvrde administratora.
                        </span>
                    @elseif ($status===3)
                        <span>
                            Verifikacioni link je poslat na Vašu novu email adresu. Vaša email adresa i zvanično ime će biti promenjeni nakon verifikacije i potvrde administratora.
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <script>
            bootstrap.Toast.getOrCreateInstance($("#rezToast")).show();
        </script>
    @endif
@endsection
