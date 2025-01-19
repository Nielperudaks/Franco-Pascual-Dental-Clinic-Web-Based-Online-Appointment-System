<!doctype html>
<html lang="en">

<head>
    <title>Franco-Pascual Dental Clinic</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4@4/bootstrap-4.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <link rel="stylesheet" href="css/style.css">
    <style>
    * {
        margin: 0;
        padding: 0;
    }



    section .air {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100px;
        background: url(https://1.bp.blogspot.com/-xQUc-TovqDk/XdxogmMqIRI/AAAAAAAACvI/AizpnE509UMGBcTiLJ58BC6iViPYGYQfQCLcBGAsYHQ/s1600/wave.png);
        background-size: 1000px 100px
    }

    section .air.air1 {
        animation: wave 30s linear infinite;
        z-index: 1000;
        opacity: 1;
        animation-delay: 0s;
        bottom: 0;
    }

    section .air.air2 {
        animation: wave2 15s linear infinite;
        z-index: 999;
        opacity: 0.5;
        animation-delay: -5s;
        bottom: 10px;
    }

    section .air.air3 {
        animation: wave 30s linear infinite;
        z-index: 998;
        opacity: 0.2;
        animation-delay: -2s;
        bottom: 15px;
    }

    section .air.air4 {
        animation: wave2 5s linear infinite;
        z-index: 997;
        opacity: 0.7;
        animation-delay: -5s;
        bottom: 20px;
    }

    @keyframes wave {
        0% {
            background-position-x: 0px;
        }

        100% {
            background-position-x: 1000px;
        }
    }

    @keyframes wave2 {
        0% {
            background-position-x: 0px;
        }

        100% {
            background-position-x: -1000px;
        }
    }
    </style>
    <style>
    /* Base styles with improved background handling */
    .ftco-section {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    }

    /* Background SVG styling */
    .ftco-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url(images/bg.svg);
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        opacity: 0.7;
        z-index: 1;
    }

    /* Container positioning */
    .container {
        position: relative;
        z-index: 2;
        margin-bottom: 15vh;
    }

    /* Form styling */
    .login-wrap {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .form-control {
        background: rgba(255, 255, 255, 0.1);
        border: none;
        color: white;
        padding: 0.8rem 1rem;
    }

    .form-control:focus {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        box-shadow: none;
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: rgba(255, 255, 255, 0.7);
    }

    /* Wave animations */
    .air {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        background: url(https://1.bp.blogspot.com/-xQUc-TovqDk/XdxogmMqIRI/AAAAAAAACvI/AizpnE509UMGBcTiLJ58BC6iViPYGYQfQCLcBGAsYHQ/s1600/wave.png);
        background-size: 1000px 100%;
        animation: wave 30s linear infinite;
        pointer-events: none;
        z-index: 1;
    }

    .air1 {
        height: 25vh;
        opacity: 0.3;
        animation-delay: 0s;
    }

    .air2 {
        height: 23vh;
        opacity: 0.2;
        animation-delay: -5s;
    }

    .air3 {
        height: 21vh;
        opacity: 0.1;
        animation-delay: -10s;
    }

    .air4 {
        height: 19vh;
        opacity: 0.05;
        animation-delay: -15s;
    }

    @keyframes wave {
        0% {
            background-position-x: 0;
        }

        100% {
            background-position-x: 1000px;
        }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .ftco-section::before {
            background-size: contain;
            /* Adjust background SVG for mobile */
        }

        .login-wrap {
            padding: 2rem !important;
            margin: 0 1rem;
        }

        h1.display-4 {
            font-size: 2rem;
        }

        .h3 {
            font-size: 1.5rem;
        }

        /* Adjust waves for mobile */
        .air1 {
            height: 20vh;
        }

        .air2 {
            height: 18vh;
        }

        .air3 {
            height: 16vh;
        }

        .air4 {
            height: 14vh;
        }
    }

    /* Height-based responsive adjustments */
    @media (min-height: 800px) {
        .ftco-section::before {
            background-size: cover;
            /* Adjust background SVG for taller screens */
        }

        .air1 {
            height: 20vh;
        }

        .air2 {
            height: 18vh;
        }

        .air3 {
            height: 16vh;
        }

        .air4 {
            height: 14vh;
        }
    }

    @media (min-height: 1000px) {
        .air1 {
            height: 15vh;
        }

        .air2 {
            height: 14vh;
        }

        .air3 {
            height: 13vh;
        }

        .air4 {
            height: 12vh;
        }
    }

    /* Ultra-wide screen handling */
    @media (min-width: 1400px) {
        .ftco-section::before {
            background-size: cover;
            /* Ensure SVG covers ultra-wide screens */
        }

        .air {
            background-size: 2000px 100%;
        }

        @keyframes wave {
            0% {
                background-position-x: 0;
            }

            100% {
                background-position-x: 2000px;
            }
        }
    }

    /* Portrait orientation */
    @media (orientation: portrait) {
        .ftco-section::before {
            background-size: contain;
            background-position: top center;
        }
    }

    /* Landscape orientation */
    @media (orientation: landscape) {
        .ftco-section::before {
            background-size: cover;
            background-position: center;
        }
    }

    /* Extra small height screens */
    @media (max-height: 600px) {
        .ftco-section::before {
            background-size: contain;
        }

        .air1 {
            height: 30vh;
        }

        .air2 {
            height: 28vh;
        }

        .air3 {
            height: 26vh;
        }

        .air4 {
            height: 24vh;
        }
    }

    /* Dark mode adjustments */
    @media (prefers-color-scheme: dark) {
        .form-control {
            background: rgba(0, 0, 0, 0.2);
        }

        .form-control:focus {
            background: rgba(0, 0, 0, 0.3);
        }
    }
    </style>




</head>

<body class="img js-fullheight" style="background-image: url(images/bg.svg);">
    <section class="ftco-section min-vh-100 position-relative overflow-hidden">
        <div class="container py-5">
            <!-- Header Section -->
            <div class="row justify-content-center mb-md-5 mb-4">
                <div class="col-12 col-md-8 col-lg-6 text-center">
                    <h1 class="display-4 text-light fw-light mb-2">Franco Pascual</h1>
                    <h2 class="h3 text-light fw-light">Dental Clinic</h2>
                </div>
            </div>

            <!-- Form Section -->
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-5">
                    <div class="login-wrap p-4 p-md-5 bg-dark-subtle rounded-3 shadow-lg">
                        <h3 class="mb-4 text-center">Password forgotten?</h3>
                        <form class="form" id="a-form" method="post" action="send-password-reset.php"
                            novalidate="novalidate">
                            <div class="form-group mb-4 position-relative">
                                <input class="form-control form-control-lg" type="email" placeholder="Email"
                                    name="email" id="email">
                            </div>
                            <div class="form-group mb-4">
                                <button type="submit" id="signup" class="btn btn-primary w-100 py-3">
                                    <span class="text-light">Send Verification</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </section>

    <script src="js/jquery.min.js"></script>
    <script src="js/popper.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>

</body>
</html>
