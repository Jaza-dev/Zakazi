{{-- Autori: Zeljko Jazarevic 2020/0484 --}}

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('naslov')</title>
    <link rel="icon" href="{{ asset('slike/zakazi.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>

    @stack("styles")
    @stack("scripts")
</head>
<body class="container-fluid bg-dark text-white">
    @yield('zaglavlje')
    <br>
    @yield('sadrzaj')
    <br>
    <div class="row">
        <div class="text-center" style="margin-bottom: 15px;" >
            <span title="Željko Jazarević   2020/0484&#13;&#10;Mateja Milošević 2020/0487&#13;&#10;Miloš Paunović    2018/0294&#13;&#10;Radosav Popadić 2020/0056">
                &copy; Zen Tim
            </span>
        </div>
    </div>
</body>
</html>
