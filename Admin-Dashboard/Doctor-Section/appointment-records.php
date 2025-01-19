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
    

    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        function decryptId($encryptedId) {
            return base64_decode($encryptedId);
        }
        
        // Get and decrypt the ID
        $encryptedId = $_GET['id'];
        $clientId = decryptId($encryptedId);
        $stmt = $conn->prepare("SELECT * FROM tbl_clients WHERE Client_ID = ?");
        $stmt->bind_param("s", $clientId);
        $stmt->execute();
        $result = $stmt->get_result();
        $clientDetails = $result->fetch_assoc();



        $stmt = $conn->prepare("SELECT * FROM tbl_treatment_records WHERE Client_ID = ?");
        $stmt->bind_param("s", $clientId);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch all data
        $paymentData = array();
        while($row = $result->fetch_assoc()) {
            $paymentData[] = $row;
        }    
        
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



    <title>Doctor's Page</title>

    <link href="../css/app.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">

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
    .dt-search {
        display: flex;
        align-items: center;
    }

    .dt-search span[data-feather] {
        margin-left: 8px;
    }

    .dt-search input {
        flex: 1;
    }

    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.9rem;
        }
    }

    .table td {
        vertical-align: middle;
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }


    .icon-container {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 20px 0;
    }

    .check-icon {
        display: none;
        opacity: 0;
        transform: scale(0.5);
        transition: all 0.3s ease-in-out;
    }

    .check-icon.show {
        opacity: 1;
        transform: scale(1);
    }

    .loader {
        transition: opacity 0.3s ease-in-out;
    }

    .feather-small {
        width: 14px;
        height: 14px;
        stroke-width: 2;
        margin-right: 4px;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    #teeth-container {
        width: 114.7vh;
        height: 100%;
        position: relative;
    }

    #teeth-container {
        background-color: transparent;
        /* Make sure it's transparent */
        background-image: url('bg.png');
    }


    .card-3d {
        height: 80vh;
        /* Adjust based on your card's design */
        display: flex;
        flex-direction: column;
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
    </style>
    <script>
    $(document).ready(function() {
        ///////////////////////////////////////////     VALIDATIONS        ///////////////////////////////////////


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


        //=======================PDF GENERATION==============================
        // Wait for document ready
        $('#createPDF').on('click', function() {
            // Show loading indicator
            $(this).prop('disabled', true);
            $(this).html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generating...'
            );
            var selectedValues = [];
            $('#allergy-select option:selected').each(function() {
                selectedValues.push($(this).text());
            });

            var illnessValues = [];
            $('#illness-select option:selected').each(function() {
                illnessValues.push($(this).text());
            });

            // Collect all form data
            const patientData = {
                // Personal Details
                lastName: $('#last-name').val(),
                firstName: $('#first-name').val(),
                middleName: $('#middle-name').val(),
                birthday: $('#birthday').val(),
                religion: $('#religion').val(),
                nationality: $('#nationality').val(),
                sex: $('select.form-select').first().val(),
                homeAddress: $('#h-address').val(),
                officeAddress: $('#o-address').val(),
                insurance: $('#insurance').val(),
                occupation: $('#occupation').val(),
                phoneNumber: $('#phone-number').val(),
                email: $('#email').val(),

                // Dental History
                previousDentist: $('#prev-dentist').val(),
                lastVisit: $('#last-visit').val(),

                // Medical History
                physicianName: $('#physician-name').val(),
                specialty: $('#specialty').val(),
                bloodType: $('#bloodType').val(),
                bloodPressure: $('#bloodPressure').val(),

                // Health Status
                healthStatus: '<?php echo $clientDetails['HealthStatus'] ?>',
                treatmentStatus: '<?php echo $clientDetails['MedicalStatus'] ?>',
                surgicalStatus: '<?php echo $clientDetails['ConditionStatus'] ?>',
                viceStatus: '<?php echo $clientDetails['ViceStatus'] ?>',



                // Multiple Select Values
                allergies: selectedValues || [],
                illnesses: illnessValues || []
            };

            console.log(selectedValues);



            // Collect table data
            const tableData = [];
            $('#datatable tbody tr').each(function() {
                var Cost = $(this).find('td:eq(4)').text().trim().replace(/^₱\s*/, '');

                const row = {
                    transactionCode: $(this).find('td:eq(0)').text().trim(),
                    treatmentDate: $(this).find('td:eq(1)').text().trim(),
                    treatmentName: $(this).find('td:eq(2)').text().trim(),
                    doctor: $(this).find('td:eq(3)').text().trim(),
                    treatmentCost: Cost
                };
                tableData.push(row);
            });

            // Debug: Log the data being sent
            console.log('Patient Data:', patientData);
            console.log('Treatment Data:', tableData);

            // Send data to PHP script
            $.ajax({
                url: 'generate-pdf.php',
                method: 'POST',
                data: {
                    patientData: JSON.stringify(patientData),
                    treatmentData: JSON.stringify(tableData)
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(response, status, xhr) {
                    // Reset button
                    const $button = $('#createPDF');
                    $button.prop('disabled', false);
                    $button.html(
                        '<span class="me-2" data-feather="file"></span>Generate Record PDF'
                    );

                    if (response.size <
                        100) { // If response is too small, it might be an error message
                        const reader = new FileReader();
                        reader.onload = function() {
                            console.error('Error:', this.result);
                            alert(
                                'Error generating PDF. Please check the console for details.'
                            );
                        };
                        reader.readAsText(response);
                    } else {
                        // Create blob and open PDF
                        const blob = new Blob([response], {
                            type: 'application/pdf'
                        });
                        const url = window.URL.createObjectURL(blob);
                        window.open(url);
                    }
                },
                error: function(xhr, status, error) {
                    // Reset button
                    const $button = $('#createPDF');
                    $button.prop('disabled', false);
                    $button.html(
                        '<span class="me-2" data-feather="file"></span>Generate Record PDF'
                    );

                    console.error('Error generating PDF:', error);
                    alert('Error generating PDF. Please check the console for details.');
                }
            });
        });


        $('#btnProceed').click(function() {
            // Check if any of the required fields are empty

            // Show the confirmation modal if all fields are filled
            $('#confirm-modal').modal('show');

        });

        var table = $('#datatable').DataTable({
            responsive: true,
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
            language: {
                search: "",
                searchPlaceholder: "Search transactions...",
                lengthMenu: "Show _MENU_ entries"
            },

            order: [
                [4, 'desc']
            ],
            pageLength: 10,
            responsive: true,
            dom: '<"row justify-content-between"<"col-md-auto"l><"col-md-auto"f>>rt<"row justify-content-between"<"col-md-auto"i><"col-md-auto"p>>',
            language: {
                search: "",
                searchPlaceholder: "Search records..."
            },

            footerCallback: function(row, data, start, end, display) {
                var api = this.api();

                // Remove currency symbol and commas from a string and convert to number
                var intVal = function(i) {
                    if (typeof i === 'string') {
                        // Remove ₱ symbol and commas, then convert to number
                        return parseFloat(i.replace(/[₱,]/g, '')) || 0;
                    }
                    return typeof i === 'number' ? i : 0;
                };

                // Calculate total for all pages
                var total = api
                    .column(4, {
                        search: 'applied'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Format the number with commas and ₱ symbol
                var formattedTotal = '₱' + total.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                // Update footer
                $(api.column(4).footer()).html(formattedTotal);
            }
        });
        $('.dt-length label').remove();
        $('.dt-search label').remove();
        $('.dt-search').prepend('<span class="me-3" data-feather="search"></span>');
        feather.replace();

        $('#cancel').click(function() {
            $('#confirm-modal').modal('hide');
        });
        $('#btnProceed').click(function() {
            // Check if any of the required fields are empty

            $('#confirm-modal').modal('show');
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
            var transactionCode = "";
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
                physicianName: $('#physician-name').val(),
                specialty: $('#specialty').val(),
                bloodType: $('#bloodType').val(),
                bloodPressure: $('#bloodPressure').val(),
                lastVisit: $('#last-visit').val(),
                healthStatus: $('input[name="healthStatus"]:checked').val(),
                medicalStatus: $('input[name="treatmentStatus"]:checked').val(),
                conditionStatus: $('input[name="surgicalStatus"]:checked').val(),
                viceStatus: $('input[name="viceStatus"]:checked').val(),
                allergies: $('#allergy-select').val().join(' '),
                illness: $('#illness-select').val().join(' '),
                clientID: <?php echo $clientId;?>,
                transactionCode: transactionCode
            };



            // Send data to PHP using AJAX
            $.ajax({
                type: 'POST',
                url: 'update-records.php',
                data: formData,
                success: function(response) {
                    $('#confirm-modal').modal('hide');
                    showToast(
                        response); // Handle response from the PHP file
                },
                error: function() {
                    alert('An error occurred while updating the records.');
                }
            });


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

        // To manually show/hide the loading screen, you can use    // Show: $('#loading-screen').fadeIn();
        // Hide: hideLoadingScreen();
    });
    </script>
    <script>
    // ==============  INPUT FIELD VALIDATIONS  ====================

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
    </script>

</head>

<body>

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
            <?php
    include("components/header.php");     
?>


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
                                        <div class="row mt-3">
                                            <div class="col-12 col-md-4 mb-3">
                                                <label for="last-name" class="fs-8 fw-lighter">Last Name</label>
                                                <input type="text" id="last-name" class="form-control" required
                                                    minlength="2" maxlength="50"
                                                    value="<?php echo htmlspecialchars($clientDetails['LastName'])?>"
                                                    autocomplete="family-name">
                                            </div>
                                            <div class="col-12 col-md-4 mb-3">
                                                <label for="first-name" class="fs-8 fw-lighter">First Name</label>
                                                <input type="text" id="first-name" class="form-control" required
                                                    minlength="2" maxlength="50"
                                                    value="<?php echo htmlspecialchars($clientDetails['FirstName'])?>"
                                                    autocomplete="given-name">
                                            </div>
                                            <div class="col-12 col-md-4 mb-3">
                                                <label for="middle-name" class="fs-8 fw-lighter">Middle Name</label>
                                                <input type="text" id="middle-name" class="form-control" minlength="2"
                                                    maxlength="50"
                                                    value="<?php echo htmlspecialchars($clientDetails['MiddleName'])?>"
                                                    autocomplete="additional-name">
                                            </div>
                                        </div>

                                        <!-- Personal Info Fields -->
                                        <div class="row mt-2">
                                            <div class="col-12 col-md-3 mb-3">
                                                <label class="fs-8 fw-lighter">Birthday</label>
                                                <input type="text" id="birthday" class="form-control"
                                                    placeholder="Birthday (YYYY-MM-DD)" required
                                                    value="<?php echo $clientDetails['Birthday']?>">
                                            </div>
                                            <div class="col-12 col-md-3 mb-3">
                                                <label class="fs-8 fw-lighter">Religion</label>
                                                <input type="text" id="religion" class="form-control"
                                                    value="<?php echo $clientDetails['Religion']?>">
                                            </div>
                                            <div class="col-12 col-md-3 mb-3">
                                                <label class="fs-8 fw-lighter">Nationality</label>
                                                <input type="text" id="nationality" class="form-control" required
                                                    value="<?php echo $clientDetails['Nationality']?>">
                                            </div>
                                            <div class="col-12 col-md-3 mb-3">
                                                <label class="fs-8 fw-lighter">Sex</label>
                                                <select class="form-select">
                                                    <option value=""
                                                        <?php if (!isset($clientDetails['Sex'])) echo 'selected'; ?>>
                                                        Sex</option>
                                                    <option value="M"
                                                        <?php if (($clientDetails['Sex'] ?? '') === 'M') echo 'selected'; ?>>
                                                        M</option>
                                                    <option value="F"
                                                        <?php if (($clientDetails['Sex'] ?? '') === 'F') echo 'selected'; ?>>
                                                        F</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Address Fields -->
                                        <div class="row mt-2">
                                            <div class="col-12 col-md-6 mb-3">
                                                <label class="fs-8 fw-lighter">Home Address</label>
                                                <input type="text" id="h-address" class="form-control"
                                                    value="<?php echo $clientDetails['Address']?>">
                                            </div>
                                            <div class="col-12 col-md-6 mb-3">
                                                <label class="fs-8 fw-lighter">Office Address</label>
                                                <input type="text" id="o-address" class="form-control"
                                                    value="<?php echo $clientDetails['OfficeAddress']?>">
                                            </div>
                                        </div>

                                        <!-- Insurance and Occupation -->
                                        <div class="row mt-2">
                                            <div class="col-12 col-md-6 mb-3">
                                                <label class="fs-8 fw-lighter">Dental Insurance</label>
                                                <input type="text" id="insurance" class="form-control"
                                                    value="<?php echo $clientDetails['DentalInsurance']?>">
                                            </div>
                                            <div class="col-12 col-md-6 mb-3">
                                                <label class="fs-8 fw-lighter">Occupation</label>
                                                <input type="text" id="occupation" class="form-control"
                                                    value="<?php echo $clientDetails['Occupation']?>">
                                            </div>
                                        </div>

                                        <!-- Contact Information -->
                                        <div class="row mt-2">
                                            <div class="col-12 col-md-6 mb-3">
                                                <label class="fs-8 fw-lighter">Phone Number</label>
                                                <input type="number" id="phone-number" class="form-control"
                                                    value="<?php echo $clientDetails['Number']?>">
                                            </div>
                                            <div class="col-12 col-md-6 mb-3">
                                                <label class="fs-8 fw-lighter">Email</label>
                                                <input type="text" id="email" class="form-control"
                                                    value="<?php echo $clientDetails['Email']?>" disabled>
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
                                                    value="<?php echo $clientDetails['PreviousDentist']?>">
                                            </div>
                                            <div class="col-12 col-md-6 mb-3">
                                                <label class="fs-8 fw-lighter">Last Visit</label>
                                                <input type="text" id="last-visit" class="form-control"
                                                    value="<?php echo $clientDetails['LastVisit']?>">
                                            </div>
                                        </div>

                                        <!-- Medical History Section -->
                                        <h5 class="h6 card-title mt-4">
                                            <span data-feather="info" class="feather-sm me-1"></span>Medical History
                                        </h5>

                                        <div class="row mt-2">
                                            <div class="col-12 col-md-6 mb-3">
                                                <label class="fs-8 fw-lighter">Physician Name</label>
                                                <input type="text" id="physician-name" class="form-control" required
                                                    minlength="2" maxlength="50"
                                                    value="<?php echo htmlspecialchars($clientDetails['PhysicianName'])?>"
                                                    autocomplete="PhysicianName"
                                                    value="<?php echo $clientDetails['PhysicianName']?> ">
                                            </div>
                                            <div class="col-12 col-md-6 mb-3">
                                                <label class="fs-8 fw-lighter">Specialty</label>
                                                <input type="text" id="specialty" class="form-control"
                                                    value="<?php echo $clientDetails['Specialty']?>">
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-12 col-md-6 mb-3">
                                                <label class="fs-8 fw-lighter">Blood Type</label>
                                                <input type="text" id="bloodType" class="form-control" required
                                                    value="<?php echo $clientDetails['BloodType']?>">
                                            </div>
                                            <div class="col-12 col-md-6 mb-3">
                                                <label class="fs-8 fw-lighter">Blood Pressure</label>
                                                <input type="text" id="bloodPressure" class="form-control" required
                                                    value="<?php echo $clientDetails['BloodPressure']?>">
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
                                                    <select class="form-select" multiple size="5" id="allergy-select"
                                                        name="allergies[]">
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
                                                <select class="form-select mb-3" multiple size="14" id="illness-select"
                                                    name="illness[]">
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

                                        <!-- Update Button -->
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="d-flex justify-content-end">
                                                    <button class="btn btn-secondary" style="min-width: 120px;"
                                                        id="btnProceed">
                                                        <span class="me-2" data-feather="edit"></span>Update
                                                    </button>
                                                </div>
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
                                                <p>Are you sure you want to update this patient's data?</p>

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




                                <div class="toast align-items-center text-white bg-primary border-0" id="toast-alert"
                                    role="alert" aria-live="assertive" aria-atomic="true">
                                    <div class="d-flex">
                                        <div class="toast-body">
                                            <p id="toast-message"></p>
                                        </div>
                                        <button type="button" class="btn-close btn-close-white me-2 m-auto"
                                            data-bs-dismiss="toast" aria-label="Close"></button>
                                    </div>
                                </div>

                            </div>
                            <h1 class="h3 mb-3">Treatment <strong>Record</strong> </h1>
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="datatable" class="table table-hover w-100 mt-1">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Transaction Code</th>
                                                            <th>Treatment Date</th>
                                                            <th>Treatment Name</th>
                                                            <th>Doctor</th>
                                                            <th>Treatment Cost</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($paymentData as $transaction): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($transaction['Transaction_Code']) ?>
                                                            </td>
                                                            <td><?= htmlspecialchars($transaction['Treatment_Date']) ?>
                                                            </td>
                                                            <td><?= htmlspecialchars($transaction['Treatment_Name']) ?>
                                                            </td>
                                                            <td><?= htmlspecialchars($transaction['Dentist']) ?>
                                                            </td>
                                                            <td>₱<?= htmlspecialchars($transaction['Treatment_Cost']) ?>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan="4" class="text-end">Total:</th>
                                                            <th></th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <div class="d-flex justify-content-end">
                                                        <button class="btn btn-secondary" id="createPDF">
                                                            <i data-feather="file" class="me-2"></i>Generate Record
                                                            PDF
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
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


    <script src="three.js/three.min.js"></script>

    <script src="STLLoader.js"></script>
    <script src='TrackballControls.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tween.js/18.6.4/tween.umd.js"></script>




    <script src="../js/app.js"></script>
</body>

</html>