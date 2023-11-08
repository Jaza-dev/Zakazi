{{-- Autori: Željko Jazarević 2020/0484 --}}

@extends("musterija.sablon")

@section('naslov', $biznis->zvanicnoIme)

@push("styles")
    @isset ($izabranaUsluga)
        <style>
            :root {
                --fc-today-bg-color: rgba(255, 220, 40, .1);
            }

            .fc-non-business, .fc-day-disabled {
                background: hsla(0, 0%, 35%, .3) !important;
            }

            #kalendar .fc-col-header a {
                color: unset;
                text-decoration: none;
            }

            #kalendar .fc-event-time, .fc-event-title {
                text-align: center;
            }
        </style>
    @endisset
@endpush

@push("scripts")
    @isset ($izabranaUsluga)
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.7/index.global.min.js'></script>

        <script>
            var kalendar;

            document.addEventListener("DOMContentLoaded", function() {
                kalendar = new FullCalendar.Calendar(document.getElementById("kalendar"), {
                    themeSystem: "bootstrap5",
                    initialView: "timeGridWeek",

                    locale: "sr-Latn-RS",
                    buttonText: {
                        today: "Današnji dan"
                    },
                    navLinks: false,

                    slotLabelFormat: {
                        hour: "numeric",
                        minute: "2-digit",
                        hour12: false,
                        meridiem: false
                    },
                    businessHours: {
                        daysOfWeek: [ 1, 2, 3, 4, 5 ],
                        startTime: "07:00",
                        endTime: "22:00"
                    },
                    scrollTime: "07:00:00",
                    nowIndicator: true,
                    validRange: function() {
                        return {
                            start: Date.now() - 86400
                        };
                    },
                    allDaySlot: false,
                    slotDuration: "00:10",
                    slotLabelInterval: "00:10",

                    selectable: true,
                    selectOverlap: false,
                    dateClick: zakaziTermin,

                    eventClick: zakaziTermin,

                    eventSources: [
                        {
                            url: "{{ route("kalendarZaposlenog") }}",
                            extraParams: {
                                zap: {{ $izabranZaposleni->idKor }},
                                biznis: {{ $biznis->Korisnik_idKor }}
                            }
                        }
                    ]
                });

                kalendar.render();
            });

            function zeroPad(str) {
                if (str.length === 1) {
                    str = "0" + str;
                }

                return str;
            }

            function onCalSelect(info) {
                let dan = zeroPad("" + info.start.getDate());
                let mesec = zeroPad("" + info.start.getMonth());

                let sati = zeroPad("" + info.start.getHours());
                let minuta = zeroPad("" + info.start.getMinutes());

                $("#kreirajModalVremeOd")[0].innerHTML = dan + "." + mesec + ". u " + sati + ":" + minuta;

                dan = zeroPad("" + info.end.getDate());
                mesec = zeroPad("" + info.end.getMonth());

                sati = zeroPad("" + info.end.getHours());
                minuta = zeroPad("" + info.end.getMinutes());

                $("#kreirajModalVremeDo")[0].innerHTML = dan + "." + mesec + ". u " + sati + ":" + minuta;

                $("#kreirajModalDodaj")[0].onclick = function() {
                    dodajNVreme(info.start, info.end);
                }

                new bootstrap.Modal("#kreirajModal").show();
            }

            function zakaziTermin(info) {
                $.ajax({
                    url: "{{ route("zakaziTermin") }}",
                    data: {
                        idBiznisa: {{$biznis->Korisnik_idKor}},
                        idZapsoleni: {{$izabranZaposleni->Musterija_Korisnik_idKor}},
                        idMusterija: {{ auth()->user()->idKor }},
                        vremePocetka: info.dateStr,
                        trajanje: {{$izabranaUsluga->trajanje}}
                    }
                })
                    .done(function() {
                        //alert("Uspesno ste zakazali termin.")
                        $("#kalendarToastNaslov")[0].innerHTML = "Uspeh!";
                        $("#kalendarToastPoruka")[0].innerHTML = "Uspešno zakazan termin.";
                        kalendar.refetchEvents()
                    })
                    .fail(function() {
                        //alert("Nije moguce zakazati zeljeni termin.")
                        $("#kalendarToastNaslov")[0].innerHTML = "Greška!";
                        $("#kalendarToastPoruka")[0].innerHTML = "Nije moguce zakazati zadati termin.";
                    })
                    .always(function() {
                        bootstrap.Toast.getOrCreateInstance($("#kalendarToast")).show();
                    });
            }
        </script>
    @endisset
