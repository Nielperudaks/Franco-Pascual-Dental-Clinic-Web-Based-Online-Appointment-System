<?php
session_start();
$is_invalid = false;
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
  $conn = require "../connection.php";
  $query = sprintf(
    "SELECT * FROM tbl_clients WHERE email = '%s'",
    $conn->real_escape_string($_POST["lEmail"])
  );
  $result = $conn->query($query);
  $validateUser = $result->fetch_assoc();
  if ($validateUser && $validateUser["AccountActivationHash"] === null) {
    if (password_verify($_POST["lPass"], $validateUser["Password"])) {
      session_start();
      session_regenerate_id();
      $_SESSION["userID"] = $validateUser["Client_ID"];
      $_SESSION['valid'] = true;
      header("Location: ../Client-Dashboard/");
      exit;
    }
  }
  $is_invalid = true;
}

if(isset($_SESSION['valid']) && $_SESSION['valid'] === true){
    header("Location: ../Client-Dashboard/");
} else if(isset($_SESSION['validAdmin']) && $_SESSION['validAdmin'] === 2 ){
    header("Location: ../Admin-Dashboard/");
} else if(isset($_SESSION['validAdmin']) && $_SESSION['validAdmin'] === 1 ){
    header("Location: ../Admin-Dashboard/Doctor-Section/");
} else if(isset($_SESSION['validAdmin']) && $_SESSION['validAdmin'] === 3 ){
    header("Location: ../Admin-Dashboard/Admin-Section/_secretary.php");
}

?>
<!doctype html>
<html lang="en">

<head>
    <title>FrancoPascual Dental</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="./" type="image/x-icon">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

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
    /* Base styles */
    .ftco-section {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    }

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

    /* Container and content */
    .container {
        position: relative;
        z-index: 2;
        margin-bottom: 15vh;
    }

    .login-wrap {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Form controls */
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

    /* Password toggle */
    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: rgba(255, 255, 255, 0.7);
    }

    /* Animated waves */
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

    /* Alert styling */
    .alert-danger {
        background: rgba(220, 53, 69, 0.2);
        border: 1px solid rgba(220, 53, 69, 0.3);
        color: #fff;
    }

    /* Responsive styles */
    @media (max-width: 768px) {
        .ftco-section::before {
            background-size: contain;
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

    /* Height-based adjustments */
    @media (min-height: 800px) {
        .ftco-section::before {
            background-size: cover;
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

    /* Ultra-wide screens */
    @media (min-width: 1400px) {
        .ftco-section::before {
            background-size: cover;
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

    /* Orientation handling */
    @media (orientation: portrait) {
        .ftco-section::before {
            background-size: contain;
            background-position: top center;
        }
    }

    @media (orientation: landscape) {
        .ftco-section::before {
            background-size: cover;
            background-position: center;
        }
    }

    /* Small screens */
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

    /* Dark mode */
    @media (prefers-color-scheme: dark) {
        .form-control {
            background: rgba(0, 0, 0, 0.2);
        }

        .form-control:focus {
            background: rgba(0, 0, 0, 0.3);
        }
    }

    /* Hover effects */
    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
    }

    #btnForgot:hover,
    #btnCreate:hover {
        opacity: 0.8;
        transition: opacity 0.3s ease;
    }
    </style>
    <script>
    $(document).ready(function() {


        $('#btnCreate').click(function() {

            location.href = 'register-page.php'

        });
        $('#btnForgot').click(function() {


            location.href = 'forgot-password-page.php'

        });


    });
    </script>

</head>

<body class="img js-fullheight" style="background-image: url(images/bg.svg);">
    <section class="ftco-section min-vh-100 position-relative overflow-hidden">
        <div class="container py-5">
            
            <div class="row justify-content-center mb-md-5 mb-4">
                <div class="col-12 col-md-8 col-lg-6 text-center">
                    <h1 class="display-4 text-light fw-bold mb-2"> Franco Pascual</h1>
                    <h2 class="h3 text-light fw-light">Dental Clinic</h2>
                </div>
            </div>

            
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-5">
                    <div class="login-wrap p-4 p-md-5 bg-dark-subtle rounded-3 shadow-lg">
                        <h3 class="mb-4 text-center">Have an account?</h3>

                        <?php if ($is_invalid) : ?>
                        <div class="alert alert-danger text-center mb-4">Invalid login</div>
                        <?php endif; ?>

                        <form class="signin-form" method="post">
                            <div class="form-group mb-3">
                                <input class="form-control form-control-lg" id="lEmail" name="lEmail" type="email"
                                    placeholder="Email" value="<?= htmlspecialchars($_POST["lEmail"] ?? "") ?>">
                            </div>

                            <div class="form-group mb-4 position-relative">
                                <input class="form-control form-control-lg" placeholder="Password" type="password"
                                    id="lPass" name="lPass">

                            </div>

                            <div class="form-group mb-4">
                                <button type="submit" class="btn btn-primary w-100 py-3">
                                    <span class="text-light">Sign In</span>
                                </button>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="forgot-password-page.php" id="btnForgot"
                                    class="text-light text-decoration-none">Forgot your
                                    password?</a>
                                <a href="register-page.php" id="btnCreate" class="text-light text-decoration-none">Create
                                    account</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="air air1"></div>
        <div class="air air2"></div>
        <div class="air air3"></div>
        <div class="air air4"></div>
    </section>


    <script src="js/jquery.min.js"></script>
    <script src="js/popper.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>

</body>

</html>