{{-- Autori: Željko Jazarević 2020/0484 --}}

@extends("musterija.sablon")

@section('naslov', 'Odrađeni termini')

@section('sadrzaj')
    <div class="row justify-content-center">
        <div class="col-10">
            <h1 class="display-6">Odrađeni termini</h1><br>
            <div>
                @if ($termini->count() == 0)
                    <p>Nemate odrađenih termina</p>
                @else
                    @foreach ($termini as $termin)
                        <div class="p-3 bg-opacity-10 border border-secondary border-start-4 rounded">
                            <form action="{{route('musterijaOceniTermin', ['idTermina' => $termin->idTermina])}}">
                                <h2 class="text-white">{{ $termin->biznis->zvanicnoIme }}</h2>
                                <p><b>Termin odradio: </b>{{$termin->zaposleni->ime}} {{$termin->zaposleni->prezime}}</p>
                                <p>{{ $termin->vremePocetka }} - {{ $termin->vremeKraja }}</p>
                                <label for="ocena" class="form-label">Ocena:</label>
                                <input name="ocena" type="range" class="form-range" min="1" max="5" id="ocena">
                                <div class="mb-3">
                                    <label for="komentar" class="form-label text-white">Komentar:</label>
                                    <textarea id="komentar" name="komentar" class="form-control" cols="13" rows="3" placeholder="Unesite komentar..."></textarea>
                                </div>
                                <div class="text-end">
                                    <input type="submit"  class="btn btn-success" value="Oceni">
                                    <a href="{{route('nePrikazujKorisniku', ['idTermina' => $termin->idTermina])}}" type="button" class="btn btn-danger"><i class="bi bi-trash"></i></a>
                                </div>
                            </form>
                        </div><br>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endsection
