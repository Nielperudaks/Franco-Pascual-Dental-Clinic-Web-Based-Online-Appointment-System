<?php
session_start();
if (!isset($_SESSION["userID"]) || $_SESSION['valid'] != true) {
    header("Location: ../Patient_Login/index.php");
    exit;
}
$conn = require __DIR__ . "../../connection.php";
$query = "SELECT * FROM tbl_clients WHERE Client_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $_SESSION["userID"]);
$stmt->execute();
$result = $stmt->get_result();
$validateUser = $result->fetch_assoc();
$profileImage = $validateUser['Image'] ?? 'img/avatars/avatar-6.jpg';
$clientID = $validateUser['Client_ID'];
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
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4@4/bootstrap-4.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
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
    /* Responsive Table Styles */
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.9rem;
        }
        .btn-sm {
            padding: 0.2rem 0.4rem;
        }
    }
    /* Ensure long text doesn't break layout */
    .table td {
        max-width: 250px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    </style>
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
    <script>
    $(document).ready(function() {
        var table = $('#datatable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [
                [2, 'desc']
            ], // Sort by appointment date by default
            language: {
                paginate: {
                    previous: "<i class='fas fa-chevron-left'>",
                    next: "<i class='fas fa-chevron-right'>"
                }
            },
            drawCallback: function() {
                // Reinitialize Feather icons after table draw
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
                // Reinitialize tooltips after table draw
                var tooltipTriggerList = [].slice.call(document.querySelectorAll(
                    '[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
        });
        $(document).on('click', '.cancel-appointment', function(e) {
            e.preventDefault();
            var transactionCode = $(this).attr('id');
            console.log(transactionCode);
            $("#myModal").modal('show');
            $("#transactionCode").val(transactionCode);
        });
        // Use event delegation for appointment report buttons
        $(document).on('click', '.appointment-report', function(e) {
            var code = $(this).attr('id');
            $("#report-modal").modal('show');
            $.ajax({
                url: 'process-report.php',
                method: 'POST',
                data: {
                    code: code
                },
                success: function(response) {
                    $('#report').val(response.report);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert(
                        'An error occurred while trying to store the data in the database.'
                    );
                }
            });
        });
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        $('#edit-user-confirm').on('click', function() {
            var clientID = "<?= htmlspecialchars($validateUser["Client_ID"]) ?>";
            var firstName = $('#edit-first-name').val().trim();
            var lastName = $('#edit-last-name').val().trim();
            var address = $('#edit-address').val().trim();
            var occupation = $('#edit-occupation').val().trim();
            var number = $('#edit-number').val().trim();
            var fileInput = $('#file')[0].files[0];
            console.log(firstName);
            var formData = new FormData();
            formData.append('clientID', clientID);
            formData.append('firstName', firstName);
            formData.append('lastName', lastName);
            formData.append('address', address);
            formData.append('occupation', occupation);
            formData.append('number', number);
            formData.append('file', fileInput);
            if (fileInput) {
                formData.append('file', fileInput);
                formData.append('isFileChanged', true);
            } else {
                formData.append('isFileChanged', false);
            }
            Swal.fire({
                title: 'Processing...',
                html: 'Please wait while we update',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            $.ajax({
                url: 'process-edit-user.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    showToast(response);
                    $('#edit-user-modal').modal('hide');
                    Swal.fire(
                        'Update success!',
                        response,
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        });
        $('#file').on('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                // Check if the file is an image
                const validImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if ($.inArray(file.type, validImageTypes) < 0) {
                    showToast('Please select a valid image file (JPEG, PNG, GIF).');
                    setTimeout(function() {
                        location.reload();
                    }, 3500);
                    return;
                }
                // Check if the file size exceeds 2MB
                const maxSize = 3 * 1024 * 1024; // 3MB in bytes
                if (file.size > maxSize) {
                    showToast('The file size exceeds 2MB. Please select a smaller file.');
                    setTimeout(function() {
                        location.reload();
                    }, 3500);
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#profile-edit-img').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });
        $("#cancel").click(function() {
            $("#myModal").modal('hide');
        });
        $("#cancel2").click(function() {
            $("#report-modal").modal('hide');
        });
        $(".cancel-appointment").click(function() {
            //var transactionCode = $(this).closest('tr').find('.transaction-code').text();
            // Show the modal and display the Transaction_Code
            var transactionCode = $(this).attr('id');
            $("#myModal").modal('show');
            $("#transactionCode").val(transactionCode);
        });
        $("#confirmBtn").click(function() {
            var transactionCode = $("#transactionCode").val();
            Swal.fire({
                title: 'Processing...',
                html: 'Please wait while we update',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            $.ajax({
                url: 'process-cancellation.php',
                method: 'POST',
                data: {
                    transactionCode: transactionCode,
                },
                success: function(response) {
                    $("#myModal").modal('hide');
                    Swal.fire(
                        'Appointment follow up created!',
                        response,
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                    showToast('Appointment Cancelled');
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert(
                        'An error occurred while trying to store the data in the database.'
                    );
                }
            });
        });
        $(".appointment-report").click(function() {
            var code = $(this).attr('id');
            console.log(code);
            $("#report-modal").modal('show');
            $.ajax({
                url: 'process-report.php',
                method: 'POST',
                data: {
                    code: code
                },
                success: function(response) {
                    $('#report').val(response.report);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert(
                        'An error occurred while trying to store the data in the database.'
                    );
                }
            });
        });
        $(".edit-user-btn").click(function() {
            $('#edit-first-name').val("<?= htmlspecialchars($validateUser["FirstName"]) ?>");
            $('#edit-last-name').val("<?= htmlspecialchars($validateUser["LastName"]) ?>");
            $('#edit-middle-name').val("<?= $validateUser["MiddleName"] ?>");
            $('#edit-address').val("<?= htmlspecialchars($validateUser["Address"]) ?>");
            $('#edit-number').val("<?= htmlspecialchars($validateUser["Number"]) ?>");
            $('#edit-age').val("<?= $validateUser["Age"] ?>");
            $('#edit-email').val("<?= $validateUser["Email"] ?>");
            $('#edit-occupation').val("<?= $validateUser["Occupation"] ?>");
            $('.client-id').text("<?= htmlspecialchars($validateUser["Client_ID"]) ?>");
            $("#edit-user-modal").modal('show');
        });
        $('#edit-user-cancel').click(function() {
            $('#edit-user-modal').modal('hide');
        });
        $('[data-bs-toggle="tooltip"]').tooltip();
        $('.dt-length label').remove();
        $('.col-md-auto.me-auto').removeClass('col-md-auto me-auto').addClass('col-md-auto');
        $('.dt-length label').remove();
        $('.dt-search label').remove();
        $('.row.mt-2.justify-content-between').removeClass('row mt-2 justify-content-between').addClass(
            'row justify-content-between');
        $('.dt-paging-button.page-item.active').removeClass('dt-paging-button page-item active').addClass(
            'dt-paging-button page-item');
        $('.dt-search').prepend('<span class="me-3" data-feather="search"></span>');
        feather.replace();
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
        const nameRegex =
            /^[A-Za-zÀ-ÿ][A-Za-zÀ-ÿ\s'-]*$/; // Letters, spaces, hyphens, apostrophes, accented characters
        const addressRegex =
            /^[A-Za-z0-9À-ÿ\s,.'-]+$/; // Letters, numbers, spaces, commas, periods, hyphens, apostrophes
        const occupationRegex = /^[A-Za-zÀ-ÿ\s&-]+$/; // Letters, spaces, ampersand, hyphens
        const phoneRegex = /^09\d{9}$/; // Starts with 09 followed by 9 digits
        function validateName(value) {
            if (/[-']{2,}/.test(value)) return false;
            if (/[-']$/.test(value)) return false;
            if (/\d/.test(value)) return false;
            return nameRegex.test(value);
        }
        $('#edit-first-name').on('input', function() {
            const value = $(this).val().trim();
            const isValid = validateName(value) && value.length >= 2 && value.length <= 50;
            if (!isValid && value !== '') {
                $(this).addClass('is-invalid');
                if (!$(this).next('.invalid-feedback').length) {
                    $(this).after(
                        '<div class="invalid-feedback">Please enter a valid first name (letters only, hyphens and apostrophes allowed between letters)</div>'
                    );
                }
            } else {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            }
        });
        // Last Name Validation
        $('#edit-last-name').on('input', function() {
            const value = $(this).val().trim();
            const isValid = validateName(value) && value.length >= 2 && value.length <= 50;
            if (!isValid && value !== '') {
                $(this).addClass('is-invalid');
                if (!$(this).next('.invalid-feedback').length) {
                    $(this).after(
                        '<div class="invalid-feedback">Please enter a valid last name (letters only, hyphens and apostrophes allowed between letters)</div>'
                    );
                }
            } else {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            }
        });
        $('#edit-address').on('input', function() {
            const value = $(this).val().trim();
            const isValid = addressRegex.test(value) && value.length >= 5 && value.length <= 200;
            if (!isValid && value !== '') {
                $(this).addClass('is-invalid');
                if (!$(this).next('.invalid-feedback').length) {
                    $(this).after(
                        '<div class="invalid-feedback">Please enter a valid address (letters, numbers, and common punctuation only)</div>'
                    );
                }
            } else {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            }
        });
        $('#edit-occupation').on('input', function() {
            const value = $(this).val().trim();
            const isValid = occupationRegex.test(value) && value.length >= 2 && value.length <= 50;
            if (!isValid && value !== '') {
                $(this).addClass('is-invalid');
                if (!$(this).next('.invalid-feedback').length) {
                    $(this).after(
                        '<div class="invalid-feedback">Please enter a valid occupation (letters and basic punctuation only)</div>'
                    );
                }
            } else {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            }
        });
        $('#edit-number').on('input', function() {
            const value = $(this).val().trim();
            const isValid = phoneRegex.test(value);
            if (!isValid && value !== '') {
                $(this).addClass('is-invalid');
                if (!$(this).next('.invalid-feedback').length) {
                    $(this).after(
                        '<div class="invalid-feedback">Please enter a valid phone number starting with 09 (11 digits total)</div>'
                    );
                }
            } else {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            }
        });
        $('form').on('submit', function(e) {
            let isValid = true;
            $('#edit-first-name, #edit-last-name, #edit-address, #edit-occupation, #edit-number').each(
                function() {
                    const value = $(this).val().trim();
                    $(this).trigger('input');
                    if ($(this).hasClass('is-invalid') || value === '') {
                        isValid = false;
                    }
                });
            if (!isValid) {
                e.preventDefault();
                alert('Please fill all fields correctly before submitting.');
            }
        });
        const allowedPatterns = {
            name: /[A-Za-zÀ-ÿ\s'-]/,
            address: /[A-Za-z0-9À-ÿ\s,.'-]/,
            occupation: /[A-Za-zÀ-ÿ\s&-]/,
            phone: /[0-9]/
        };
        function handleInput(event, pattern) {
            const char = String.fromCharCode(event.keyCode || event.which);
            if (event.keyCode === 8 || event.keyCode === 46 || event.keyCode === 37 || event.keyCode === 39) {
                return true;
            }
            if (!pattern.test(char)) {
                event.preventDefault();
                return false;
            }
            return true;
        }
        function handlePaste(event, pattern) {
            const pastedText = (event.clipboardData || window.clipboardData).getData('text');
            if (![...pastedText].every(char => pattern.test(char))) {
                event.preventDefault();
                return false;
            }
            return true;
        }
        $('#edit-first-name').on('keypress', function(e) {
            return handleInput(e, allowedPatterns.name);
        }).on('paste', function(e) {
            return handlePaste(e, allowedPatterns.name);
        });
        $('#edit-last-name').on('keypress', function(e) {
            return handleInput(e, allowedPatterns.name);
        }).on('paste', function(e) {
            return handlePaste(e, allowedPatterns.name);
        });
        $('#edit-address').on('keypress', function(e) {
            return handleInput(e, allowedPatterns.address);
        }).on('paste', function(e) {
            return handlePaste(e, allowedPatterns.address);
        });
        $('#edit-occupation').on('keypress', function(e) {
            return handleInput(e, allowedPatterns.occupation);
        }).on('paste', function(e) {
            return handlePaste(e, allowedPatterns.occupation);
        });
        $('#edit-number').on('keypress', function(e) {
            if (!allowedPatterns.phone.test(String.fromCharCode(e.keyCode || e.which))) {
                e.preventDefault();
                return false;
            }
            const currentValue = $(this).val();
            if (currentValue.length === 0 && e.key !== '0') {
                e.preventDefault();
                return false;
            }
            if (currentValue.length === 1 && currentValue[0] === '0' && e.key !== '9') {
                e.preventDefault();
                return false;
            }
            if (currentValue.length >= 11) {
                e.preventDefault();
                return false;
            }
            return true;
        }).on('paste', function(e) {
            const pastedText = (event.clipboardData || window.clipboardData).getData('text');
            if (!phoneRegex.test(pastedText)) {
                e.preventDefault();
                return false;
            }
            return true;
        });
    });
    </script>
    <script>
    $(document).ready(function() {
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
        function updateProgress(value) {
            const percent = Math.round(value);
            $('.progress-bar').css('width', percent + '%');
            $('.progress-text').text(percent + '%');
        }
        function hideLoadingScreen() {
            $('#loading-screen').fadeOut(200, function() {
                $(this).remove();
            });
        }
    });
    </script>
    <title>Client Dashboard</title>
    <link href="css/app.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
    <div id="loading-screen">
        <div class="spinner"></div>
        <div class="progress-container">
            <div class="progress-bar"></div>
        </div>
        <div class="loading-text">Loading... <span class="progress-text">0%</span></div>
    </div>
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
        <div id="toastContainer"></div>
    </div>
    <div class="wrapper">
        <nav id="sidebar" class="sidebar js-sidebar">
            <div class="sidebar-content js-simplebar">
                <div class="text-center">
                    <img src="img/photos/Logo.png" class="img-fluid rounded-circle" width="144" height="144" />
                    <a class="sidebar-brand" href="index.php">
                        <p class="align-middle">Franco - Pascual</p>
                    </a>
                </div>
                <ul class="sidebar-nav">
                    <li class="sidebar-header">
                        Appointment
                    </li>
                    <li class="sidebar-item ">
                        <a class="sidebar-link" href="index.php">
                            <i class="align-middle" data-feather="calendar"></i> <span
                                class="align-middle">Calendar</span>
                        </a>
                    </li>
                    <li class="sidebar-header">
                        User
                    </li>
                    <li class="sidebar-item active">
                        <a class="sidebar-link" href="Transactions.php">
                            <i class="align-middle" data-feather="user"></i> <span class="align-middle">Profile</span>
                        </a>
                    </li>
                    <li class="sidebar-item ">
                        <a class="sidebar-link" href="appointment-records.php">
                            <i class="align-middle" data-feather="book"></i> <span class="align-middle">Appointment
                                Records</span>
                        </a>
                    </li>
                    <li class="sidebar-header">
                        Payment
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="Micro-transactions/Microtransactions.php">
                            <i class="align-middle" data-feather="credit-card"></i> <span
                                class="align-middle">Microtransactions</span>
                        </a>
                    </li>
                    <li class="sidebar-header">
                        Others
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="logout.php">
                            <i class="align-middle" data-feather="log-out"></i> <span class="align-middle">Log
                                out</span>
                        </a>
                    </li>
            </div>
        </nav>
        <div class="main">
            <nav class="navbar navbar-expand navbar-light navbar-bg">
                <a class="sidebar-toggle js-sidebar-toggle">
                    <i class="hamburger align-self-center"></i>
                </a>
                <div class="navbar-collapse collapse">
                    <ul class="navbar-nav navbar-align">
                        <p class=" d-none d-sm-inline-block pt-2">
                            <img src="<?php echo htmlspecialchars($profileImage); ?>"
                                class="avatar img-fluid rounded me-1" alt="Charles Amar" /> <span
                                class="text-dark"><?= htmlspecialchars($validateUser["FirstName"]) ?></span>
                        </p>
                    </ul>
                </div>
            </nav>
            <main class="content">
                <div class="container-fluid p-0">
                    <h1 class="h3 mb-3">User <strong>Profile</strong> </h1>
                    <div class="row">
                        <div class="col-md-4 col-xl-3">
                            <div class="card mb-3">
                                <div class="card-header d-flex justify-content-between">
                                    <h5 class="card-title mb-0 pt-2">Profile Details</h5>
                                    <button class="btn edit-user-btn" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Edit Profile"> <i class="feather-sm me-1" data-feather="edit"></i>
                                    </button>
                                </div>
                                <div class="card-body text-center">
                                    <img id="profile-img" src="<?php echo htmlspecialchars($profileImage); ?>"
                                        alt="Christina Mason" class="img-fluid rounded-circle mb-2" width="128"
                                        height="128" />
                                    <h5 class="card-title mb-0"><?= htmlspecialchars($validateUser["FirstName"]) ?>
                                        <?= htmlspecialchars($validateUser["MiddleName"]) ?>
                                        <?= htmlspecialchars(string: $validateUser["LastName"]) ?>
                                    </h5>
                                    <div class="text-muted mb-2">Patient
                                    </div>
                                </div>
                                <hr class="my-0" />
                                <div class="card-body">
                                    <h5 class="h6 card-title">About</h5>
                                    <ul class="list-unstyled mb-0">
                                        <?php
                                        if ($validateUser["Address"] === null || $validateUser["Address"] === '') {
                                            $validateUser["Address"] = 'Not Set';
                                        }
                                        if ($validateUser["Occupation"] === null || $validateUser["Occupation"] === '') {
                                            $validateUser["Occupation"] = 'Not Set';
                                        }
                                        if ($validateUser["Age"] === null || $validateUser["Age"] === '') {
                                            $validateUser["Age"] = 'Not Set';
                                        }
                                        if ($validateUser["Number"] === null || $validateUser["Number"] === '') {
                                            $validateUser["Number"] = 'Not Set';
                                        }
                                        ?>
                                        <li class="mb-1"><span data-feather="home" class="feather-sm me-1"></span> Lives
                                            in <span
                                                class="text-primary"><?= htmlspecialchars($validateUser["Address"]) ?></span>
                                        </li>
                                        <li class="mb-1"><span data-feather="briefcase" class="feather-sm me-1"></span>
                                            Works at <span
                                                class="text-primary"><?= htmlspecialchars($validateUser["Occupation"]) ?></span>
                                        </li>
                                    </ul>
                                </div>
                                <hr class="my-0" />
                                <div class="card-body">
                                    <h5 class="h6 card-title">Contacts</h5>
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-1"><span data-feather="phone" class="feather-sm me-1"></span>
                                            Number: <span
                                                class="text-primary"><?= htmlspecialchars($validateUser["Number"]) ?></span>
                                        </li>
                                        <li class="mb-1"><span data-feather="mail" class="feather-sm me-1"></span>
                                            Email: <span
                                                class="text-primary"><?= htmlspecialchars($validateUser["Email"]) ?></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 col-xl-9">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Transactions</h5>
                                </div>
                                <div class="card-body ">
                                    <div class="table-responsive">
                                        <table id="datatable" class="table dt-responsive nowrap" cellspacing="0"
                                            width="100%">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Service</th>
                                                    <th>Doctor</th>
                                                    <th>Book Date</th>
                                                    <th>Time Slot</th>
                                                    <th>Status</th>
                                                    <th>Transact Code</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $conn = require __DIR__ . "../../connection.php";
                                                $query = "SELECT * FROM tbl_transaction WHERE Client_ID = {$_SESSION["userID"]}";
                                                $result = $conn->query($query);
                                                while ($validateUser = $result->fetch_assoc()) {
                                                    if ($validateUser["Status"] === 'Waiting Approval') {
                                                        $addClass = 'style="background-color: #F4D951"';
                                                        $td = '<td>
														<button class="btn btn-xs cancel-appointment" style ="background-color: #F5F7FB" id="' . $validateUser["Transaction_Code"] . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Cancel Appointment"><span data-feather="x"></span></button>													
													</td>';
                                                    } else if ($validateUser["Status"] === 'Approved') {
                                                        $addClass = 'style="background-color: #9AA3D2"';
                                                        $td = '<td>
														<button class="btn btn-xs cancel-appointment" style ="background-color: #F5F7FB" id="' . $validateUser["Transaction_Code"] . '"  data-bs-toggle="tooltip" data-bs-placement="top" title="Cancel Appointment"><span data-feather="x"></span></button>
													</td>';
                                                    } else if ($validateUser["Status"] === 'Done') {
                                                        $addClass = 'style="background-color: #97DBC2"';
                                                        $td = '<td>
														<button class="btn btn-xs appointment-report" style ="background-color: #F5F7FB" id="' . $validateUser["Transaction_Code"] . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Appointment Result"><span data-feather="book-open" ></span></button>
													</td>';
                                                    } else {
                                                        $addClass = 'style="background-color: #FFB0B0"';
                                                        $td = '<td>		
													</td>';
                                                    }
                                                    ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $validateUser["Service"]; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $validateUser["Doctor"]; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $validateUser["AppointmentDate"]; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $validateUser["AppointmentTime"]; ?>
                                                    </td>
                                                    <td>
                                                        <p class='badge me-1 my-1 fs-9' <?php echo $addClass; ?>>
                                                            <?php echo $validateUser["Status"]; ?>
                                                        </p>
                                                    </td>
                                                    <td class="transaction-code">
                                                        <?php echo $validateUser["Transaction_Code"]; ?>
                                                    </td>
                                                    <?php echo $td ?>
                                                </tr>
                                                <?php
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade custom-modal" id="myModal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Appointment Cancellation</h5>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to cancel this appointment?</p>
                                    <input type="hidden" id="transactionCode" name="" value="">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn " data-dismiss="modal" id="cancel">No</button>
                                    <button type="button" class="btn btn-secondary" id="confirmBtn">Yes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade custom-modal" id="report-modal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Appointment Report</h5>
                                </div>
                                <div class="modal-body">
                                    <textarea id="report" class="form-control" rows="4" disabled></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn " data-dismiss="modal" id="cancel2">Back</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade custom-modal" id="edit-user-modal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Edit Profile</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="text-center">
                                        <img id="profile-edit-img" src="<?php echo htmlspecialchars($profileImage); ?>"
                                            alt="Christina Mason" class="img-fluid rounded-circle mb-2" width="128"
                                            height="128" />
                                        <div class="text-muted mb-2">Patient
                                        </div>
                                    </div>
                                    <label class="form-label" for="file">Change Photo</label>
                                    <input type="file" class="form-control" id="file" name="file" />
                                    <div class="mb-3">
                                        <label for="edit-first-name" class="form-label">First Name:</label>
                                        <input type="text" id="edit-first-name" class="form-control" required
                                            minlength="2" maxlength="50" placeholder="Enter your first name">
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit-last-name" class="form-label">Last Name:</label>
                                        <input type="text" id="edit-last-name" class="form-control" required
                                            minlength="2" maxlength="50" placeholder="Enter your last name">
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit-address" class="form-label">Address:</label>
                                        <input type="text" id="edit-address" class="form-control" required minlength="5"
                                            maxlength="200" placeholder="Enter your complete address">
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit-occupation" class="form-label">Occupation:</label>
                                        <input type="text" id="edit-occupation" class="form-control" required
                                            minlength="2" maxlength="50" placeholder="Enter your occupation">
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit-number" class="form-label">Number:</label>
                                        <input type="tel" id="edit-number" class="form-control" required
                                            pattern="09[0-9]{9}" maxlength="11" placeholder="09XXXXXXXXX">
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit-email" class="form-label">Email:</label>
                                        <input type="email" id="edit-email" class="form-control" disabled>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn" data-dismiss="modal"
                                        id="edit-user-cancel">Cancel</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                        id="edit-user-confirm">Update</button>
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
                                Clinic and Orthodontics INC. <a class="text-muted" href="#" target="_blank"></a> &copy;
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
    <script src="js/app.js"></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.js"></script>
    <script>
    </script>
</body>
</html>