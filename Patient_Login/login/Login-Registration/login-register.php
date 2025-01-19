<?php
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
      header("Location: ../Client-Dashboard/index.php");
      exit;
    }
  }
  $is_invalid = true;
}
?>

<!DOCTYPE html>
<html lang="es" dir="ltr">

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/just-validate@latest/dist/just-validate.production.min.js" defer></script>
    <script src="scripts/validations.js" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
    $(document).ready(function() {

        $('.main').fadeIn(1000);
        $('.site-title').fadeIn(3000);

    });
    </script>
    <style>
    h1 {

        font-family: "Roboto", sans-serif;
        color: #4A90E2;
        z-index: 1;
        text-align: start;
    }

    .site-title {
        font-size: 400px;
        font-weight: 500px;
        text-transform: uppercase;
        color: transparent;
        -webkit-text-stroke: 2px white;

        opacity: 1;
        animation: moveAndFade 20s ease-in-out infinite;
        white-space: nowrap;

    }

    .main {
        position: absolute;
        width: 1000px;
        min-width: 1000px;
        min-height: 600px;
        height: 600px;
        padding: 25px;
        overflow: hidden;

    }

    /* Container to restrict overflow */
    .title-container {
        overflow: hidden;
        width: 100%;
        /* Make sure it spans the viewport */
    }

    

    /* Define the animation sequence */
    @keyframes moveAndFade {
        0% {
            opacity: 0;
            transform: translateX(0);
        }

        25% {
            opacity: 1;
            transform: translateX(50px);
            /* Move to the right */
        }

        50% {
            opacity: 1;
            /* Fade out */
            transform: translateX(50px);
        }

        75% {
            opacity: 0;
            /* Fade out */
            transform: translateX(50px);
        }

        100% {
            opacity: 0;
            /* Fade back in */
            transform: translateX(0);
        }
    }
    </style>
</head>

<body>
    <section>

        <div class="main" hidden>

            <div class="container a-container" id="a-container">
                <form class="form" id="a-form" action="send-activation-email.php" method="post" novalidate="novalidate">
                    <h2 class="form_title title">Create Account</h2>
                    <div>
                        <input class="form__input" type="text" placeholder="First Name" name="firstName" id="firstName">
                    </div>
                    <div>
                        <input class="form__input" type="text" placeholder="Last Name" name="lastName" id="lastName">
                    </div>
                    <div>
                        <input class="form__input" type="email" placeholder="Email" name="email" id="email">
                    </div>
                    <div>
                        <input class="form__input" type="password" placeholder="Password" name="password" id="password">
                    </div>
                    <div>
                        <input class="form__input" type="password" placeholder="Re-enter Password" name="rPassword"
                            id="rPassword">
                    </div>
                    <button class="form__button button" id="signup" type="submit">SIGN UP</button>
                </form>
            </div>

            <div class="container b-container" id="b-container">
                <form class="form" id="b-form" method="post">
                    <h2 class="form_title title">Sign in to Website</h2>
                    <?php if ($is_invalid) : ?>
                    <em>Invalid login</em>
                    <?php endif; ?>
                    <span class="form__span">or use your email account</span>
                    <input class="form__input" id="lEmail" name="lEmail" type="email" placeholder="Email"
                        value="<?= htmlspecialchars($_POST["lEmail"] ?? "") ?>">
                    <input class="form__input" type="password" id="lPass" name="lPass" placeholder="Password">
                    <a href="forgot-password-page.php" class="form__link">Forgot your password?</a>
                    <button class="form__button button" type="submit" id="submit" name="submit">SIGN IN</button>
                </form>
            </div>
            <div class="switch" id="switch-cnt">
                <div class="switch__circle"></div>
                <div class="switch__circle switch__circle--t"></div>
                <div class="switch__container" id="switch-c1">
                    <h2 class="switch__title title">Welcome Back !</h2>
                    <p class="switch__description description">To keep connected with us please login with your personal
                        info</p>
                    <button class="switch__button button switch-btn">SIGN IN</button>
                </div>
                <div class="switch__container is-hidden" id="switch-c2">
                    <h2 class="switch__title title">Hello Friend !</h2>
                    <p class="switch__description description">Enter your personal details and start journey with us</p>
                    <button class="switch__button button switch-btn">SIGN UP</button>
                </div>
            </div>
        </div>
        <div class="title-container">
            <h1 class="site-title">Franco</h1>
            <h1 class="site-title">Pascual</h1>
        </div>
        <div class='air air1'></div>
        <div class='air air2'></div>
        <div class='air air3'></div>
        <div class='air air4'></div>
    </section>

    <script src="scripts/login-script.js"></script>

</body>

</html>