<div class="container-top">
    <div class="container">
        <div class="row">
            <div class="col-md-12" style="padding: 70px;padding-bottom: 100px;text-align: center">
                <h1 class="title-text">Play Liar's Dice on Slack!</h1>
                <h3 style="margin-bottom: 40px" class="content-text">A classic dice game about deception, bluffing and
                    messing <br> with your friends or colleagues</h3>
                <a href="https://slack.com/oauth/authorize?&client_id=248125203971.250771295524&scope=bot"><img
                            alt="Add to Slack" height="40" width="139"
                            src="https://platform.slack-edge.com/img/add_to_slack.png"
                            srcset="https://platform.slack-edge.com/img/add_to_slack.png 1x, https://platform.slack-edge.com/img/add_to_slack@2x.png 2x"/></a>
            </div>
        </div>
    </div>
</div>

<div class="container" style="padding-top: 50px">
    <div class="row" style="margin-top: 50px">
        <div class="col-md-6 hidden-md hidden-lg">
            <h3 style="margin-top: 0">Start the game</h3>
            <p style="font-size: 16px;line-height: 1.7;">Play Liar’s Dice by inviting the bot to a channel of your
                choosing, then simply type “play liar” to open a new game up for your friends or colleagues to join!</p>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-body">
                    <img src="{{asset('img1.png')}}" alt="">
                </div>
            </div>
        </div>
        <div class="col-md-6 hidden-sm hidden-xs">
            <h3 style="margin-top: 0">Start the game</h3>
            <p style="font-size: 16px;line-height: 1.7;">Play Liar’s Dice by inviting the bot to a channel of your
                choosing, then simply type “play liar” to open a new game up for your friends or colleagues to join!</p>
        </div>
    </div>

    <div class="row" style="margin-top: 100px">
        <div class="col-md-6 ">
            <h3 style="margin-top: 0">Let people join</h3>
            <p style="font-size: 16px;line-height: 1.7;">To join a game, your Slack members simply have to write “me”,
                and once you are satisfied with the number of participants (3-4 recommended), you as the host can write
                “start game” to get the action going!</p>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-body">
                    <img src="{{asset('img2.png')}}" alt="">
                </div>
            </div>
        </div>
    </div>

    <div class="row" style="margin-top: 100px">
        <div class="col-md-6 hidden-md hidden-lg">
            <h3 style="margin-top: 0">Play the game</h3>
            <p style="font-size: 16px;line-height: 1.7;">The game is all about guessing the amount of dice present in
                the game, you do this by typing commands to the bot. If you type 4.3, well then you are guessing that
                there are 4 3's in the game! You can of course also deceive your opponents, which is a big part of the
                game. If you think someone is lying, simply time “Liar” to stop the round - which will reveal who was
                right!</p>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-body">
                    <img src="{{asset('img3.png')}}" alt="">
                </div>
            </div>
        </div>
        <div class="col-md-6 hidden-sm hidden-xs">
            <h3 style="margin-top: 0">Play the game</h3>
            <p style="font-size: 16px;line-height: 1.7;">The game is all about guessing the amount of dice present in
                the game. If you type 4.3, well then you are guessing that
                there are 4 3's in the game! You can of course also deceive your opponents, which is a big part of the
                game. Think someone is lying? Simply type “Liar” to stop the round - which will reveal who was
                right!</p>

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
        background: #2986BE;
        background-image: url('{{asset('bg.png')}}'), linear-gradient(-150deg, #00C1B6 0%, #136EB5 97%);
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.75);
    }

    .container-bottom {
        background: whitesmoke;
        height: 400px;
        padding-top: 25px;
        padding-bottom: 25px;
    }

    .title-text {
        color: white;
        font-size: 56px;
        font-weight: bold;
        text-shadow: 1px 1px 1px rgb(0, 0, 0);
    }

    .panel-body {
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.20);
    }

    .content-text {
        color: whitesmoke;
        line-height: 1.6;
        font-size: 26px;
        text-shadow: 1px 1px 1px rgb(0, 0, 0);
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