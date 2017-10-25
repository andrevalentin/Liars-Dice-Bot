<div class="container">
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a style="color: whitesmoke;font-family: 'Playfair Display', serif;    font-size: 26px;font-weight: bold;    text-shadow: 1px 1px 1px rgb(0, 0, 0);"
                   class="navbar-brand" href="/"><img style="height: 45px;display: inline-block;margin-right: 15px;"
                                                      src="{{asset('logo.png')}}" alt="">Liar's Dice</a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="/leaderboard">Leaderboards</a></li>
                    <li><a href="/help">Help</a></li>
                    <li><a href="/rules">Rules</a></li>
                    <li><a style="padding: 10px;" href="/install">
                            <button class="btn btn-sm btn-default">Get Started</button>
                        </a>
                    </li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</div>

<style>
    .navbar {
        margin-bottom: 0;
        background: none;
        border:none;
    }
    .navbar-default {
        height: 70px;
        padding-top: 10px;
    }
    .navbar-default .navbar-nav>li>a {
        color:whitesmoke;
        font-size: 14px;
        font-weight: bold;
        color: #6d9a91;
        text-shadow: 1px 1px 1px rgb(0, 0, 0);
    }

</style>