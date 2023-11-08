{{-- Autori: Zeljko Jazarevic 2020/0484 --}}

@extends("admin.sablon")

@section("naslov", "Verifikacija")

@section("sadrzaj")
    <div class="row">
        <div class="col-xl-9 col-lg-12">
            <table class="table table-striped table-dark align-middle">
                <tr>
                    <th>Ime biznisa</th>
                    <th>Email biznisa</th>
                    <th>Ime vlasnika</th>
                    <th>Prezime vlasnika</th>
                    <th>PIB</th>
                    <th>Traženi tip biznisa</th>
                    <th>Tipovi biznisa</th>
                    <th>Novo ime biznisa</th>
                    <th>Novi email biznisa</th>
                    <th>Verifikacija</th>
                </tr>
                @foreach ($neVerifikovani as $biznis)
                    <form action="{{route('verifikuj', ['idKor' => $biznis->idKor])}}" method="post">
                        @csrf
                        <tr>
                            <td>{{$biznis->zvanicnoIme}}</td>
                            <td>{{$biznis->email}}</td>
                            <td>{{$biznis->imeVlasnika}}</td>
                            <td>{{$biznis->prezimeVlasnika}}</td>
                            <td>{{$biznis->PIB == null ? "/" : $biznis->PIB}}</td>
                            <td>{{$biznis->tipBiznisa->naziv}}</td>
                            <td>
                                <select class="form-select" name="tipBiznisa">
                                    @foreach($tipoviBiznisa as $tipBiznisa)
                                        <option value="{{$tipBiznisa->naziv}}">{{$tipBiznisa->naziv}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>{{$biznis->novoZvanicnoIme == null ? "/" : $biznis->novoZvanicnoIme}}</td>
                            <td>{{$biznis->noviEmail == null ? "/" : $biznis->noviEmail}}</td>
                            <td><input type="submit" class="btn btn-sm btn-success" value="Verifikuj">
                                <a href="{{route('odbij', ['idKor' => $biznis->idKor])}}" class="btn btn-sm btn-danger"><i class="bi bi-trash3"></i> Odbij</a></td>
                        </tr>
                    </form>
                @endforeach
            </table>
        </div>
        <div class="col-xl-3 col-lg-12">
            <h4 class="h4">Dodaj novi tip biznisa</h4>
            <form action="{{route('dodajNoviTipBiznisa')}}" method="post">
                @csrf
                <div class="input-group mb-3">
                    <input type="text" name="imeTipaBiznisa" class="form-control" placeholder="Novi tip biznisa">
                    <input type="submit" class="btn btn-outline-secondary" type="button" id="button-addon2" value="Dodaj">
                  </div>
            </form>
        </div>
    </div>
@endsection
