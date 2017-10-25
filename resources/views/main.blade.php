<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Liar's Dice</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700|Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- Styles -->
</head>
<body>

@include('header.header')

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
        font-family: 'Roboto', sans-serif;
        background: rgba(58,55,73,1);
    }


</style>