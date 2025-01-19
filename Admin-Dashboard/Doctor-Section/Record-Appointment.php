<?php
session_start();

// Check if session user ID is not set
if (!isset($_SESSION["userID"])) {
    header("Location: ../Login-Registration/");
    exit();
}

$conn = require __DIR__ . "../../../connection.php";

$query = "SELECT * FROM tbl_admin WHERE Admin_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION["userID"]);
$stmt->execute();
$result = $stmt->get_result();
$validateUser = $result->fetch_assoc();

// Check if user is not found or doesn't have correct access level
if (!$validateUser || $validateUser['Access_Level'] != 1) {
    header("Location: ../Login-Registration/");
    exit();
}

$encryptionKey = '09292222';

// Function to decrypt data
function decryptData($encryptedData, $key)
{
    $iv = substr(md5($key), 0, 16);  // Use a fixed IV for consistency
    $decrypted = openssl_decrypt(base64_decode($encryptedData), 'AES-256-CBC', $key, 0, $iv);
    return $decrypted !== false ? $decrypted : '';
}

// Get encrypted values from the URL parameters
$encryptedClientID = $_GET['cid'] ?? '';
$encryptedTransactionID = $_GET['tid'] ?? '';
$encryptedFirstName = $_GET['name'] ?? '';

