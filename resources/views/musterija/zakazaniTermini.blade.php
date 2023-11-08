{{-- Autori: Željko Jazarević 2020/0484 --}}

@extends("musterija.sablon")

@section('naslov', 'Zakazani termini')

@section('sadrzaj')
    <div class="row justify-content-center">
        <div class="col-10">
            <h1 class="display-6">Zakazani termini</h1><br>
            <div>
                @if ($termini->count() == 0)
                    <p>Nemate zakazanih termina.</p>
                @else
                    @foreach ($termini as $termin)
                        <div class="p-3 bg-opacity-10 border border-secondary border-start-4 rounded">
                            <h2 class="text-white">{{$termin->biznis->zvanicnoIme}}</h2>
                            <p><b>Zaposleni: </b>{{$termin->zaposleni->ime}} {{$termin->zaposleni->prezime}}</p>
                            <p>{{ $termin->vremePocetka }} - {{ $termin->vremeKraja }}</p>
                            <div class="text-end"><a href="{{ route('musterijaOtkaziTermin', ['idTermina' => $termin->idTermina]) }}" class="btn btn-danger">Otkazi</a></div>
                        </div><br>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endsection