@endpush

@section('sadrzaj')
    <div class="row">
        <div class="col-12">
            <h1 class="display-6">{{$biznis->zvanicnoIme}}</h1>
            <p>{{$biznis->opis}}</p>

            <div class="col-md-auto">
                <a href="{{route('recenzijeBiznisa', ['idBiznisa' => $biznis->Korisnik_idKor])}}" class="btn btn-success">Pogledajte recenzije</a><br><br>
                <div class="row">
                    <div class="col-md-auto">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                @if (isset($izabranZaposleni))
                                    {{ $izabranZaposleni->ime." ".$izabranZaposleni->prezime }}
                                @else
                                    Izaberite zaposlenog
                                @endif
                            </button>
                            <ul class="dropdown-menu">
                                @foreach ($biznis->zaposleni() as $zaposleni)
                                    <li><a class="dropdown-item" href="{{route('odaberiZaposlenog', ['idBiznisa' => $biznis->Korisnik_idKor, 'idZaposleni' => $zaposleni->Musterija_Korisnik_idKor])}}">{{ $zaposleni->ime." ".$zaposleni->prezime }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    @if (isset($izabranZaposleni))
                        {{-- Prvo se odabere usluga --}}
                        <div class="col-md-auto" style="padding-left: 0;">
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    @if (isset($izabranaUsluga))
                                        {{ $izabranaUsluga->nazivUsluge . " - Cena: " . $izabranaUsluga->cena . " dinara - Trajanje: " . $izabranaUsluga->trajanje . "minuta"}}
                                    @else
                                        Izaberite uslugu
                                    @endif
                                </button>
                                <ul class="dropdown-menu">
                                    @foreach ($usluge as $usluga)
                                        <li><a class="dropdown-item" href="{{ route('odaberiUslugu', [
                                        'idBiznisa' => $biznis->Korisnik_idKor,
                                        'idZaposleni' => $izabranZaposleni->Musterija_Korisnik_idKor,
                                        'idUsluga' => $usluga->idCenovnik
                                    ]) }}">{{ $usluga->nazivUsluge . " - Cena: " . $usluga->cena . "din - Trajanje: " . $usluga->trajanje . "min"}}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div><br>

        </div>
        <hr>
        @if (isset($izabranaUsluga))

        <div class="row">
            <div class="col-12">
                <p>Zaposleni:<b> {{$izabranZaposleni->ime}} {{$izabranZaposleni->prezime}}</b></p>
                <p>Usluga: <b>{{$izabranaUsluga->nazivUsluge}}</b></p>
                <p>Cena: <b>{{$izabranaUsluga->cena}} dinara</b></p>
                <p>Trajanje: <b>{{$izabranaUsluga->trajanje}} min</b></p>

                <div class="col-12" style="border-right: solid 1px #595C5F;">
                    <div id="kalendar"></div>
                </div>
            </div>
        </div>

        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="kalendarToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <strong class="me-auto" id="kalendarToastNaslov"></strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    <span id="kalendarToastPoruka"></span>
                </div>
            </div>
        </div>

    @elseif(isset($recenzije))
        <div class="d-flex justify-content-center">
            <div class="col-4 text-center">
                <h1 class="display-6 text-center">Recenzije</h1><br>
                @if (count($recenzije) == 0)
                    <p>Još uvek nema recenzija.</p>
                @endif
                @foreach ($recenzije as $recenzija)
                    <div class="p-3 bg-opacity-10 border border-secondary border-start-4 rounded">
                        <h2 class="text-white">{{$recenzija->musterija->korisnickoIme}}</h2>
                        <p>Zaposleni: {{$recenzija->zaposleni->ime}}</p>
                        <p>
                        @for ($i = 0; $i < $recenzija->ocenaKorisnika; $i++)
                            <i class="bi bi-star-fill"></i>
                        @endfor
                        </p>
                        <p>{{$recenzija->komentarKorisnika}}</p>
                    </div><br>
                @endforeach
            </div>
        </div>
    @endif
    </div>

@endsection
