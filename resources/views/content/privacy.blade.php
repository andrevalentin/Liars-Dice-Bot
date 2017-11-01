@extends('main')
@section('content')
    <div class="container-top">
        <div class="container">
            <div class="row">
                <div class="col-md-12" style="padding-top: 21px">


                    <h3 class="content-text">Privacy Policy</h3>




                    <p><b>Information</b></p>

                    <p>We only store unidentifiable ID strings and numbers provided by the Slack APIs to run the
                        game.</p>
                    <p>
                        All we store in our database, is the game data itself, what your rolls were, what you called, not
                        what you are otherwise saying. We ONLY store relevant data to the game itself, even if you use the
                        game’s “say” function, we don’t even store any of that!
                    </p>

                    <p><b>Your Privacy</b></p>

                    <p>
                        Liar’s Dice doesn’t store any recognisable data about you. The only thing we store are your ID’s.

                    </p>

                    <p><b>Copyright</b></p>
                    <p>Liar’s Dice is not my idea, this is not copyrighted in any way.</p>

                    <p><b>Cookies</b></p>
                    Nope, not sure how we could even do that?
                    Third party links and advertising
                    We don’t store your emails or anything like that, so no, that would be impossible.

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