{{-- Autori:  Željko Jazarević 2020/0484
              Miloš Paunović 2018/0294 --}}

@extends("musterija.sablon")

@section('sadrzaj')
    <div class="row justify-content-center">
        <div class="col-6">
            <form action="{{route('musterijaPretraga')}}" method="get">
                <div class="input-group">
                    <input type="text" name="imeBiznisa" class="form-control" placeholder="Unesite ime biznisa">
                    <div class="input-group-append">
                        <select name="sortiraj" class="form-select" aria-label="Default select example">
                            <option value="poOceni">Sortiraj po oceni</option>
                            <option value="poImenu">Sortiraj po imenu</option>
                        </select>
                    </div>
                    <div class="input-group-append">
                        <input class="btn btn-secondary" type="submit" value="Pretraži">
                    </div>
                </div>
            </form>
        </div>
    </div><br>
    <div class="row justify-content-center">
        <div class="col-10">
            <div>
                {{-- prikaz svih biznisa ili svih pretrazenih biznisa --}}
                @if ($biznisi->count() == 0)
                    <p>Nema biznisa koji odgovaraju kriterijumu pretrage.</p>
                @else
                    @foreach ($biznisi as $biznis)
                        <div class="p-3 bg-opacity-10 border border-secondary border-start-4 rounded">
                            <h2 class="text-white">{{$biznis->zvanicnoIme}}</h2><span class="text-white">

                                @php
                                    if(is_null($biznis->prosecnaOcena)) echo '<i>još uvek nema ocena</i>';
                                    else {
                                        echo number_format($biznis->prosecnaOcena, 1).' ';
                                        for($i=0; $i < floor($biznis->prosecnaOcena); $i++) {
                                            echo '<i class="bi bi-star-fill"></i>';
                                        }
                                        if($biznis->prosecnaOcena<5) {
                                            $deo = $biznis->prosecnaOcena - floor($biznis->prosecnaOcena);
                                            if($deo>0.75)
                                                echo '<i class="bi bi-star-fill"></i>';
                                            elseif($deo>0.25)
                                                echo '<i class="bi bi-star-half"></i>';
                                            else
                                                echo '<i class="bi bi-star"></i>';
                                            for($i = floor($biznis->prosecnaOcena)+1; $i<5; $i++)
                                                echo '<i class="bi bi-star"></i>';
                                        }
                                    }
                                @endphp

                                <!--{{number_format($biznis->prosecnaOcena, 1)}} <i class="bi bi-star"></i-->
                            </span><br><hr>
                            <p>{{$biznis->opis}}</p>
                            <div class="text-end">
                                <a href="{{ route('biznisInfo', ['idBiznisa' => $biznis->Korisnik_idKor]) }}" type="button" class="btn btn-lg btn-success">Zakaži termin</a>
                            </div>
                        </div><br>
                    @endforeach
                @endif
            </div>

        </div>
    </div>
@endsection
