<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance</title>
    <script src="https://kit.fontawesome.com/9e9ad697fd.js" crossorigin="anonymous"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        html,
        body {
            height: 100%;
            background: #434A54;
            color: white;
            font-family: 'Inconsolata', monospace;
            font-size: 100%;
        }

        h1{
            font-weight: 600;
        }

        h2{
            font-weight: 400;
        }

        .maintenance {
            text-transform: uppercase;
            margin-bottom: 1rem;
            font-size: 3rem;
        }

        .container {
            display: table;
            margin: 0 auto;
            max-width: 1024px;
            width: 100%;
            height: 100%;
            align-content: center;
            position: relative;
            box-sizing: border-box;
        }

        .container .what-is-up {
            position: absolute;
            width: 100%;
            top: 50%;
            transform: translateY(-50%);
            display: block;
            vertical-align: middle;
            text-align: center;
            box-sizing: border-box;
        }

        .container .what-is-up .spinny-cogs {
            display: block;
            margin-bottom: 2rem;
        }

        .container .what-is-up .spinny-cogs .fa:nth-of-type(1) {
            animation: fa-spin-one 1s infinite linear;
        }

        .container .what-is-up .spinny-cogs .fa:nth-of-type(3) {
            animation: fa-spin-two 2s infinite linear;
        }

        @media screen and (max-width: 768px) {
            .maintenance {
                font-size: 2rem;
            }

            .container .what-is-up {
                padding: 1rem;
            }

            .container .what-is-up h2 {
                font-size: 1rem;
            }
        }

        @-webkit-keyframes fa-spin-one {
            0% {
                -webkit-transform: translateY(-2rem) rotate(0deg);
                transform: translateY(-2rem) rotate(0deg);
            }

            100% {
                -webkit-transform: translateY(-2rem) rotate(-359deg);
                transform: translateY(-2rem) rotate(-359deg);
            }
        }

        @keyframes fa-spin-one {
            0% {
                -webkit-transform: translateY(-2rem) rotate(0deg);
                transform: translateY(-2rem) rotate(0deg);
            }

            100% {
                -webkit-transform: translateY(-2rem) rotate(-359deg);
                transform: translateY(-2rem) rotate(-359deg);
            }
        }

        .fa-spin-one {
            -webkit-animation: fa-spin-one 1s infinite linear;
            animation: fa-spin-one 1s infinite linear;
        }

        @-webkit-keyframes fa-spin-two {
            0% {
                -webkit-transform: translateY(-.5rem) translateY(1rem) rotate(0deg);
                transform: translateY(-.5rem) translateY(1rem) rotate(0deg);
            }

            100% {
                -webkit-transform: translateY(-.5rem) translateY(1rem) rotate(-359deg);
                transform: translateY(-.5rem) translateY(1rem) rotate(-359deg);
            }
        }

        @keyframes fa-spin-two {
            0% {
                -webkit-transform: translateY(-.5rem) translateY(1rem) rotate(0deg);
                transform: translateY(-.5rem) translateY(1rem) rotate(0deg);
            }

            100% {
                -webkit-transform: translateY(-.5rem) translateY(1rem) rotate(-359deg);
                transform: translateY(-.5rem) translateY(1rem) rotate(-359deg);
            }
        }

        .fa-spin-two {
            -webkit-animation: fa-spin-two 2s infinite linear;
            animation: fa-spin-two 2s infinite linear;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="what-is-up">
            <div class="spinny-cogs">
                <i class="fa fa-cog" aria-hidden="true"></i>
                <i class="fa fa-5x fa-cog fa-spin" aria-hidden="true"></i>
                <i class="fa fa-3x fa-cog" aria-hidden="true"></i>
            </div>
            <h1 class="maintenance">Sistem Sedang Maintenance</h1>
            <h2>Mohon maaf atas ketidaknyamanannya. Kami sedang ada perbaikan dan silahkan coba lagi nanti.</h2>
        </div>
    </div>
    <h1></h1>
    <p></p>
</body>

</html>