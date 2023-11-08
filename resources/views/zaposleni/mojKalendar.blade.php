{{-- Autori: Mateja Milošević 2020/0487 --}}

@extends("musterija.sablon")

@section("naslov", "Moj kalendar")

@push("styles")
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
@endpush

@push("scripts")
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

                selectable: false,

                eventSources: [
                    { url: "{{ route("kalendarZaposleni") }}" }
                ]
            });

            kalendar.render();
        });
    </script>
@endpush

@section('sadrzaj')
    <div class="row justify-content-center">
        <div class="col">
            <h1 class="display-6">Moj kalendar</h1><br>
            <div>
                <span style="font-size: 20px;">Legenda:&emsp;</span>

                @php($legenda = app(\App\Http\Controllers\MusterijaController::class)->kalendarZaposleni(null, true))
                @php($zap = \App\Http\Controllers\Utility\Helperi::dohvatiAuthKorisnika())

                @foreach ($zap->biznisi() as $b)
                    <span style="color: {{ $legenda[$b->Korisnik_idKor] }};">■</span><span> - {{ $b->zvanicnoIme }}</span>
                    &emsp;&emsp;
                @endforeach
            </div>
            <hr>
            <div>
                <div id="kalendar"></div>
            </div>
        </div>
    </div>
@endsection
