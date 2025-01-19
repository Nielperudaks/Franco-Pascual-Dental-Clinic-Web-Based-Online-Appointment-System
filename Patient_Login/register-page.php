<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate a secure random token
}
?>

<!doctype html>
<html lang="en">

<head>
    <title>FrancoPascual Dental</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/just-validate@latest/dist/just-validate.production.min.js" defer></script>
    <script src="scripts/email-checker.js" defer></script>

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
    <script>
    $(document).ready(function() {

        // Toggle password visibility
        $('.toggle-password').click(function() {
            const passwordField = $('#password');
            const rPasswordField = $('#rPassword');
            const type = passwordField.attr('type') === 'password' ? 'text' : 'password';

            passwordField.attr('type', type);
            rPasswordField.attr('type', type);
            $(this).toggleClass('fa-eye fa-eye-slash');
        });
        $('#btn-signIn').click(function() {
            location.href = 'index.php'

        });
        // Add maxlength attribute to all input fields
        $('input[type="text"], input[type="password"], input[type="email"], textarea').attr('maxlength', '50');

        // Real-time validation for all input fields
        $('input[type="text"], input[type="password"], input[type="email"], textarea').on('input', function() {
            const maxLength = 50;
            const currentLength = $(this).val().length;
            const remainingChars = maxLength - currentLength;

            // Trim the input if it somehow exceeds 50 characters
            if (currentLength > maxLength) {
                $(this).val($(this).val().substring(0, maxLength));
            }

            // Show feedback to user
            // Check if feedback element exists, if not create it
            let feedbackId = $(this).attr('id') + '-feedback';
            if ($('#' + feedbackId).length === 0) {
                $(this).after('<small id="' + feedbackId + '" class="text-muted"></small>');
            }

            // Update feedback message
            if (currentLength > 0) {
                $('#' + feedbackId).text(`${remainingChars} characters remaining`);

                // Change text color when approaching limit
                if (remainingChars <= 5) {
                    $('#' + feedbackId).removeClass('text-muted').addClass('text-danger');
                } else {
                    $('#' + feedbackId).removeClass('text-danger').addClass('text-muted');
                }
            } else {
                $('#' + feedbackId).text(''); // Clear feedback if input is empty
            }
        });

        // Prevent paste of content longer than 50 characters
        $('input[type="text"], input[type="password"], input[type="email"], textarea').on('paste', function(e) {
            // Get pasted data via clipboard API
            let pastedData = e.originalEvent.clipboardData || window.clipboardData;
            let pastedText = pastedData.getData('Text');

            if (pastedText.length > 50) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Too Long!',
                    text: 'Pasted text exceeds the maximum length of 50 characters.',
                    confirmButtonColor: '#3085d6',
                });
            }
        });

        // Optional: Add validation before form submission
        $('form').on('submit', function(e) {
            let invalidInputs = $(this).find('input, textarea').filter(function() {
                return $(this).val().length > 50;
            });

            if (invalidInputs.length > 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Some fields exceed the maximum length of 50 characters. Please check your inputs.',
                    confirmButtonColor: '#5085d6',
                });

                // Highlight invalid inputs
                invalidInputs.addClass('is-invalid');
            }
        });

        ///////////////////////////////////validations          ////////

        // Regular expression for valid name characters
        const nameRegex = /^[A-Za-zÀ-ÖØ-öø-ÿ\s'-]+$/;

        // Function to validate name input
        function validateNameInput(input) {
            const $input = $(input);
            const $feedback = $input.siblings('.invalid-feedback');

            // Remove invalid characters immediately
            $input.on('input', function(e) {
                let cursorPosition = e.target.selectionStart;
                let newValue = $(this).val().replace(/[^A-Za-zÀ-ÖØ-öø-ÿ\s'-]/g, '');

                if (newValue !== $(this).val()) {
                    $(this).val(newValue);
                    // Maintain cursor position
                    e.target.setSelectionRange(cursorPosition - 1, cursorPosition - 1);
                }
            });

            // Validation on blur
            $input.on('blur', function() {
                const value = $(this).val().trim();

                if (value === '') {
                    if ($input.prop('required')) {
                        setInvalid($(this), 'This field is required');
                    }
                } else if (!nameRegex.test(value)) {
                    setInvalid($(this), 'Please enter a valid name');
                } else if (value.length < 2) {
                    setInvalid($(this), 'Name must be at least 2 characters long');
                } else if (value.length > 50) {
                    setInvalid($(this), 'Name cannot exceed 50 characters');
                } else if (/([-'])\1+/.test(value)) {
                    setInvalid($(this), 'Special characters cannot be consecutive');
                } else if (/^[-']|[-']$/.test(value)) {
                    setInvalid($(this), 'Name cannot start or end with special characters');
                } else if (value[1] === '-' || value[1] === "'") {
                    setInvalid($(this), 'Second character cannot be a dash or apostrophe');
                } else {
                    setValid($(this));
                }
            });

            // Real-time validation feedback
            $input.on('input', function() {
                const $feedback = $(this).siblings('.invalid-feedback');
                if ($feedback.is(':visible')) {
                    $(this).trigger('blur');
                }
            });
        }

        // Helper function to set invalid state
        function setInvalid($input, message) {
            $input.removeClass('is-valid').addClass('is-invalid');
            let $feedback = $input.siblings('.invalid-feedback');
            if ($feedback.length === 0) {
                $feedback = $('<div class="invalid-feedback"></div>').insertAfter($input);
            }
            $feedback.text(message);
        }

        // Helper function to set valid state
        function setValid($input) {
            $input.removeClass('is-invalid').addClass('is-valid');
            const $feedback = $input.siblings('.invalid-feedback');
            if ($feedback.length > 0) {
                $feedback.remove();
            }
        }

        // Initialize validation for name inputs
        ['#lastName', '#firstName'].forEach(selector => {
            validateNameInput(selector);
        });

        // Form submission handling
        $('form').on('submit', function(e) {
            let isValid = true;

            // Validate all name fields before submission
            ['#lastName', '#firstName'].forEach(selector => {
                const $input = $(selector);
                $input.trigger('blur');
                if ($input.hasClass('is-invalid')) {
                    isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault();
                // Scroll to the first invalid input
                const $firstInvalid = $('.is-invalid').first();
                if ($firstInvalid.length) {
                    $('html, body').animate({
                        scrollTop: $firstInvalid.offset().top - 100
                    }, 200);
                }
            }
        });
    });
    </script>
</head>

<body class="img js-fullheight" style="background-image: url(images/bg.svg);">
    <section class="ftco-section min-vh-100 position-relative overflow-hidden">
        <div class="container py-5">
            
            <div class="row justify-content-center mb-md-5 mb-4">
                <div class="col-12 col-md-8 col-lg-6 text-center">
                    <h1 class="display-4 text-light fw-light mb-2">Franco Pascual</h1>
                    <h2 class="h3 text-light fw-light">Dental Clinic</h2>
                </div>
            </div>

            
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-5">
                    <div class="login-wrap p-4 p-md-5 bg-dark-subtle rounded-3 shadow-lg">
                        <h3 class="mb-4 text-center">Create an account?</h3>

                        <form class="form" action="send-activation-email.php" id="a-form" method="post"
                            novalidate="novalidate">
                            
                            <div class="form-group mb-3">
                                <input class="form-control form-control-lg" type="text" placeholder="First Name"
                                    name="firstName" id="firstName">
                            </div>

                            <div class="form-group mb-3">
                                <input class="form-control form-control-lg" type="text" placeholder="Last Name"
                                    name="lastName" id="lastName">
                            </div>

                            <div class="form-group mb-3">
                                <input class="form-control form-control-lg" type="email" placeholder="Email"
                                    name="email" id="email">
                            </div>

                            <div class="form-group mb-3 position-relative">
                                <input class="form-control form-control-lg" type="password" placeholder="Password"
                                    name="password" id="password">

                            </div>

                            <div class="form-group mb-4 position-relative">
                                <input class="form-control form-control-lg" type="password"
                                    placeholder="Re-enter Password" name="rPassword" id="rPassword">

                            </div>

                            <div class="form-group mb-4">
                                <button id="signup" type="submit" class="btn btn-primary w-100 py-3">
                                    <span class="text-light">Register</span>
                                </button>
                            </div>

                            <div class="text-center">
                                <a class="text-light text-decoration-none" id="btn-signIn">
                                    Already have an account? <span class="text-light">Sign In</span>
                                </a>
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