// Decrypt each value
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $client_id = decryptData($encryptedClientID, $encryptionKey);
    $transactionID = decryptData($encryptedTransactionID, $encryptionKey);
    $firstName = decryptData($encryptedFirstName, $encryptionKey);

    $query = "SELECT * FROM tbl_clients WHERE Client_ID = $client_id";
    $result = $conn->query($query);
    $clientDetails = $result->fetch_assoc();

    $query = "SELECT Teeth, Doctor, IsPriority FROM tbl_transaction WHERE Transaction_Code = '$transactionID'";
    $result = $conn->query($query);
    $teethnum = $result->fetch_assoc();
    // echo htmlspecialchars($transactionID) . "<br>";
    // echo $teethnum["Teeth"]. "<br>";
    $teethnum["Teeth"] = array_map('intval', explode(',', $teethnum["Teeth"]));
    $teethArray = json_encode($teethnum["Teeth"]);

    $doctor = $teethnum["Doctor"];
    $priority = $teethnum["IsPriority"];
    $teethString = implode(",", $teethnum["Teeth"]);

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">
    <meta name="author" content="AdminKit">
    <meta name="keywords"
        content="adminkit, bootstrap, bootstrap 5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="canonical" href="https://demo-basic.adminkit.io/" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.tiny.cloud/1/ubg63rs3fsu2nhbko5duyq8jc65aixeut5x6yocylewwb5cw/tinymce/7/tinymce.min.js"
        referrerpolicy="origin"></script>

    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4@4/bootstrap-4.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>



    <title>Admin Dashboard</title>

    <link href="../css/app.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
    #loading-screen {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.95);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .spinner {
        width: 60px;
        height: 60px;
        margin-bottom: 20px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    .progress-container {
        width: 200px;
        background-color: #f1f1f1;
        border-radius: 10px;
        padding: 3px;
        margin-bottom: 10px;
    }

    .progress-bar {
        width: 0%;
        height: 20px;
        background-color: #3498db;
        border-radius: 8px;
        transition: width 0.3s ease-in-out;
    }

    .loading-text {
        color: #333;
        font-family: Arial, sans-serif;
        font-size: 16px;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
    </style>

    <style>
    #teeth-container {
        background-color: transparent;
        /* Make sure it's transparent */
        background-image: url('bg.png');
    }
    </style>
    <style>
    /* Tooltip CSS */
    #tooltip {
        position: absolute;
        background-color: rgba(255, 255, 255, 0.9);
        /* Slight background */
        border: 1px solid #ccc;
        padding: 10px;
        border-radius: 4px;
        font-size: 14px;
        color: #333;
        z-index: 10;
    }

    .tooth-cell {
        padding: 10px;
        text-align: center;
        border: 1px solid #ccc;
        cursor: pointer;
    }

    #center {
        border-left: 1px solid #333;
    }

    .tooth-cell.hovered {
        background-color: yellow;
    }

    .tooth-cell.selected {
        background-color: #F35D5D;
        color: whitesmoke;
    }




    #selectAllButton {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 10;
        padding: 10px;
        background-color: whitesmoke;
        /* Green background */
        color: gray;
        /* White text */
        border: none;
        cursor: pointer;
        border-radius: 5px;

    }

    #selectAllButton:hover {
        background-color: white;
        /* Darker green on hover */
    }

    #camera-controls {
        position: absolute;
        top: 20px;
        /* 20px from the top of the container */
        left: 20px;
        /* 20px from the left of the container */
        z-index: 10;
        /* Ensures the buttons are on top of the model */
    }


    /* ===================== 3D MODEL CSS ======================= */
    #selectAllButton {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 10;
        padding: 10px;
        background-color: whitesmoke;
        /* Green background */
        color: gray;
        /* White text */
        border: none;
        cursor: pointer;
        border-radius: 5px;

    }

    #selectAllButton:hover {
        background-color: white;
        /* Darker green on hover */
    }

    #camera-controls {
        position: absolute;
        top: 20px;
        /* 20px from the top of the container */
        left: 20px;
        /* 20px from the left of the container */
        z-index: 10;
        /* Ensures the buttons are on top of the model */
    }

    #camera-controls button {

        margin-bottom: 10px;
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        border: none;
        cursor: pointer;
    }

    #camera-controls button:hover {
        background-color: #0056b3;
    }

    /* Card Container Styles */
    #3d-teeth {
        display: flex;
        flex-direction: column;
        height: auto;
        /* Allow height to adjust dynamically */
        min-height: 400px;
        /* Minimum height to prevent extreme shrinkage */
        max-height: none;
        /* Remove max-height restriction */
        width: 100%;
        /* Full width of parent container */
    }

    #3d-teeth .card-body {
        flex-grow: 1;
        /* Allow body to expand and fill available space */
        display: flex;
        flex-direction: column;
    }

    #teeth-container {
        flex-grow: 1;
        /* Allow container to expand within card body */
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 250px;
        /* Minimum height for 3D model area */
        position: relative;
        /* For absolute positioning of tooltip */
    }

    #toothTable {
        width: 100%;
        /* Full width of container */
        margin-top: 15px;
        /* Space between 3D model and tooth table */
        border-collapse: collapse;
    }

    /* Responsive adjustments for smaller screens */
    @media (max-width: 768px) {
        #3d-teeth {
            min-height: 300px;
        }

        #teeth-container {
            min-height: 200px;
        }

        #toothTable {
            font-size: 0.9em;
            /* Slightly smaller font for compact displays */
        }

        .tooth-cell {
            padding: 5px;
            /* Reduce padding on smaller screens */
        }
    }

    /* Ensure select/deselect button is centered and styled responsively */
    #selectAllButton {
        margin-top: 10px;
        align-self: center;
        display: flex;
        align-items: center;
        justify-content: center;
    }


    /* Teeth Container Styles */
    #teeth-container {
        width: 100%;
        max-width: 800px;
        /* Fixed maximum width */
        height: auto;
        aspect-ratio: 16 / 9;
        /* Maintains a consistent aspect ratio */
        margin: 0 auto;
        /* Centers the container */
        position: relative;
        overflow: hidden;
    }

    /* Tooltip Styles */
    #tooltip {
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 14px;
        z-index: 1000;
    }

    /* Select/Deselect Button Styles */
    #selectAllButton {
        margin: 10px 0;
        padding: 8px 16px;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    #selectAllButton:hover {
        background-color: #e9ecef;
    }

    /* Teeth Table Styles */
    #toothTable {
        width: 100%;
        margin-top: 20px;
        border-collapse: separate;
        border-spacing: 2px;
        table-layout: fixed;
    }

    .tooth-cell {
        text-align: center;
        padding: 8px 4px;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .tooth-cell:hover {
        background-color: #e9ecef;
    }

    .tooth-cell.selected {
        background-color: #FF5555;
        color: white;
    }

    /* Responsive Breakpoints */
    @media only screen and (max-width: 1200px) {
        #teeth-container {
            max-width: 100%;
        }
    }

    @media only screen and (max-width: 992px) {
        .tooth-cell {
            padding: 6px 2px;
            font-size: 14px;
        }
    }

    @media only screen and (max-width: 768px) {
        #toothTable {
            margin-top: 15px;
        }

        .tooth-cell {
            padding: 4px 1px;
            font-size: 12px;
        }

        #teeth-container {
            aspect-ratio: auto;
            /* Disable fixed aspect ratio */
            height: 40vh;
            /* Set height to 60% of the viewport height */
        }
    }

    @media only screen and (max-width: 576px) {

        /* Stack the teeth rows for very small screens */
        #toothTable {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
        }

        #upperTeethRow,
        #lowerTeethRow {
            display: table-row;
        }

        .tooth-cell {
            display: table-cell;
            min-width: 30px;
            padding: 4px 2px;
            font-size: 11px;
        }

        #teeth-container {
            height: 60vh;
            /* Increase height for smaller screens */
        }

        #selectAllButton {
            width: 100%;
            justify-content: center;
        }
    }

    /* Optional: Add smooth scaling for the 3D model */
    #teeth-container canvas {
        width: 100% !important;
        height: auto !important;
        max-height: 600px;
    }

    /* Handle landscape orientation on mobile */
    @media only screen and (max-width: 896px) and (orientation: landscape) {
        #teeth-container {
            min-height: 180px;
        }

        .card-body {
            padding: 10px;
        }

        #toothTable {
            margin-top: 10px;
        }
    }

    /* Ensure proper spacing in the card */
    .card-body {
        padding: 1.25rem;
    }

    .card-header {
        padding: 1rem 1.25rem;
    }

    /* Add smooth transitions */
    .tooth-cell {
        transition: all 0.2s ease-in-out;
    }

    /* Improve touch targets on mobile */
    @media (hover: none) and (pointer: coarse) {
        .tooth-cell {
            min-height: 44px;
        }

        #selectAllButton {
            min-height: 44px;
        }
    }
    </style>
    <script>
    $(document).ready(function() {


        const bloodTypeRegex = /^(A|B|AB|O)[+-]$/;

        $('#bloodType').on('input', function(e) {
            let value = $(this).val().toUpperCase();
            // Remove any characters that aren't letters or + or -
            value = value.replace(/[^ABO+-]/g, '');
            $(this).val(value);

            validateBloodType($(this));
        });

        function validateBloodType($input) {
            const value = $input.val().trim().toUpperCase();

            if (value === '' && $input.prop('required')) {
                setInvalid($input, 'Blood type is required');
            } else if (value !== '' && !bloodTypeRegex.test(value)) {
                setInvalid($input, 'Please enter a valid blood type (A+, A-, B+, B-, AB+, AB-, O+, O-)');
            } else {
                setValid($input);
            }
        }

        // Blood Pressure Validation
        const bloodPressureRegex = /^\d{2,3}\/\d{2,3}$/;

        $('#bloodPressure').on('input', function(e) {
            let value = $(this).val();
            // Only allow numbers and forward slash
            value = value.replace(/[^\d/]/g, '');
            // Ensure only one forward slash
            if ((value.match(/\//g) || []).length > 1) {
                value = value.substring(0, value.lastIndexOf('/'));
            }
            $(this).val(value);

            validateBloodPressure($(this));
        });

        function validateBloodPressure($input) {
            const value = $input.val().trim();

            if (value === '' && $input.prop('required')) {
                setInvalid($input, 'Blood pressure is required');
                return;
            }

            if (value !== '' && !bloodPressureRegex.test(value)) {
                setInvalid($input, 'Please enter blood pressure in format: 120/80');
                return;
            }

            const [systolic, diastolic] = value.split('/').map(Number);

            if (systolic < 60 || systolic > 250) {
                setInvalid($input, 'Systolic pressure should be between 60 and 250');
                return;
            }

            if (diastolic < 40 || diastolic > 150) {
                setInvalid($input, 'Diastolic pressure should be between 40 and 150');
                return;
            }

            setValid($input);
        }

        // Phone Number Validation
        $('#phone-number').on('input', function(e) {
            let value = $(this).val().replace(/\D/g, ''); // Remove non-digits

            // Ensure the value starts with '09'
            if (value.length >= 2 && !value.startsWith('09')) {
                value = '09' + value.substring(2);
            }

            // Limit to 11 digits
            if (value.length > 11) {
                value = value.substring(0, 11);
            }

            $(this).val(value);
            validatePhoneNumber($(this));
        });

        function validatePhoneNumber($input) {
            const value = $input.val().trim();

            if (value === '' && $input.prop('required')) {
                setInvalid($input, 'Phone number is required');
            } else if (value !== '' && value.length !== 11) {
                setInvalid($input, 'Phone number must be 11 digits');
            } else if (value !== '' && !value.startsWith('09')) {
                setInvalid($input, 'Phone number must start with 09');
            } else {
                setValid($input);
            }
        }

        // Helper functions for validation states
        function setInvalid($input, message) {
            $input.removeClass('is-valid').addClass('is-invalid');
            let $feedback = $input.siblings('.invalid-feedback');
            if ($feedback.length === 0) {
                $feedback = $('<div class="invalid-feedback"></div>').insertAfter($input);
            }
            $feedback.text(message);
        }

        function setValid($input) {
            $input.removeClass('is-invalid').addClass('is-valid');
            const $feedback = $input.siblings('.invalid-feedback');
            if ($feedback.length > 0) {
                $feedback.remove();
            }
        }

        // Validate on blur
        $('#bloodType, #bloodPressure, #phone-number').on('blur', function() {
            if ($(this).attr('id') === 'bloodType') {
                validateBloodType($(this));
            } else if ($(this).attr('id') === 'bloodPressure') {
                validateBloodPressure($(this));
            } else if ($(this).attr('id') === 'phone-number') {
                validatePhoneNumber($(this));
            }
        });

        // Form submission validation
        $('form').on('submit', function(e) {
            let isValid = true;

            // Trigger validation for all fields
            $('#bloodType').trigger('blur');
            $('#bloodPressure').trigger('blur');
            $('#phone-number').trigger('blur');

            // Check if any field is invalid
            if ($('.is-invalid').length > 0) {
                isValid = false;
                e.preventDefault();
                // Scroll to first invalid input
                $('html, body').animate({
                    scrollTop: $('.is-invalid:first').offset().top - 100
                }, 200);
            }

            return isValid;
        });


        $('#birthday').on('blur', function() {
            var birthday = $(this).val();

            // Allow the field to be blank
            if (birthday === "") {
                return;
            }

            // Regular expression for YYYY-MM-DD format
            var datePattern = /^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/;

            // Check if the input matches the format
            if (!datePattern.test(birthday)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Date Format',
                    text: 'Please enter date in the format: YYYY-MM-DD. For example, 2003-05-04.',
                });

                // Clear the input if it's in the wrong format
                $(this).val('');
            }
        });
        $('#last-visit').on('blur', function() {
            var lastVisit = $(this).val();

            // Allow the field to be blank
            if (lastVisit === "") {
                return;
            }

            // Regular expression for YYYY-MM-DD format
            var datePattern = /^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/;

            // Check if the input matches the format
            if (!datePattern.test(lastVisit)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Date Format',
                    text: 'Please enter date in the format: YYYY-MM-DD. For example, 2003-05-24.',
                });

                // Clear the input if it's in the wrong format
                $(this).val('');
            }
        });

        const nameRegex = /^[A-Za-zÀ-ÖØ-öø-ÿ\s'-]+$/;

        // Function to validate name input
        function validateNameInput(input) {
            const $input = $(input);
            const $feedback = $input.siblings('.invalid-feedback');
            const value = $input.val().trim();

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
        ['#last-name', '#first-name', '#middle-name', '#physician-name', '#prev-dentist', '#religion',
            '#nationality'
        ].forEach(selector => {
            validateNameInput(selector);
        });

        // Form submission handling
        $('form').on('submit', function(e) {
            let isValid = true;

            // Validate all name fields before submission
            ['#last-name', '#first-name', '#middle-name', '#physician-name', '#prev-dentist',
                '#religion', '#nationality'
            ].forEach(selector => {
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


        highlightTeethFromArray(<?php echo $teethArray ?>);
        console.log(<?php echo $teethArray ?>);

        $('#cancel').click(function() {
            $('#confirm-modal').modal('hide');
        });

        $('#btnProceed').click(function() {
            var selectedOption = $('#service-select').find('option:selected');
            const selectedService = selectedOption.text();

            // Check if the selected service is the default or first option
            if (!selectedService || selectedService === 'Select a Service') {
                // Show SweetAlert error message
                Swal.fire({
                    icon: 'warning',
                    title: 'Service Selection Required',
                    text: 'Please choose a service before proceeding.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                    didOpen: () => {
                        // Optionally highlight the select element
                        serviceSelect.addClass('is-invalid');
                    },
                    didClose: () => {
                        // Remove highlight when alert is closed
                        serviceSelect.removeClass('is-invalid');
                    }
                });

                // Prevent further processing
                return false;
            }

            Swal.fire({
                title: 'Confirmation',
                text: "Are you already done providing service?",
                icon: 'question',
                confirmButtonText: 'Yes, Proceed to payment!',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                reverseButtons: true

            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = {
                        lastName: $('#last-name').val(),
                        firstName: $('#first-name').val(),
                        middleName: $('#middle-name').val(),
                        address: $('#h-address').val(),
                        occupation: $('#occupation').val(),
                        phoneNumber: $('#phone-number').val(),
                        birthday: $('#birthday').val(),
                        sex: $('select.form-select').val(),
                        religion: $('#religion').val(),
                        nationality: $('#nationality').val(),
                        officeAddress: $('#o-address').val(),
                        dentalInsurance: $('#insurance').val(),
                        previousDentist: $('#prev-dentist').val(),
                        physicianName: $('#nationality').val(),
                        specialty: $('#o-address').val(),
                        bloodType: $('#insurance').val(),
                        bloodPressure: $('#prev-dentist').val(),
                        lastVisit: $('#last-visit').val(),
                        healthStatus: $('input[name="healthStatus"]:checked').val(),
                        medicalStatus: $('input[name="treatmentStatus"]:checked').val(),
                        conditionStatus: $('input[name="surgicalStatus"]:checked')
                            .val(),
                        viceStatus: $('input[name="viceStatus"]:checked').val(),
                        allergies: $('#allergy-select').val().join(' '),
                        illness: $('#illness-select').val().join(' '),
                        clientID: <?php echo $client_id; ?>,
                        transactionCode: '<?php echo $transactionID; ?>'
                    };

                    console.log($('input[name="healthStatus"]:checked').val());
                    console.log($('input[name="treatmentStatus"]:checked').val());
                    console.log($('input[name="surgicalStatus"]:checked').val());
                    console.log($('input[name="viceStatus"]:checked').val());

                    // Send data to PHP using AJAX
                    $.ajax({
                        type: 'POST',
                        url: 'update-records.php',
                        data: formData,
                        success: function(response) {
                            $('#confirm-modal').modal('hide');

                            showToast(
                                response
                            ); // Handle response from the PHP file
                        },
                        error: function() {
                            alert(
                                'An error occurred while updating the records.'
                            );
                        }
                    });
                    var selectedOption = $('#service-select').find('option:selected');
                    var price = selectedOption.data('price');
                    //console.log(formData);
                    const formData2 = {
                        date: $('#date').val(),
                        procedure: selectedOption.text(),
                        dentist: $('#dentist').val(),
                        toothNum: $('#tooth-num').val(),
                        AmtCharged: price,
                        clientID: <?php echo $client_id; ?>,
                        selectedTeeth: "<?php echo $teethString; ?>",
                        transactionCode: "<?php echo $transactionID; ?>"
                    };
                    console.log(formData2);
                    $.ajax({
                        type: 'POST',
                        url: 'process-service-record.php',
                        data: formData2,
                        success: function(response) {
                            Swal.fire(
                                'Appointment records Saved!',
                                'Payment info has been sent to the Secretary.',
                                'success'
                            ).then(() => {
                                window.location.href = "index.php";

                            });
                            showToast(response);

                        },
                        error: function() {
                            alert(
                                'An error occurred while updating the records.'
                            );
                        }
                    });
                }
            });

        });

        // Remove the invalid class when user starts typing in a field
        $('input').on('input', function() {
            $(this).removeClass('is-invalid');
        });

        function showToast(message, delay = 3000) {
            var toast = document.createElement('div');
            toast.classList.add('toast', 'bg-secondary', 'text-white');
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');
            toast.setAttribute('data-bs-delay', delay);

            var toastBody = document.createElement('div');
            toastBody.classList.add('toast-body');
            toastBody.textContent = message;
            toast.appendChild(toastBody);

            document.getElementById('toastContainer').appendChild(toast);

            var bsToast = new bootstrap.Toast(toast);
            bsToast.show();

            toast.addEventListener('hidden.bs.toast', function() {
                toast.remove();
            });
        }

        $('#confirmBtn').click(function() {
            // Gather form data
            const formData = {
                lastName: $('#last-name').val(),
                firstName: $('#first-name').val(),
                middleName: $('#middle-name').val(),
                address: $('#h-address').val(),
                occupation: $('#occupation').val(),
                phoneNumber: $('#phone-number').val(),
                birthday: $('#birthday').val(),
                sex: $('select.form-select').val(),
                religion: $('#religion').val(),
                nationality: $('#nationality').val(),
                officeAddress: $('#o-address').val(),
                dentalInsurance: $('#insurance').val(),
                previousDentist: $('#prev-dentist').val(),
                physicianName: $('#nationality').val(),
                specialty: $('#o-address').val(),
                bloodType: $('#insurance').val(),
                bloodPressure: $('#prev-dentist').val(),
                lastVisit: $('#last-visit').val(),
                healthStatus: $('input[name="healthStatus"]:checked').val(),
                medicalStatus: $('input[name="treatmentStatus"]:checked').val(),
                conditionStatus: $('input[name="surgicalStatus"]:checked').val(),
                viceStatus: $('input[name="viceStatus"]:checked').val(),
                allergies: $('#allergy-select').val().join(' '),
                illness: $('#illness-select').val().join(' '),
                clientID: <?php echo $client_id; ?>,
                transactionCode: '<?php echo $transactionID; ?>'
            };

            console.log($('input[name="healthStatus"]:checked').val());
            console.log($('input[name="treatmentStatus"]:checked').val());
            console.log($('input[name="surgicalStatus"]:checked').val());
            console.log($('input[name="viceStatus"]:checked').val());

            // Send data to PHP using AJAX
            $.ajax({
                type: 'POST',
                url: 'update-records.php',
                data: formData,
                success: function(response) {
                    $('#confirm-modal').modal('hide');

                    showToast(
                        'Update Record Successful'
                    ); // Handle response from the PHP file
                },
                error: function() {
                    alert('An error occurred while updating the records.');
                }
            });
            const formData2 = {
                date: $('#date').val(),
                procedure: $('#procedure').val(),
                dentist: $('#dentist').val(),
                toothNum: $('#tooth-num').val(),
                AmtCharged: $('#amt-charged').val(),
                clientID: <?php echo $client_id; ?>,
                selectedTeeth: "<?php echo $teethString; ?>",
                transactionCode: "<?php echo $transactionID; ?>"
            };
            console.log($('#date').val());
            $.ajax({
                type: 'POST',
                url: 'process-service-record.php',
                data: formData2,
                success: function(response) {
                    $('#confirm-modal').modal('hide');
                    //alert(response);
                    showToast('Payment Request Sent');
                    setTimeout(function() {
                        window.location.href = "index.php";
                    }, 2000);
                    //

                },
                error: function() {
                    alert('An error occurred while updating the records.');
                }
            });
        });


        // Add maxlength attribute to all input fields
        $('input[type="text"], input[type="password"], input[type="email"], textarea').attr('maxlength',
            '70');

        // Real-time validation for all input fields
        $('input[type="text"], input[type="password"], input[type="email"], textarea').on('input',
            function() {
                const maxLength = 70;
                const currentLength = $(this).val().length;
                const remainingChars = maxLength - currentLength;

                // Trim the input if it somehow exceeds 70 characters
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

        // Prevent paste of content longer than 70 characters
        $('input[type="text"], input[type="password"], input[type="email"], textarea').on('paste',
            function(e) {
                // Get pasted data via clipboard API
                let pastedData = e.originalEvent.clipboardData || window.clipboardData;
                let pastedText = pastedData.getData('Text');

                if (pastedText.length > 70) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Too Long!',
                        text: 'Pasted text exceeds the maximum length of 70 characters.',
                        confirmButtonColor: '#3085d6',
                    });
                }
            });

        // Optional: Add validation before form submission
        $('form').on('submit', function(e) {
            let invalidInputs = $(this).find('input, textarea').filter(function() {
                return $(this).val().length > 70;
            });

            if (invalidInputs.length > 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Some fields exceed the maximum length of 70 characters. Please check your inputs.',
                    confirmButtonColor: '#5085d6',
                });

                // Highlight invalid inputs
                invalidInputs.addClass('is-invalid');
            }
        });
    });
    </script>
    <script>
    $(document).ready(function() {
        // Simulate loading progress
        let progress = 0;
        const progressInterval = setInterval(function() {
            progress += Math.random() * 500;
            if (progress > 100) progress = 100;
            updateProgress(progress);

            if (progress === 100) {
                clearInterval(progressInterval);
                setTimeout(hideLoadingScreen, 500);
            }
        }, 500);

        // Update progress bar and text
        function updateProgress(value) {
            const percent = Math.round(value);
            $('.progress-bar').css('width', percent + '%');
            $('.progress-text').text(percent + '%');
        }

        // Hide loading screen with fade effect
        function hideLoadingScreen() {
            $('#loading-screen').fadeOut(200, function() {
                $(this).remove();
            });
        }

        // To manually show/hide the loading screen, you can use:
        // Show: $('#loading-screen').fadeIn();
        // Hide: hideLoadingScreen();
    });
    </script>
    <script>
    $(document).ready(function() {
        // Function to load services into the select dropdown
        function loadServices() {
            $.ajax({
                url: 'get_services.php', // Create this PHP script to fetch services
                method: 'GET',
                dataType: 'json',
                success: function(services) {
                    // Clear existing options
                    $('#service-select').empty();

                    // Add a default/placeholder option
                    $('#service-select').append(
                        $('<option>', {
                            value: '',
                            text: 'Select a Service',
                            selected: true,
                            disabled: true
                        })
                    );

                    // Populate services
                    $.each(services, function(index, service) {
                        $('#service-select').append(
                            $('<option>', {
                                value: service.Service_ID,
                                text: service.ServiceName,
                                'data-price': service.Price
                            })
                        );
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching services:", error);
                    $('#service-select').append(
                        $('<option>', {
                            text: 'Error loading services',
                            value: ''
                        })
                    );
                }
            });
        }

        // Event listener for service selection
        $('#service-select').on('change', function() {
            // Get the selected option
            var selectedOption = $(this).find('option:selected');

            // Get the price from the data attribute
            var price = selectedOption.data('price');
            var IsPriority = '<?php echo $priority; ?>'; // Ensure PHP variable is echoed correctly

            // If IsPriority is 'Yes', reduce 100 from the price
            if (IsPriority === 'Yes') {
                price = price - 100;
            }

            // Format the price (optional - adjust as needed)
            var formattedPrice = price ? '₱' + parseFloat(price).toFixed(2) : '';

            // Display the price
            $('#amount').text(formattedPrice);
        });
        // Load services when the page loads
        loadServices();
    });
    </script>

</head>

<body>
    <?php if (isset($validateUser)): ?>
    <div id="loading-screen">
        <div class="spinner"></div>
        <div class="progress-container">
            <div class="progress-bar"></div>
        </div>
        <div class="loading-text">Loading... <span class="progress-text">0%</span></div>
    </div>


    <!-- toast -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
        <div id="toastContainer"></div>
    </div>

    <div class="wrapper">
        <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>

        <?php
            include("components/sidebar.php");
            ?>
        <div class="main">
            <h4?php include("components/header.php"); ?>


                <main class="content">
                    <div class="container-fluid p-0">

                        <h1 class="h3 mb-3">Patient <strong>Record</strong> </h1>

                        <div class="row">

                            <div class="col-12">
                                <div class="card">

                                    <div class="card-body d-flex flex-row">
                                        <div class="col">
                                            <!-- Patient Details Section -->
                                            <h5 class="h6 card-title">
                                                <span data-feather="info" class="feather-sm me-1"></span>Patient Details
                                            </h5>

                                            <!-- Name Fields -->
                                            <div class="row mt-3 ">
                                                <div class="col-12 col-md-4 mb-3">
                                                    <label for="last-name" class="fs-8 fw-lighter">Last Name</label>
                                                    <input type="text" id="last-name" class="form-control" required
                                                        value="<?php echo $clientDetails['LastName'] ?>">
                                                </div>
                                                <div class="col-12 col-md-4 mb-3">
                                                    <label for="first-name" class="fs-8 fw-lighter">First Name</label>
                                                    <input type="text" id="first-name" class="form-control" required
                                                        value="<?php echo $clientDetails['FirstName'] ?>">
                                                </div>
                                                <div class="col-12 col-md-4 mb-3">
                                                    <label for="middle-name" class="fs-8 fw-lighter">Middle Name</label>
                                                    <input type="text" id="middle-name" class="form-control" required
                                                        value="<?php echo $clientDetails['MiddleName'] ?>">
                                                </div>
                                            </div>

                                            <!-- Personal Info Fields -->
                                            <div class="row mt-2">
                                                <div class="col-12 col-md-3 mb-3">
                                                    <label class="fs-8 fw-lighter">Birthday</label>
                                                    <input type="text" id="birthday" class="form-control"
                                                        placeholder="Birthday (YYYY-MM-DD)" required
                                                        value="<?php echo $clientDetails['Birthday'] ?>">
                                                </div>
                                                <div class="col-12 col-md-3 mb-3">
                                                    <label class="fs-8 fw-lighter">Religion</label>
                                                    <input type="text" id="religion" class="form-control"
                                                        value="<?php echo $clientDetails['Religion'] ?>">
                                                </div>
                                                <div class="col-12 col-md-3 mb-3">
                                                    <label class="fs-8 fw-lighter">Nationality</label>
                                                    <input type="text" id="nationality" class="form-control" required
                                                        value="<?php echo $clientDetails['Nationality'] ?>">
                                                </div>
                                                <div class="col-12 col-md-3 mb-3">
                                                    <label class="fs-8 fw-lighter">Sex</label>
                                                    <select class="form-select">
                                                        <option value="" <?php if (!isset($clientDetails['Sex']))
                                                                echo 'selected'; ?>>
                                                            Sex</option>
                                                        <option value="M" <?php if (($clientDetails['Sex'] ?? '') === 'M')
                                                                echo 'selected'; ?>>
                                                            M</option>
                                                        <option value="F" <?php if (($clientDetails['Sex'] ?? '') === 'F')
                                                                echo 'selected'; ?>>
                                                            F</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Address Fields -->
                                            <div class="row mt-2">
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="fs-8 fw-lighter">Home Address</label>
                                                    <input type="text" id="h-address" class="form-control"
                                                        value="<?php echo $clientDetails['Address'] ?>">
                                                </div>
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="fs-8 fw-lighter">Office Address</label>
                                                    <input type="text" id="o-address" class="form-control"
                                                        value="<?php echo $clientDetails['OfficeAddress'] ?>">
                                                </div>
                                            </div>

                                            <!-- Insurance and Occupation -->
                                            <div class="row mt-2">
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="fs-8 fw-lighter">Dental Insurance</label>
                                                    <input type="text" id="insurance" class="form-control"
                                                        value="<?php echo $clientDetails['DentalInsurance'] ?>">
                                                </div>
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="fs-8 fw-lighter">Occupation</label>
                                                    <input type="text" id="occupation" class="form-control"
                                                        value="<?php echo $clientDetails['Occupation'] ?>">
                                                </div>
                                            </div>

                                            <!-- Contact Information -->
                                            <div class="row mt-2">
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="fs-8 fw-lighter">Phone Number</label>
                                                    <input type="text" id="phone-number" class="form-control"
                                                        value="<?php echo $clientDetails['Number'] ?>">
                                                </div>
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="fs-8 fw-lighter">Email</label>
                                                    <input type="text" id="email" class="form-control"
                                                        value="<?php echo $clientDetails['Email'] ?>" disabled>
                                                </div>
                                            </div>

                                            <!-- Dental History Section -->
                                            <h5 class="h6 card-title mt-4">
                                                <span data-feather="info" class="feather-sm me-1"></span>Dental History
                                            </h5>

                                            <div class="row mt-2">
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="fs-8 fw-lighter">Previous Dentist</label>
                                                    <input type="text" id="prev-dentist" class="form-control"
                                                        value="<?php echo $clientDetails['PreviousDentist'] ?>">
                                                </div>
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="fs-8 fw-lighter">Last Visit</label>
                                                    <input type="text" id="last-visit" class="form-control"
                                                        value="<?php echo $clientDetails['LastVisit'] ?>">
                                                </div>
                                            </div>

                                            <!-- Medical History Section -->
                                            <h5 class="h6 card-title mt-4">
                                                <span data-feather="info" class="feather-sm me-1"></span>Medical History
                                            </h5>

                                            <div class="row mt-2">
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="fs-8 fw-lighter">Physician Name</label>
                                                    <input type="text" id="physician-name" class="form-control"
                                                        value="<?php echo $clientDetails['PhysicianName'] ?>">
                                                </div>
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="fs-8 fw-lighter">Specialty</label>
                                                    <input type="text" id="specialty" class="form-control"
                                                        value="<?php echo $clientDetails['Specialty'] ?>">
                                                </div>
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="fs-8 fw-lighter">Blood Type</label>
                                                    <input type="text" id="bloodType" class="form-control" required
                                                        value="<?php echo $clientDetails['BloodType'] ?>">
                                                </div>
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="fs-8 fw-lighter">Blood Pressure</label>
                                                    <input type="text" id="bloodPressure" class="form-control" required
                                                        value="<?php echo $clientDetails['BloodPressure'] ?>">
                                                </div>
                                            </div>

                                            <!-- Health Questions Section -->
                                            <div class="row mt-4">
                                                <div class="col-12 col-lg-6 mb-4">
                                                    <!-- Health Status Questions -->
                                                    <div class="mb-3">
                                                        <div
                                                            class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-2">
                                                            <label class="form-label mb-2 mb-sm-0">Are you in good
                                                                health?</label>
                                                            <div class="ms-0 ms-sm-2">
                                                                <input class="form-check-input" type="radio"
                                                                    name="healthStatus" value="yes"
                                                                    <?php echo (($clientDetails['HealthStatus'] ?? '') === 'yes') ? 'checked' : ''; ?>>
                                                                <span class="form-check-label me-2">Yes</span>
                                                                <input class="form-check-input" type="radio"
                                                                    name="healthStatus" value="no"
                                                                    <?php echo (($clientDetails['HealthStatus'] ?? '') === 'no') ? 'checked' : ''; ?>>
                                                                <span class="form-check-label">No</span>
                                                            </div>
                                                        </div>

                                                        <div
                                                            class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-2">
                                                            <label class="form-label mb-2 mb-sm-0">Are you in medical
                                                                treatment right now?</label>
                                                            <div class="ms-0 ms-sm-2">
                                                                <input class="form-check-input" type="radio"
                                                                    name="treatmentStatus" value="yes"
                                                                    <?php echo (($clientDetails['MedicalStatus'] ?? '') === 'yes') ? 'checked' : ''; ?>>
                                                                <span class="form-check-label me-2">Yes</span>
                                                                <input class="form-check-input" type="radio"
                                                                    name="treatmentStatus" value="no"
                                                                    <?php echo (($clientDetails['MedicalStatus'] ?? '') === 'no') ? 'checked' : ''; ?>>
                                                                <span class="form-check-label">No</span>
                                                            </div>
                                                        </div>

                                                        <div
                                                            class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-2">
                                                            <label class="form-label mb-2 mb-sm-0">Ever had serious
                                                                illness or surgical operation?</label>
                                                            <div class="ms-0 ms-sm-2">
                                                                <input class="form-check-input" type="radio"
                                                                    name="surgicalStatus" value="yes"
                                                                    <?php echo (($clientDetails['ConditionStatus'] ?? '') === 'yes') ? 'checked' : ''; ?>>
                                                                <span class="form-check-label me-2">Yes</span>
                                                                <input class="form-check-input" type="radio"
                                                                    name="surgicalStatus" value="no"
                                                                    <?php echo (($clientDetails['ConditionStatus'] ?? '') === 'No') ? 'checked' : ''; ?>>
                                                                <span class="form-check-label">No</span>
                                                            </div>
                                                        </div>

                                                        <div
                                                            class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-2">
                                                            <label class="form-label mb-2 mb-sm-0">Do you smoke, drink
                                                                alcohol or take any dangerous drugs?</label>
                                                            <div class="ms-0 ms-sm-2">
                                                                <input class="form-check-input" type="radio"
                                                                    name="viceStatus" value="yes"
                                                                    <?php echo (($clientDetails['ViceStatus'] ?? '') === 'yes') ? 'checked' : ''; ?>>
                                                                <span class="form-check-label me-2">Yes</span>
                                                                <input class="form-check-input" type="radio"
                                                                    name="viceStatus" value="no"
                                                                    <?php echo (($clientDetails['ViceStatus'] ?? '') === 'no') ? 'checked' : ''; ?>>
                                                                <span class="form-check-label">No</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Allergies Section -->
                                                    <div class="mb-3">
                                                        <label class="form-label">Are you allergic to any of the
                                                            following? <span class="text-secondary">(Hold CTRL to select
                                                                multiple options!)</span></label>
                                                        <select class="form-select" multiple size="5"
                                                            id="allergy-select" name="allergies[]">
                                                            <?php
                                                                $selectedAllergies = !empty($clientDetails['Allergies']) ? explode(' ', $clientDetails['Allergies']) : [];
                                                                $allergyOptions = [
                                                                    '1' => 'Local Anesthetic',
                                                                    '2' => 'Sulfa Drug',
                                                                    '3' => 'Aspirin',
                                                                    '4' => 'Latex',
                                                                    '5' => 'Penicilin,Antibiotics'
                                                                ];
                                                                foreach ($allergyOptions as $value => $label) {
                                                                    $selected = in_array($value, $selectedAllergies) ? 'selected' : '';
                                                                    echo "<option class=\"$label\" value=\"$value\" $selected>$label</option>";
                                                                }
                                                                ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Illness Section -->
                                                <div class="col-12 col-lg-6">
                                                    <label class="form-label">Do you have or have you had any of the
                                                        following? <span class="text-secondary">(Hold CTRL to select
                                                            multiple options!)</span></label>
                                                    <select class="form-select mb-3" multiple size="14"
                                                        id="illness-select" name="illness[]">
                                                        <?php
                                                            $selectedIllnesses = !empty($clientDetails['Illness']) ? explode(' ', $clientDetails['Illness']) : [];
                                                            $illnessOptions = [
                                                                '1' => 'High Blood',
                                                                '2' => 'Low blood',
                                                                '3' => 'Epilepsy/Convulsions',
                                                                '4' => 'Aids or HIV infection',
                                                                '5' => 'Sexually Transmitted Disease (STD)',
                                                                '6' => 'Ulcers',
                                                                '7' => 'Radiation Therapy',
                                                                '8' => 'Joint Replacement',
                                                                '9' => 'Heart surgery',
                                                                '10' => 'Heart attack',
                                                                '11' => 'Heart disease',
                                                                '12' => 'Thyroid problem',
                                                                '13' => 'Hepatitis/Liver disease',
                                                                '14' => 'Rheumatic fever',
                                                                '15' => 'Hay fever/allergies',
                                                                '16' => 'Respiratory Problems',
                                                                '17' => 'Jaundice',
                                                                '18' => 'Tuberculosis',
                                                                '19' => 'Kidney diseases',
                                                                '20' => 'Diabetes',
                                                                '21' => 'Stroke',
                                                                '22' => 'Cancer',
                                                                '23' => 'Anemia',
                                                                '24' => 'Angima',
                                                                '25' => 'Asthma',
                                                                '26' => 'Emphysema',
                                                                '27' => 'Bleeding Problems',
                                                                '28' => 'Blood Disease',
                                                                '29' => 'Arthritis'
                                                            ];
                                                            foreach ($illnessOptions as $value => $label) {
                                                                $selected = in_array($value, $selectedIllnesses) ? 'selected' : '';
                                                                echo "<option value=\"$value\" $selected>$label</option>";
                                                            }
                                                            ?>
                                                    </select>
                                                </div>
                                            </div>



                                        </div>
                                    </div>

                                    <div class="modal fade custom-modal" id="confirm-modal" tabindex="-1" role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">Payment Confirmation
                                                    </h5>

                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to proceed to payment?</p>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn " data-dismiss="modal"
                                                        id="cancel">No</button>
                                                    <button type="button" class="btn btn-secondary"
                                                        id="confirmBtn">Confirm</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>




                                    <div class="toast align-items-center text-white bg-primary border-0"
                                        id="toast-alert" role="alert" aria-live="assertive" aria-atomic="true">
                                        <div class="d-flex">
                                            <div class="toast-body">
                                                <p id="toast-message"></p>
                                            </div>
                                            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                                                data-bs-dismiss="toast" aria-label="Close"></button>
                                        </div>
                                    </div>

                                </div>
                                <h1 class="h3 mb-3">Service <strong>Assessment</strong> </h1>
                                <div class="row">
                                    <div class="col">
                                        <div class="card flex-fill card-3d mb-3" id="3d-teeth">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">Permanent 3d Teeth Model <span
                                                        class="text-muted">(Optional)</span> </h5>
                                            </div>
                                            <div class="card-body d-flex flex-column">
                                                <div class="align-self-center" id="teeth-container">
                                                    <div id="tooltip" style="display: none; position: absolute;"></div>

                                                    <button id="selectAllButton" onclick="toggleSelectAll()"><span
                                                            data-feather="grid"></span> Select / Deselect</button>
                                                </div>
                                                <table id="toothTable">
                                                    <tr id="upperTeethRow">
                                                        <!-- Upper Teeth Numbers -->
                                                        <td class="tooth-cell" data-tooth="18">18</td>
                                                        <td class="tooth-cell" data-tooth="17">17</td>
                                                        <td class="tooth-cell" data-tooth="16">16</td>
                                                        <td class="tooth-cell" data-tooth="15">15</td>
                                                        <td class="tooth-cell" data-tooth="14">14</td>
                                                        <td class="tooth-cell" data-tooth="13">13</td>
                                                        <td class="tooth-cell" data-tooth="12">12</td>
                                                        <td class="tooth-cell" data-tooth="11">11</td>
                                                        <td class="tooth-cell" data-tooth="21">21</td>
                                                        <td class="tooth-cell" data-tooth="22">22</td>
                                                        <td class="tooth-cell" data-tooth="23">23</td>
                                                        <td class="tooth-cell" data-tooth="24">24</td>
                                                        <td class="tooth-cell" data-tooth="25">25</td>
                                                        <td class="tooth-cell" data-tooth="26">26</td>
                                                        <td class="tooth-cell" data-tooth="27">27</td>
                                                        <td class="tooth-cell" data-tooth="28">28</td>

                                                    </tr>
                                                    <tr id="lowerTeethRow">
                                                        <!-- Lower Teeth Numbers -->
                                                        <td class="tooth-cell" data-tooth="48">48</td>
                                                        <td class="tooth-cell" data-tooth="47">47</td>
                                                        <td class="tooth-cell" data-tooth="46">46</td>
                                                        <td class="tooth-cell" data-tooth="45">45</td>
                                                        <td class="tooth-cell" data-tooth="44">44</td>
                                                        <td class="tooth-cell" data-tooth="43">43</td>
                                                        <td class="tooth-cell" data-tooth="42">42</td>
                                                        <td class="tooth-cell" data-tooth="41">41</td>
                                                        <td class="tooth-cell" data-tooth="31">31</td>
                                                        <td class="tooth-cell" data-tooth="32">32</td>
                                                        <td class="tooth-cell" data-tooth="33">33</td>
                                                        <td class="tooth-cell" data-tooth="34">34</td>
                                                        <td class="tooth-cell" data-tooth="35">35</td>
                                                        <td class="tooth-cell" data-tooth="36">36</td>
                                                        <td class="tooth-cell" data-tooth="37">37</td>
                                                        <td class="tooth-cell" data-tooth="38">38</td>
                                                    </tr>
                                                </table>

                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-4 col-xl-3">
                                        <div class="card ">
                                            <div class="card-body">
                                                <h5 class="h6 card-title"><span data-feather="info"
                                                        class="feather-sm me-1"></span>Service</h5>


                                                <div class="form-group flex-fill ">

                                                    <label class="fs-8 fw-lighter">Date</label>
                                                    <input type="text" id="date" class="form-control " placeholder=""
                                                        value="<?php echo date("Y-m-d"); ?> " disabled></input>
                                                </div>
                                                <div class="form-group flex-fill mt-3">

                                                    <label class="fs-8 fw-lighter">Dentist</label>
                                                    <input type="text" id="dentist" class="form-control" placeholder=""
                                                        value="<?php echo $doctor ?>" disabled></input>
                                                </div>
                                                <div class="form-group flex-fill mt-3">
                                                    <!-- <input type="text" id="procedure" class="form-control "
                                                    placeholder=""></input> -->
                                                    <label class="fs-8 fw-lighter">Procedure</label>
                                                    <select class="form-select" id="service-select">
                                                    </select>
                                                </div>

                                                <div class="d-flex flex-row mt-3">
                                                    <div class="form-group flex-fill me-3">

                                                        <label class="fs-8 fw-lighter">No. of Tooth</label>
                                                        <input type="text" id="tooth-num" class="form-control  me-3 "
                                                            placeholder=""></input>
                                                    </div>

                                                </div>
                                                <div class="form-group flex-fill mt-4">

                                                    <!-- <label class="fs-8 fw-lighter">Amount Charged</label>
                                                    <input type="text" id="amt-charged" class="form-control "
                                                        placeholder=""></input> -->
                                                    <h2 class="my-2 ">
                                                        Total:
                                                    </h2>
                                                    <h2 id="amount" class="my-2">
                                                    </h2>
                                                </div>

                                                <hr class="my-0 mt-4" />
                                                <button type="button" class="btn btn-secondary mt-3"
                                                    id="btnProceed">Proceed
                                                    to Payment</button>
                                            </div>
                                        </div>
                                    </div>
                </main>

                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row text-muted">
                            <div class="col-6 text-start">
                                <p class="mb-0">
                                    <a class="text-muted" href="#" target="_blank"><strong>Franco -Pascual</strong></a>
                                    Clinic and Orthodontics INC. <a class="text-muted" href="#" target="_blank"></a>
                                    &copy;
                                </p>
                            </div>
                            <div class="col-6 text-end">
                                <ul class="list-inline">
                                    <li class="list-inline-item">
                                        <a class="text-muted" href="#" target="_blank">2024</a>
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </div>
                </footer>
        </div>
    </div>


    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.js"></script>

    <script>
    new DataTable('#datatable');
    </script>
    <script src="three.js/three.min.js"></script>

    <script src="STLLoader.js"></script>
    <script src='TrackballControls.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tween.js/18.6.4/tween.umd.js"></script>

    <script>
    // document.getElementById('left-view').addEventListener('click', function() {
    //     moveCameraToPosition(new THREE.Vector3(-4, -3.5, 0.1)); // Left view
    // });

    // document.getElementById('right-view').addEventListener('click', function() {
    //     moveCameraToPosition(new THREE.Vector3(6, 0, 0)); // Right view
    // });

    // document.getElementById('original-view').addEventListener('click', function() {
    //     moveCameraTionsition(new THREE.Vector3(0, -3.5, 0.1)); // Original position


    // });
    </script>

    <script>
    var container;
    var camera, cameraTarget, scene, renderer;
    var raycaster = new THREE.Raycaster(); // Detect mouse clicks and hovers
    var mouse = new THREE.Vector2(); // Mouse position
    var selectedTeeth = []; // Array to store selected teeth
    var hoveredTooth = null; // Store the hovered tooth
    var tooltip = document.getElementById('tooltip'); // Tooltip element
    var clock = new THREE.Clock(); // Clock for the pulsing animation
    var allSelected = false; // Flag to track select/deselect state
    var selectedToothNumbers = [];

    init();
    animate();

    function init() {

        container = document.getElementById('teeth-container');

        camera = new THREE.PerspectiveCamera(50, container.clientWidth / container.clientHeight, 2, 12);
        camera.position.set(0, -4, 0.1);

        // MOUSE CONTROLS
        controls = new THREE.TrackballControls(camera, container);
        controls.rotateSpeed = 2.0;
        controls.zoomSpeed = 2.2;
        controls.panSpeed = 0.8;
        controls.noZoom = false;
        controls.noPan = false;
        controls.staticMoving = true;
        controls.dynamicDampingFactor = 0.3;
        controls.keys = [65, 83, 68];
        controls.minDistance = 3; // Minimum zoom distance (closer to object)
        controls.maxDistance = 10; //
        controls.addEventListener('change', render);

        cameraTarget = new THREE.Vector3(0, -0.25, 0);

        scene = new THREE.Scene();

        scene.background = null;


        var loader = new THREE.STLLoader();
        var material = new THREE.MeshPhongMaterial({
            color: 0xAAAAAA,
            specular: 0xffffff,
            shininess: 100
        });

        // Load and position each tooth
        loadTooth('(R) Upper Third Molar', '3d-teeth/teeth/teeth1.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(R) Upper Second Molar', '3d-teeth/teeth/teeth2.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(R) Upper First Molar', '3d-teeth/teeth/teeth3.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(R) Upper Second Bicuspid', '3d-teeth/teeth/teeth4.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(R) Upper First Bicuspid', '3d-teeth/teeth/teeth5.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(R) Upper Cuspid', '3d-teeth/teeth/teeth6.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(R) Upper Lateral Incisor', '3d-teeth/teeth/teeth7.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(R) Upper Central Incisor', '3d-teeth/teeth/teeth8.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Upper Central Incisor', '3d-teeth/teeth/teeth9.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Upper Lateral Incisor', '3d-teeth/teeth/teeth10.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Upper Cuspid', '3d-teeth/teeth/teeth11.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Upper First Bicuspid', '3d-teeth/teeth/teeth12.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Upper Second Bicuspid', '3d-teeth/teeth/teeth13.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Upper First Molar', '3d-teeth/teeth/teeth14.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Upper Second Molar', '3d-teeth/teeth/teeth15.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Upper Third Molar', '3d-teeth/teeth/teeth16.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(R) Lower Third Molar', '3d-teeth/teeth/teeth17.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(R) Lower Second Molar', '3d-teeth/teeth/teeth18.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(R) Lower First Molar', '3d-teeth/teeth/teeth19.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(R) Lower Second Bicuspid', '3d-teeth/teeth/teeth20.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(R) Lower First Bicuspid', '3d-teeth/teeth/teeth21.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(R) Lower Cuspid', '3d-teeth/teeth/teeth22.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(R) Lower Lateral Incisor', '3d-teeth/teeth/teeth23.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(R) Lower Central Incisor', '3d-teeth/teeth/teeth24.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Lower Central Incisor', '3d-teeth/teeth/teeth25.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Lower Lateral Incisor', '3d-teeth/teeth/teeth26.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Lower Cuspid', '3d-teeth/teeth/teeth27.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Lower First Bicuspid', '3d-teeth/teeth/teeth28.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Lower Second Bicuspid', '3d-teeth/teeth/teeth29.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Lower First Molar', '3d-teeth/teeth/teeth30.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Lower Second Molar', '3d-teeth/teeth/teeth31.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Lower Third Molar', '3d-teeth/teeth/teeth32.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        // Load r_letter.stl and set color to blue
        loadTooth('r_letter', '3d-teeth/teeth/r_letter.stl', {
            x: 0,
            y: 0,
            z: 0
        }, true, 0xCFE2F3);

        // Load l_letter.stl and set color to blue
        loadTooth('l_letter', '3d-teeth/teeth/l_letter.stl', {
            x: 0,
            y: 0,
            z: 0
        }, true, 0xCFE2F3);


        // LIGHTS
        // Hemisphere Light (ambient lighting)
        var hemisphereLight = new THREE.HemisphereLight(0xffffff, 0x444444, 1.2);
        hemisphereLight.position.set(0, 1, 0); // Light from above
        scene.add(hemisphereLight);

        // Directional Light (strong front light)
        var directionalLight = new THREE.DirectionalLight(0xffffff, 1.0);
        directionalLight.position.set(2, 1, 3); // Positioned to shine on the front
        directionalLight.castShadow = true;
        scene.add(directionalLight);

        // Fill light to reduce shadows from below
        var fillLight = new THREE.PointLight(0xffffff, 0.8);
        fillLight.position.set(0, -3, 3); // Soft light from below the model
        scene.add(fillLight);

        // RENDERER
        renderer = new THREE.WebGLRenderer({
            antialias: true,
            alpha: true // Enable transparency
        });
        renderer.setPixelRatio(window.devicePixelRatio);
        renderer.setSize(container.clientWidth, container.clientHeight);
        renderer.gammaInput = true;
        renderer.gammaOutput = true;

        container.appendChild(renderer.domElement);

        window.addEventListener('resize', onWindowResize, false);
        window.addEventListener('mousemove', onDocumentMouseMove, false); // Add hover event listener
        window.addEventListener('click', onDocumentMouseDown, false); // Add click event listener


    }

    function loadTooth(name, stlPath, position, isLetter = false, color = 0xAAAAAA) {
        var loader = new THREE.STLLoader();
        var material = new THREE.MeshPhongMaterial({
            color: color, // Use the custom color
            specular: 0xffffff,
            shininess: 100
        });
        loader.load(stlPath, function(geometry) {
            var toothMesh = new THREE.Mesh(geometry, material);
            toothMesh.scale.set(0.3, 0.3, 0.3);
            toothMesh.name = name; // Assign name to the tooth
            toothMesh.position.set(position.x, position.y, position.z); // Position the tooth
            toothMesh.isLetter = isLetter; // Mark as letter if true
            scene.add(toothMesh);
            // console.log("Loaded tooth name:", toothMesh.name);
        });


    }


    //---------FOR SELECT AND DESELECT BUTTON---------------
    // Function to toggle select/deselect all teeth
    function toggleSelectAll() {
        if (allSelected) {
            // Deselect all teeth in the 3D model and reset table cells
            scene.children.forEach(function(object) {
                if (!object.isLetter && object.type === "Mesh") {
                    object.material.color.set(0xAAAAAA); // Reset color to default

                }
            });

            // Clear selected teeth array
            selectedTeeth = [];
            selectedToothNumbers = []

            // Reset all table cells to default color
            // document.querySelectorAll('.tooth-cell').forEach(function(cell) {
            //     cell.style.backgroundColor = ''; // Reset to default (usually transparent)
            // });
            document.querySelectorAll('.tooth-cell').forEach(cell => {

                if (cell.classList.contains('selected')) {
                    cell.classList.remove('selected'); // Only remove 'selected' if it’s currently applied
                }

            });

        } else {
            // Select all teeth in the 3D model and highlight in red
            scene.children.forEach(function(object) {
                if (!object.isLetter && object.type === "Mesh") {
                    object.material.color.set(0xFF0000); // Highlight in red
                    if (!selectedTeeth.includes(object)) {
                        selectedTeeth.push(object); // Add to selected teeth array
                    }
                }
                const toothNumber = getToothNumberByName(object.name);
                if (toothNumber && !selectedToothNumbers.includes(parseInt(toothNumber))) {
                    selectedToothNumbers.push(parseInt(toothNumber));
                }
            });
            document.querySelectorAll('.tooth-cell').forEach(cell => {
                if (cell.classList.contains('selected')) {

                } else {
                    cell.classList.toggle('selected');

                }



            });

            // Set all table cells to red to indicate selection

        }
        console.log(selectedToothNumbers);

        allSelected = !allSelected; // Toggle the state
    }


    // Function to handle hover behavior for all teeth
    document.getElementById('selectAllButton').addEventListener('mouseenter', function() {
        scene.children.forEach(function(object) {
            if (!object.isLetter && object.type === "Mesh") {
                object.material.color.set(0xFFFF00); // Highlight in yellow
            }
        });
    });

    // Function to handle un-hovering
    document.getElementById('selectAllButton').addEventListener('mouseleave', function() {
        scene.children.forEach(function(object) {
            if (!object.isLetter && object.type === "Mesh") {
                if (selectedTeeth.includes(object)) {
                    object.material.color.set(0xFF0000); // Keep red for selected teeth
                } else {
                    object.material.color.set(0xAAAAAA); // Reset to default for non-selected teeth
                }
            }
        });
    });

    document.querySelectorAll('.tooth-cell').forEach(cell => {
        cell.addEventListener('mouseenter', function() {
            let toothNumber = this.getAttribute('data-tooth');

            highlightToothInModel(toothNumber); // Highlight corresponding 3D tooth
            this.classList.add('hovered');



        });

        cell.addEventListener('mouseleave', function() {
            let toothNumber = this.getAttribute('data-tooth');
            resetToothInModel(toothNumber); // Reset 3D tooth color
            this.classList.remove('hovered');

        });

        cell.addEventListener('click', function() {
            let toothNumber = parseInt(this.getAttribute('data-tooth'));
            //console.log(toothNumber);

            if (toothNumber) {
                if (selectedToothNumbers.includes(toothNumber)) {
                    // If tooth is already selected, remove it from selectedToothNumbers
                    selectedToothNumbers = selectedToothNumbers.filter(num => num !== toothNumber);
                } else {
                    // If tooth is not in selectedToothNumbers, add it
                    selectedToothNumbers.push(toothNumber);
                }
            }

            // console.log(selectedToothNumbers);
            toggleSelectToothInModel(toothNumber); // Toggle selection in 3D model

            this.classList.toggle('selected');
        });
    });




    function onWindowResize() {
        // Get the container dimensions
        const width = container.clientWidth;
        const height = container.clientHeight;

        // Update the camera's aspect ratio and projection matrix
        camera.aspect = width / height;
        camera.updateProjectionMatrix();

        // Resize the renderer to match the new container size
        renderer.setSize(width, height);

        // Update controls to handle the new size
        controls.handleResize();
    }

    // Listen for window resize events
    window.addEventListener('resize', onWindowResize, false);




    function animate() {
        requestAnimationFrame(animate);
        var elapsedTime = clock.getElapsedTime();

        selectedTeeth.forEach(function(tooth) {
            var pulseFactor = (Math.sin(elapsedTime * 2) + 1) / 2;
            var newColor = new THREE.Color(1, pulseFactor * 0.5, pulseFactor * 0.5);
            tooth.material.color.set(newColor);
        });

        render();
        controls.update();
    }



    function onDocumentMouseMove(event) {
        // Calculate mouse position in normalized device coordinates relative to container
        const rect = container.getBoundingClientRect();
        mouse.x = ((event.clientX - rect.left) / container.clientWidth) * 2 - 1;
        mouse.y = -((event.clientY - rect.top) / container.clientHeight) * 2 + 1;

        // Update raycaster to check hover
        raycaster.setFromCamera(mouse, camera);
        const intersects = raycaster.intersectObjects(scene.children, true);

        if (intersects.length > 0) {
            const currentTooth = intersects[0].object;

            // Skip if the hovered object is a letter (r_letter or l_letter)
            if (currentTooth.isLetter) {
                tooltip.style.display = 'none'; // Hide tooltip for letters
                return; // Ignore letters
            }

            // Reset the color of the previous hovered tooth if it's not selected
            if (hoveredTooth && hoveredTooth !== currentTooth && !selectedTeeth.includes(hoveredTooth)) {
                hoveredTooth.material.color.set(0xAAAAAA); // Reset previous hover color if not selected
                resetTableCell(hoveredTooth.name); // Reset table cell for the previous hovered tooth

            }

            // Only change color if it's a different tooth and not selected
            if (hoveredTooth !== currentTooth && !selectedTeeth.includes(currentTooth)) {
                highlightTableCell(currentTooth.name); // Change the table cell color when hovered
                currentTooth.material.color.set(0xFFFF00); // Highlight hovered tooth in yellow
                hoveredTooth = currentTooth; // Update hovered tooth
                // console.log(hoveredTooth.name); 
            }

            // Update tooltip position and content
            updateTooltip(currentTooth.name);
        } else {
            resetTableCell(); // Reset the table cell when no tooth is hovered
            tooltip.style.display = 'none'; // Hide tooltip if no tooth is hovered

            if (hoveredTooth && !selectedTeeth.includes(hoveredTooth)) {
                hoveredTooth.material.color.set(0xAAAAAA); // Reset color if not selected
            }
            hoveredTooth = null;
        }
    }


    function onDocumentMouseDown(event) {
        // Calculate mouse position relative to the container
        const rect = container.getBoundingClientRect();
        mouse.x = ((event.clientX - rect.left) / container.clientWidth) * 2 - 1;
        mouse.y = -((event.clientY - rect.top) / container.clientHeight) * 2 + 1;

        raycaster.setFromCamera(mouse, camera);
        var intersects = raycaster.intersectObjects(scene.children, true);

        if (intersects.length > 0) {
            var clickedTooth = intersects[0].object;

            // Skip if the clicked object is a letter (r_letter or l_letter)
            if (clickedTooth.isLetter) {
                return; // Ignore letters
            }

            // Toggle selection
            if (selectedTeeth.includes(clickedTooth)) {
                clickedTooth.material.color.set(0xAAAAAA); // Deselect tooth
                selectedTeeth = selectedTeeth.filter(tooth => tooth !== clickedTooth); // Remove from selected array

                // remove the selected tooth in an array
                const toothNumber = getToothNumberByName(clickedTooth.name);
                if (toothNumber) {
                    selectedToothNumbers = selectedToothNumbers.filter(num => num !== parseInt(toothNumber));
                }


            } else {
                clickedTooth.material.color.set(0xFF0000); // Select tooth and color it red
                selectedTeeth.push(clickedTooth); // Add to selected array

                // Add tooth number to selectedToothNumbers array
                const toothNumber = getToothNumberByName(clickedTooth.name);
                if (toothNumber && !selectedToothNumbers.includes(parseInt(toothNumber))) {
                    selectedToothNumbers.push(parseInt(toothNumber));
                }

            }
        }
        if (intersects.length > 0) {
            var clickedTooth = intersects[0].object;
            if (!clickedTooth.isLetter) {
                toggleTableCell(clickedTooth.name); // Synchronize table selection with 3D model
                // Existing click logic
            }
        }
    }


    // Corrected toothMapping with consistent numbering
    const toothMapping = {
        18: "(R) Upper Third Molar",
        17: "(R) Upper Second Molar",
        16: "(R) Upper First Molar",
        15: "(R) Upper Second Bicuspid",
        14: "(R) Upper First Bicuspid",
        13: "(R) Upper Cuspid",
        12: "(R) Upper Lateral Incisor",
        11: "(R) Upper Central Incisor",
        21: "(L) Upper Central Incisor",
        22: "(L) Upper Lateral Incisor",
        23: "(L) Upper Cuspid",
        24: "(L) Upper First Bicuspid",
        25: "(L) Upper Second Bicuspid",
        26: "(L) Upper First Molar",
        27: "(L) Upper Second Molar",
        28: "(L) Upper Third Molar",
        48: "(R) Lower Third Molar",
        47: "(R) Lower Second Molar",
        46: "(R) Lower First Molar",
        45: "(R) Lower Second Bicuspid",
        44: "(R) Lower First Bicuspid",
        43: "(R) Lower Cuspid",
        42: "(R) Lower Lateral Incisor",
        41: "(R) Lower Central Incisor",
        31: "(L) Lower Central Incisor",
        32: "(L) Lower Lateral Incisor",
        33: "(L) Lower Cuspid",
        34: "(L) Lower First Bicuspid",
        35: "(L) Lower Second Bicuspid",
        36: "(L) Lower First Molar",
        37: "(L) Lower Second Molar",
        38: "(L) Lower Third Molar",
        // Continue with other mappings...
    };

    function getToothNumberByName(toothName) {
        return Object.keys(toothMapping).find(key => toothMapping[key] === toothName);
    }

    function highlightToothInModel(toothNumber) {
        // console.log("Highlighting tooth:", toothNumber);

        // Look up the tooth name using the tooth number
        var toothName = toothMapping[toothNumber];
        //console.log(toothName)

        // Traverse the scene to find the matching object by name
        scene.children.forEach(function(object) {
            if (object.name === toothName) {

                // console.log("Found object:", object.name);
                object.material.color.set(0xFFFF00); // Highlight color
                updateTooltip(toothName);
                //resetTableCell();
            }
        });

    }


    function resetToothInModel(toothNumber) {
        var toothName = toothMapping[toothNumber];
        scene.children.forEach(function(object) {
            if (object.name === toothName && !selectedTeeth.includes(object)) {
                object.material.color.set(0xAAAAAA); // Reset to default
            }
        });
    }

    function toggleSelectToothInModel(toothNumber) {

        var toothName = toothMapping[toothNumber];
        scene.children.forEach(function(object) {
            if (object.name === toothName) {
                if (selectedTeeth.includes(object)) {
                    object.material.color.set(0xAAAAAA); // Deselect
                    selectedTeeth = selectedTeeth.filter(tooth => tooth !== object);
                } else {
                    object.material.color.set(0xFF0000); // Select
                    selectedTeeth.push(object);
                }
            }
        });
    }

    function highlightTableCell(toothName) {
        document.querySelectorAll('.tooth-cell').forEach(cell => {
            if (cell.getAttribute('data-tooth') == getToothNumber(toothName)) {
                cell.classList.add('hovered');
            }
        });
    }

    function resetTableCell() {
        document.querySelectorAll('.tooth-cell').forEach(cell => {
            cell.classList.remove('hovered');
        });
    }

    function toggleTableCell(toothName) {
        document.querySelectorAll('.tooth-cell').forEach(cell => {
            if (cell.getAttribute('data-tooth') == getToothNumber(toothName)) {
                cell.classList.toggle('selected');
            }
        });
    }

    function highlightTeethFromArray(teethArray) {
        // Clear previous selections
        console.log(teethArray);
        selectedTeeth = []; // Reset selected teeth array
        scene.children.forEach(function(object) {
            if (!object.isLetter && object.type === "Mesh") {
                object.material.color.set(0xAAAAAA); // Reset color to default for all teeth
            }
        });

        // Highlight specified teeth in red
        teethArray.forEach(function(toothNumber) {
            var toothName = toothMapping[toothNumber]; // Get the tooth name from the mapping
            scene.children.forEach(function(object) {
                if (object.name === toothName) {
                    object.material.color.set(0xFF0000); // Set color to red
                    selectedTeeth.push(object); // Add to selected teeth array
                    if (!selectedTeeth.includes(object)) {
                        selectedTeeth.push(object); // Add to selected teeth array
                    }
                }
                const toothNumber = getToothNumberByName(object.name);
                if (toothNumber && !selectedToothNumbers.includes(parseInt(toothNumber))) {
                    selectedToothNumbers.push(parseInt(toothNumber));
                }

            });
            document.querySelectorAll('.tooth-cell').forEach(cell => {
                if (cell.getAttribute('data-tooth') == toothNumber) {
                    cell.classList.toggle('selected');
                }
            });
        });
    }

    function getToothNumber(toothName) {
        switch (toothName) {
            case '(R) Upper Third Molar':
                return '18';
            case '(R) Upper Second Molar':
                return '17';
            case '(R) Upper First Molar':
                return '16';
            case '(R) Upper Second Bicuspid':
                return '15';
            case '(R) Upper First Bicuspid':
                return '14';
            case '(R) Upper Cuspid':
                return '13';
            case '(R) Upper Lateral Incisor':
                return '12';
            case '(R) Upper Central Incisor':
                return '11';
            case '(L) Upper Central Incisor':
                return '21';
            case '(L) Upper Lateral Incisor':
                return '22';
            case '(L) Upper Cuspid':
                return '23';
            case '(L) Upper First Bicuspid':
                return '24';
            case '(L) Upper Second Bicuspid':
                return '25';
            case '(L) Upper First Molar':
                return '26';
            case '(L) Upper Second Molar':
                return '27';
            case '(L) Upper Third Molar':
                return '28';
            case '(R) Lower Third Molar':
                return '48';
            case '(R) Lower Second Molar':
                return '47';
            case '(R) Lower First Molar':
                return '46';
            case '(R) Lower Second Bicuspid':
                return '45';
            case '(R) Lower First Bicuspid':
                return '44';
            case '(R) Lower Cuspid':
                return '43';
            case '(R) Lower Lateral Incisor':
                return '42';
            case '(R) Lower Central Incisor':
                return '41';
            case '(L) Lower Central Incisor':
                return '31';
            case '(L) Lower Lateral Incisor':
                return '32';
            case '(L) Lower Cuspid':
                return '33';
            case '(L) Lower First Bicuspid':
                return '34';
            case '(L) Lower Second Bicuspid':
                return '35';
            case '(L) Lower First Molar':
                return '36';
            case '(L) Lower Second Molar':
                return '37';
            case '(L) Lower Third Molar':
                return '38';

            default:
                return null;
        }
    }



    function updateTooltip(tooth) {
        let description = "";
        switch (tooth) {
            case '(R) Upper Third Molar':
                description = "(R) Upper Third Molar (FDI: 18, Palmer: 8, Universal: 1)";
                break;
            case '(R) Upper Second Molar':
                description = "(R) Upper Second Molar (FDI: 17, Palmer: 7, Universal: 2)";
                break;
            case '(R) Upper First Molar':
                description = "(R) Upper First Molar (FDI: 16, Palmer: 6, Universal: 3)";
                break;
            case '(R) Upper Second Bicuspid':
                description = "(R) Upper Second Bicuspid (FDI: 15, Palmer: 5, Universal: 4)";
                break;
            case '(R) Upper First Bicuspid':
                description = "(R) Upper First Bicuspid (FDI: 14, Palmer: 4, Universal: 5)";
                break;
            case '(R) Upper Cuspid':
                description = "(R) Upper Cuspid (FDI: 13, Palmer: 3, Universal: 6)";
                break;
            case '(R) Upper Lateral Incisor':
                description = "(R) Upper Lateral Incisor (FDI: 12, Palmer: 2, Universal: 7)";
                break;
            case '(R) Upper Central Incisor':
                description = "(R) Upper Central Incisor (FDI: 11, Palmer: 1, Universal: 8)";
                break;
            case '(L) Upper Central Incisor':
                description = "(L) Upper Central Incisor (FDI: 21, Palmer: 1, Universal: 9)";
                break;
            case '(L) Upper Lateral Incisor':
                description = "(L) Upper Lateral Incisor (FDI: 22, Palmer: 2, Universal: 10)";
                break;
            case '(L) Upper Cuspid':
                description = "(L) Upper Cuspid (FDI: 23, Palmer: 3, Universal: 11)";
                break;
            case '(L) Upper First Bicuspid':
                description = "(L) Upper First Bicuspid (FDI: 24, Palmer: 4, Universal: 12)";
                break;
            case '(L) Upper Second Bicuspid':
                description = "(L) Upper Second Bicuspid (FDI: 25, Palmer: 5, Universal: 13)";
                break;
            case '(L) Upper First Molar':
                description = "(L) Upper First Molar (FDI: 26, Palmer: 6, Universal: 14)";
                break;
            case '(L) Upper Second Molar':
                description = "(L) Upper Second Molar (FDI: 27, Palmer: 7, Universal: 15)";
                break;
            case '(L) Upper Third Molar':
                description = "(L) Upper Third Molar (FDI: 28, Palmer: 8, Universal: 16)";
                break;
            case '(R) Lower Third Molar':
                description = "(R) Lower Third Molar (FDI: 48, Palmer: 8, Universal: 32)";
                break;
            case '(R) Lower Second Molar':
                description = "(R) Lower Second Molar (FDI: 47, Palmer: 7, Universal: 31)";
                break;
            case '(R) Lower First Molar':
                description = "(R) Lower First Molar (FDI: 46, Palmer: 6, Universal: 30)";
                break;
            case '(R) Lower Second Bicuspid':
                description = "(R) Lower Second Bicuspid (FDI: 45, Palmer: 5, Universal: 29)";
                break;
            case '(R) Lower First Bicuspid':
                description = "(R) Lower First Bicuspid (FDI: 44, Palmer: 4, Universal: 28)";
                break;
            case '(R) Lower Cuspid':
                description = "(R) Lower Cuspid (FDI: 43, Palmer: 3, Universal: 27)";
                break;
            case '(R) Lower Lateral Incisor':
                description = "(R) Lower Lateral Incisor (FDI: 42, Palmer: 2, Universal: 26)";
                break;
            case '(R) Lower Central Incisor':
                description = "(R) Lower Central incisor (FDI: 41, Palmer: 1, Universal: 25)";
                break;
            case '(L) Lower Central Incisor':
                description = "(L) Lower Central incisor (FDI: 31, Palmer: 1, Universal: 24)";
                break;
            case '(L) Lower Lateral Incisor':
                description = "(L) Lower Lateral Incisor (FDI: 32, Palmer: 2, Universal: 23)";
                break;
            case '(L) Lower Cuspid':
                description = "(L) Lower Cuspid (FDI: 33, Palmer: 3, Universal: 22)";
                break;
            case '(L) Lower First Bicuspid':
                description = "(L) Lower First Bicuspid (FDI: 34, Palmer: 4, Universal: 21)";
                break;
            case '(L) Lower Second Bicuspid':
                description = "(L) Lower Second Bicuspid (FDI: 35, Palmer: 5, Universal: 20)";
                break;
            case '(L) Lower First Molar':
                description = "(L) Lower First Molar (FDI: 36, Palmer: 6, Universal: 19)";
                break;
            case '(L) Lower Second Molar':
                description = "(L) Lower Second Molar (FDI: 37, Palmer: 7, Universal: 18)";
                break;
            case '(L) Lower Third Molar':
                description = "(L) Lower Third Molar (FDI: 38, Palmer: 8, Universal: 17)";
                break;

            default:
                description = "Unknown tooth.";
        }

        // Update tooltip content
        tooltip.innerHTML = description;

        // Set the tooltip to a fixed position in the upper-right corner
        tooltip.style.position = 'absolute';
        tooltip.style.right = '20px'; // Fixed position from the right edge
        tooltip.style.top = '20px'; // Fixed position from the top edge

        // Ensure the tooltip is shown
        tooltip.style.display = 'block';
    }



    function render() {
        renderer.render(scene, camera);
    }
    </script>
    <script src="../js/app.js"></script>
    <?php else: ?>
    <p>Please <a href="Login-Registration/login-register.php">Login or Register</a></p>
    <?php endif ?>


</body>

</html>