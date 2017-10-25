<div class="container-top">
    <div class="container">
        <div class="row">
            <div class="col-md-5">
                <div>
                    <div class="content-frame">
                        <img src="{{asset('test-gif.gif')}}" alt="">
                    </div>
                </div>
            </div>

            <div class="col-md-7" style="padding-top: 21px">
                <h1 class="title-text">Where lies happens </h1>
                <h3 class="content-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Architecto dicta
                    ipsam laudantium natus
                    nemo veritatis voluptas. Distinctio dolore eaque molestias recusandae tenetur. Accusamus aliquam
                    animi commodi dicta facilis id</h3>
                <button class="btn btn-default btn-download">Get Started</button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid container-bottom">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="well"></div>
            </div>
            <div class="col-md-4">
                <div class="well"></div>
            </div>
            <div class="col-md-4">
                <div class="well"></div>
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
        background: #323648;
        min-height: 640px;
        background: rgba(58,55,73,1);
        background: -moz-linear-gradient(top, rgba(58,55,73,1) 0%, rgba(22,20,28,1) 100%);
        background: -webkit-gradient(left top, left bottom, color-stop(0%, rgba(58,55,73,1)), color-stop(100%, rgba(22,20,28,1)));
        background: -webkit-linear-gradient(top, rgba(58,55,73,1) 0%, rgba(22,20,28,1) 100%);
        background: -o-linear-gradient(top, rgba(58,55,73,1) 0%, rgba(22,20,28,1) 100%);
        background: -ms-linear-gradient(top, rgba(58,55,73,1) 0%, rgba(22,20,28,1) 100%);
        background: linear-gradient(to bottom, rgba(58,55,73,1) 0%, rgba(22,20,28,1) 100%);
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#3a3749', endColorstr='#16141c', GradientType=0 );

    }

    .container-bottom {
        background: whitesmoke;
        height: 400px;
        padding-top: 25px;
        padding-bottom: 25px;
    }

    .title-text {
        color: white;
        font-size: 73px;
        font-weight: bold;
        font-family: 'Playfair Display', serif;
        text-shadow: 1px 1px 1px rgb(0, 0, 0);
    }

    .content-text {
        color: whitesmoke;
        line-height: 1.6;
        font-size: 21px;
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