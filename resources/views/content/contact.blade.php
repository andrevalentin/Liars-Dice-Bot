@extends('main')
@section('content')
    <div class="container-top">
        <div class="container">
            <div class="row">
                <div class="col-md-12" style="padding-top: 21px">
                    <h3 class="content-text">Contact</h3>
                    <p>Contact us at <a href="mailto:liarsdice@valentin.nu">liarsapp@valentin.nu</a></p>
                    <br><br><br><br>
                </div>
            </div>
        </div>
    </div>

    <style>
        .content-frame {
            width: 100%;
            background: #f5f5f5;
            height: 475px;
            margin-top: 50px;
            margin-bottom: 50px;
            border-radius: 24px;
            overflow: hidden;
        }

        .container-top {
            background: white;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.50);
        }

        .container-top>.container>.row>.col-md-12>p {
            color: black;
        }

        .container-top>.container>.row>.col-md-12>ul {
            color: black;
        }


        .container-top>.container>.row>.col-md-12>p {
            color: black;
        }

        .container-bottom {
            background: whitesmoke;
            height: 400px;
            padding-top: 25px;
            padding-bottom: 25px;
        }

        .title-text {
            color: black;
            font-size: 56px;
            font-weight: bold;
        }

        .panel-body{
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.20);
        }

        .content-text {
            color: black;
            line-height: 1.6;
            font-size: 26px;
        }

        .btn-download {
            width: 100%;
            height: 50px;
            background: #679a93;
            color: white;
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            text-shadow: 1px 1px 1px rgb(55, 55, 73);
            border: 0;
            margin-bottom: 30px;
        }

        .btn-download:hover {
            background: #57c49f;
            color: whitesmoke;
            border: 0;
            -webkit-box-shadow: 1px 1px 1px 1px rgba(0, 0, 0, 0.5);
            -moz-box-shadow: 1px 1px 1px 1px rgba(0, 0, 0, 0.5);
            box-shadow: 1px 1px 1px 1px rgba(0, 0, 0, 0.5);
        }

        .btn-download:focus, .btn-download:active, .btn-download:active:focus {
            background: #57c49f;
            color: white;
            outline: none;
        }
    </style>
@endsection