@extends('main')
@section('content')

    <div class="container-top">
        <div class="container">
            <div class="row">
                <div class="col-md-12" style="padding-top: 21px;color: white;">

                    <h3 class="content-text">How-to-play</h3>
                    <p>1. Invite the bot to a new Slack channel</p>
                    <p>2. Invite people you would like to play with, to the same channel</p>
                    <p>3. Write “Play Liar’s” in the channel (this opens a new game)</p>
                    <p>4. Now other players can write “me” to join the game</p>
                    <p>5. When you are satisfied with the amount of players (recommended 3-5, minimum 2) then you write
                        “start game” in the channel to start the game</p>
                    <p>6. Now the bot should write you in private, giving every player some dice, and the game
                        begins.</p>
                    <p>7. The first player gets a message about it being their turn, and they now have to guess how many
                        of a certain dice there is in the game total, you do it like this: “3,6”</p>

                    <br>

                    <p>If you write “3,6” it means you think there are at least 3 6’s in the game, now the next player
                        can either write “liar” if he doesn’t believe you, or write “4,2”, “5,4” etc.</p>
                    <p>See special rules about the value of “1”.</p>
                    <p>If a player writes “liar”, the round ends, and all the winners lose a dice, the loser
                        automatically starts next round again, and the goal of the game is to NOT have any dice once the
                        game is over.</p>

                    <p>Rules are simple, there is only one loser, and a lot of winners in Liar’s Dice!</p>

                    <br>

                    <h3 class="content-text">Bot Commands</h3>
                    <ul>
                        <li>In Channel
                            <ul>
                                <li>Play liar</li>
                                <li>Close game</li>
                                <li>Me</li>
                                <li>Leave</li>
                                <li>Start game</li>
                            </ul>
                        </li>
                        <li>In private message with the bot
                            <ul>
                                <li>[ Amount of Dice ] , [ Dice Face ] (Eg. 4,4)</li>
                                <li>Liar</li>
                                <li>Abort game</li>
                                <li>Say < Message ></li>
                            </ul>
                        </li>
                    </ul>

                    <br>
                    <br>
                    <h3 class="content-text">Special rules</h3>

                    <p><b>The 1</b></p>
                    <p>The dice face 1 counts as a joker, which means if your hand looks like this: 1,1,2 then
                        theoretically you have 3 2’s, or 2 6’s and a 2.</p>
                    <p>Just remember, if you have a 1, then it counts as anything, also for your opponents.</p>

                    <br>

                    <p><b>Betting on 1’s</b></p>
                    <p>You can also bet on 1’s, like saying I believe there are 2,1’s in the game.</p>
                    <p>However, since 1’s are jokers, this means that they only count for themselves, which is why 1’s
                        count higher than 6’s, so if I say 1,1, then you can’t say 1,6 afterwords, you would have to say
                        2,6.</p>

                    <br>
                    <p><b>The staircase rule.. (disabled by default)</b></p>
                    <p>To be explained later..</p>

                    <br>
                    <br>
                    <br>
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
