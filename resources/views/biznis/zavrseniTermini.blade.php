{{-- Autori: Miloš Paunović 2018/0294
             --}}

@extends("biznis.sablon")

@section('naslov', 'Odrađeni termini')

@section('sadrzaj')


    <script>
        $(document).ready(function(){
            dohvatiZavrseneTermine();
        })

        function dohvatiZavrseneTermine() {
            $.ajax({
                type:'GET',
                url:"{{route('dataZavrseniTerminiBiznis')}}",
                success:function(data) {
                    glavniDiv = $('#termini').html('');
                    if(data.length==0) glavniDiv.html('<p>Nemate odrađenih termina</p>');
                    else {
                        for(i=0; i<data.length; i++) {
                            elements = '<div class="p-3 bg-opacity-10 border border-secondary border-start-4 rounded"><form action="'
                            elements += data[i].rutaOceni;
                            elements += '" method="get"><h2 class="text-white">';
                            elements += data[i].musterija;
                            elements += '</h2><h6>Termin odradio: <strong>';
                            elements += data[i].zaposleni;
                            elements += '</strong></h6><p>';
                            elements += data[i].vremePocetka + ' - ' + data[i].vremeKraja;
                            elements += '</p><label for="ocena" class="form-label">Ocena:</label><input name="ocena" type="range" class="form-range" min="1" max="5" id="ocena"><div class="mb-3"><label for="komentar" class="form-label text-white">Komentar:</label><textarea id="komentar" name="komentar" class="form-control" cols="13" rows="3" placeholder="Unesite komentar..."></textarea></div><div class="text-end"><input type="submit"  class="btn btn-success" value="Oceni"><button class="btn btn-danger" formaction="';
                            elements += data[i].rutaNePrikazuj;
                            elements += '"><i class="bi bi-trash"></i></button></div></form></div><br>';

                            glavniDiv.append(elements);
                        }
                    }

                    $("form").each(function(){
                        $(this).on('submit', function(e) {
                            e.preventDefault();

                            _ocena = $(this).find('input[name="ocena"]').val();
                            _komentar = $(this).find('textarea').val();
                            _ruta = e.currentTarget.action;
                            _data = undefined;

                            if ($(e.originalEvent.submitter).attr('formaction')) {
                                _ruta = $(e.originalEvent.submitter).attr('formaction');
                            }else {
                                _data = {
                                    ocena: _ocena,
                                    komentar: _komentar
                                }
                            }

                            $.ajax({
                                type: 'GET',
                                url: _ruta,
                                data: _data
                            }).always(dohvatiZavrseneTermine());
                        });
                    });
                }
            });
        }




    </script>
    <div class="row justify-content-center">
        <div class="col-10">
            <h1 class="display-6">Odrađeni termini</h1><br>

            <div id="termini"></div>
        </div>
    </div>
@endsection
