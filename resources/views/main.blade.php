<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Liar's Dice</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- Styles -->
</head>
<body>

@include('header.header')

@if(!empty($alert))
    @if($alert['status'] == 'error')
        <div class="alert">
            <div class="alert alert-danger" style="margin: 0;text-align: center">
                <strong>{{$alert['message']}}</strong>
            </div>
        </div>
    @endif
    @if($alert['status'] == 'success')
        <div class="alert">
            <div class="alert alert-success" style="margin: 0;text-align: center">
                <strong>{{$alert['message']}}</strong>
            </div>
        </div>
    @endif
@endif

<div class="container-fluid" style="padding: 0">
    @if(View::hasSection('content'))
        @yield('content')
    @else
        @include('content.index')
    @endif
</div>

@include('footer.footer')

</body>
</html>

<style>
    body {
        font-family: 'Open Sans', sans-serif;
        background: whitesmoke;
    }

    .panel {
        border-radius: 0;
    }
</style>