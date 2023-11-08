{{-- Autori: Mateja Milošević 2020/0487
             Miloš Paunović 2018/0294 --}}

@extends("biznis.sablon")

@section("naslov", "Moji zaposleni")

@push("styles")
    @isset ($izabran)
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

            #kalendar .fc-event {
                cursor: pointer;
            }

            #kalendar .fc-event-time, .fc-event-title {
                text-align: center;
            }

            #cenovnik .row {
                flex-wrap: unset;
                margin-left: unset;
                margin-right: unset;
                margin-bottom: 2px;
            }

            #cenovnik input, button {
                margin-right: 1px;
            }
        </style>
    @endisset
@endpush

@push("scripts")
    @isset ($izabran)
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
                    select: onCalSelect,

                    eventClick: onEventClick,

                    eventSources: [
                        {
                            url: "{{ route("kalendarBiznis") }}",
                            extraParams: {
                                zap: {{ $izabran->idKor }}
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

            const KTIP_NERADNOVREME = 0;
            const KTIP_TERMIN = 1;

            function onEventClick(info) {
                switch (info.event.extendedProps.tip) {
                    case KTIP_NERADNOVREME:
                        nVremeModal(info.event);
                        break;
                    case KTIP_TERMIN:
                        terminModal(info.event);
                        break;
                    default:
                        break;
                }
            }

            function terminModal(event) {
                $("#terminModalVremeOd")[0].innerHTML = event.extendedProps.vremeOd;
                $("#terminModalVremeDo")[0].innerHTML = event.extendedProps.vremeDo;

                $("#terminModalMusterija")[0].innerHTML = event.extendedProps.musterija;
                $("#terminModalOcena")[0].innerHTML = event.extendedProps.ocena;

                $("#terminModalOtkazi")[0].onclick = function() {
                    otkaziTermin(event.extendedProps.id);
                }

                new bootstrap.Modal("#terminModal").show();
            }

            function otkaziTermin(id) {
                $.ajax({
                    url: "{{ route("otkaziTermin") }}",
                    data: {
                        idTermin: id
                    }
                })
                    .done(function() {
                        $("#kalendarToastNaslov")[0].innerHTML = "Uspeh!";
                        $("#kalendarToastPoruka")[0].innerHTML = "Uspešno otkazan termin. Mušterija je obaveštena putem email-a.";

                        kalendar.refetchEvents()
                    })
                    .fail(function() {
                        $("#kalendarToastNaslov")[0].innerHTML = "Greška!";
                        $("#kalendarToastPoruka")[0].innerHTML = "Termin nije mogao biti otkazan.";
                    })
                    .always(function() {
                        bootstrap.Toast.getOrCreateInstance($("#kalendarToast")).show();
                    });
            }

            function nVremeModal(event) {
                $("#nVremeModalVremeOd")[0].innerHTML = event.extendedProps.vremeOd;
                $("#nVremeModalVremeDo")[0].innerHTML = event.extendedProps.vremeDo;

                $("#nVremeModalObrisi")[0].onclick = function() {
                    obrisiNVreme(event.extendedProps.id);
                }

                new bootstrap.Modal("#nVremeModal").show();
            }

            function dodajNVreme(start, end) {
                $.ajax({
                    url: "{{ route("dodajNeradnoVreme") }}",
                    method: "POST",
                    data: {
                        zap: {{ $izabran->idKor }},
                        pocetak: start.getTime() / 1000,
                        kraj: end.getTime() / 1000
                    }
                })
                    .done(function() {
                        $("#kalendarToastNaslov")[0].innerHTML = "Uspeh!";
                        $("#kalendarToastPoruka")[0].innerHTML = "Uspešno dodat period neradnog vremena.";

                        kalendar.refetchEvents()
                    })
                    .fail(function() {
                        $("#kalendarToastNaslov")[0].innerHTML = "Greška!";
                        $("#kalendarToastPoruka")[0].innerHTML = "Period neradnog vremena nije mogao biti dodat.";
                    })
                    .always(function() {
                        bootstrap.Toast.getOrCreateInstance($("#kalendarToast")).show();
                    });
            }

            function obrisiNVreme(id) {
                $.ajax({
                    url: "{{ route("obrisiNeradnoVreme") }}",
                    data: {
                        idNVreme: id
                    }
                })
                    .done(function() {
                        $("#kalendarToastNaslov")[0].innerHTML = "Uspeh!";
                        $("#kalendarToastPoruka")[0].innerHTML = "Uspešno obrisan period neradnog vremena.";

                        kalendar.refetchEvents()
                    })
                    .fail(function() {
                        $("#kalendarToastNaslov")[0].innerHTML = "Greška!";
                        $("#kalendarToastPoruka")[0].innerHTML = "Period neradnog vremena nije mogao biti obrisan.";
                    })
                    .always(function() {
                        bootstrap.Toast.getOrCreateInstance($("#kalendarToast")).show();
                    });
            }
        </script>

        <script>
            $(document).ready(function() {
                dohvatiUsluge();
            });

            function dohvatiUsluge() {
                $.ajax({
                    type:'GET',
                    url:"{{route('uslugeZaposlenog', ['idZaposleni' => $izabran->idKor])}}",
                    success: function(data) {
                        samoUslugeDiv = $('#samoUsluge');
                        jedanRed = $('#samoUsluge>.row').last().clone();
                        samoUslugeDiv.empty();
                        jedanRed.appendTo(samoUslugeDiv);

                        for(i=0; i<data.length; i++) {
                            if(i<data.length-1) jedanRed.clone().appendTo(samoUslugeDiv);
                            trenutniRed = $(samoUslugeDiv.children()[i]);
                            trenutniRed.find('input[name="naziv"]').val(data[i].naziv);
                            trenutniRed.find('input[name="cena"]').val(data[i].cena);
                            trenutniRed.find('input[name="trajanje"]').val(data[i].trajanje);
                            trenutniRed.find('input[name="id"]').val(data[i].id);
                            trenutniRed.find('button').first().off('click').on('click', function(e) {
                                rukujKlikom(e, 1);
                            });
                            trenutniRed.find('button').last().off('click').on('click', function(e) {
                                rukujKlikom(e, 0);
                            });
                            trenutniRed.children().prop('disabled', false);
                        }
                        if(data.length!=0) $('#samoUsluge').prop('hidden', false);

                        $("#cenovnik>form").on('submit', function(e) {e.preventDefault();})

                        $('#cenovnik>form>.row').find('button').off('click').on('click', function(e){
                            rukujKlikom(e, 2);
                        })
                        $('#cenovnik>form>.row').children().val('').prop('disabled', false);
                    }
                });
            }

            function rukujKlikom(e, izmeni) {
                dugme = $(e.currentTarget);
                red = dugme.parent();
                red.children().prop('disabled', true);

                _url = "{{route('izbrisiUslugu', ['idZaposleni' => $izabran->idKor])}}";

                _naziv = red.find('input[name="naziv"]').val();
                _cena = red.find('input[name="cena"]').val();
                _trajanje = red.find('input[name="trajanje"]').val();
                _id = (izmeni!=2)?red.find('input[name="id"]').val():undefined;
                _data = {
                    id:_id,
                    naziv:_naziv,
                    cena:_cena,
                    trajanje:_trajanje
                };
                if(izmeni==1) {
                    _url = "{{route('izmeniUslugu', ['idZaposleni' => $izabran->idKor])}}";
                }else if(izmeni==2) {
                    _url = "{{route('dodajUslugu', ['idZaposleni' => $izabran->idKor])}}";
                }
                console.log(_data);
                $.ajax({
                    type:'GET',
                    url:_url,
                    data: _data,
                    success: function(res){
                        dohvatiUsluge()
                    }
                });
            }
        </script>
    @endisset
@endpush

@section('sadrzaj')
    @if (!empty($zaposleni))
        <div class="row">
            <div class="col-md-auto">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        @if (isset($izabran))
                            {{ $izabran->ime." ".$izabran->prezime }}
                        @else
                            Izaberite zaposlenog...
                        @endif
                    </button>
                    <ul class="dropdown-menu">
                        @foreach ($zaposleni as $z)
                            <li><a class="dropdown-item" href="{{ route("mojiZaposleni", [ "zap" => $z->idKor ]) }}">{{ $z->ime." ".$z->prezime }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-md-auto my-auto">
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#dodajModal">Dodajte zaposlenog</button>
            </div>
        </div>

        <hr>

        @isset($izabran)
            <div class="row">
                <div class="col-xl-8 col-lg-12" style="border-right: solid 1px #595C5F;">
                    <div id="kalendar"></div>
                </div>
                <div class="col-xl-4 col-lg-12">
                    <div id="cenovnik">
                        <h2>Cenovnik</h2>
                        <form action="">
                            <div id="samoUsluge" hidden>
                                <div class="row">
                                    <input type="text" placeholder="Naziv usluge" name="naziv" class="col-6">
                                    <input type="number" placeholder="Cena" name="cena" class="col-2" min="0">
                                    <input type="number" placeholder="Trajanje" name="trajanje" class="col-2" min="0" step="10">
                                    <input name="id" class="col-2" hidden>
                                    <button class="btn btn-success col-1"><i class="bi bi-check-lg"></i></button>
                                    <button class="btn btn-danger col-1"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                            <br>
                            <h5>Dodaj uslugu:</h5>
                            <div class="row">
                                <input type="text" placeholder="Naziv usluge" name="naziv" class="col-6">
                                <input type="number" placeholder="Cena" name="cena" class="col-2" min="0">
                                <input type="number" placeholder="Trajanje" name="trajanje" class="col-2" min="0" step="10">
                                <button class="btn btn-success col-1"><i class="bi bi-plus"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endisset
    @else
        <div class="row">
            <div class="col-md-auto">
                <div class="alert alert-warning" role="alert" style="margin: 0;">
                    Nemate nijednog zaposlenog.
                </div>
            </div>
            <div class="col-md-auto my-auto">
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#dodajModal">Dodajte zaposlenog</button>
            </div>
        </div>
    @endif

    <div class="modal fade" id="dodajModal" tabindex="-1" aria-labelledby="dodajModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="dodajModalLabel">Dodajte zaposlenog</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route("zahtevZaposlenje") }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Unesite email adresu na koju će se poslati zahtev za zaposlenje:</p>
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com" name="email">
                            <label for="floatingInput">Email adresa</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">Pošalji</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @isset($izabran)
        <div class="modal fade" id="terminModal" tabindex="-1" aria-labelledby="terminModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="terminModalLabel">Termin</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <span>Vreme: </span>
                        <span id="terminModalVremeOd"></span>
                        <span> - </span>
                        <span id="terminModalVremeDo"></span>

                        <br>

                        <span>Mušterija: </span>
                        <span id="terminModalMusterija"></span>

                        <br>

                        <span>Prosečna ocena mušterije: </span>
                        <span id="terminModalOcena"></span>
                        <span><i class="bi bi-star-fill"></i></span>

                        <br><br>

                        <span>Zaposleni: {{ $izabran->ime." ".$izabran->prezime }}</span>
                    </div>
                    <div class="modal-footer">
                        <button id="terminModalOtkazi" class="btn btn-danger" type="button" title="Mušteriji će biti poslato email obaveštenje." data-bs-dismiss="modal">Otkaži</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="nVremeModal" tabindex="-1" aria-labelledby="nVremeModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="nVremeModalLabel">Neradno Vreme</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <span>Vreme: </span>
                        <span id="nVremeModalVremeOd"></span>
                        <span> - </span>
                        <span id="nVremeModalVremeDo"></span>

                        <br><br>

                        <span>Zaposleni: {{ $izabran->ime." ".$izabran->prezime }}</span>
                    </div>
                    <div class="modal-footer">
                        <button id="nVremeModalObrisi" class="btn btn-danger" type="button" data-bs-dismiss="modal">Obriši</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="kreirajModal" tabindex="-1" aria-labelledby="kreirajModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="kreirajModalLabel">Neradno Vreme</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <span>Da li želite da označite period od </span>

                        <span id="kreirajModalVremeOd"></span>
                        <span> do </span>
                        <span id="kreirajModalVremeDo"></span>

                        <span> kao neradno vreme za zaposlenog {{ $izabran->ime." ".$izabran->prezime }}?</span>
                    </div>
                    <div class="modal-footer">
                        <button id="kreirajModalDodaj" class="btn btn-success" type="button" data-bs-dismiss="modal">Potvrdi</button>
                    </div>
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
    @endisset

    @includeWhen(isset($status) && $status == 0, "porukaModal", [ "naslov" => "Uspeh!", "sadrzaj" => "Uspešno ste poslali zahtev za zaposlenje!" ])
    @includeWhen(isset($status) && $status == 1, "porukaModal", [ "naslov" => "Greška!", "sadrzaj" => "Uneli ste nevalidnu email adresu. Pokušajte ponovo." ])
@endsection
