<?php
session_start();
if (!isset($_SESSION["userID"])) {
    header("Location: ../Login-Registration/");
    exit();
}
$conn = require __DIR__ . "../../connection.php";
$query = "SELECT * FROM tbl_admin WHERE Admin_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION["userID"]);
$stmt->execute();
$result = $stmt->get_result();
$validateUser = $result->fetch_assoc();
if (!$validateUser || $validateUser['Access_Level'] != 1) {
    header("Location: ../Login-Registration/");
    exit();
}
function getValidatedDate($requestMonth, $requestYear)
{
    $currentYear = (int) date('Y');
    $currentMonth = (int) date('n');
    $month = isset($requestMonth) ? (int) $requestMonth : $currentMonth;
    $year = isset($requestYear) ? (int) $requestYear : $currentYear;
    $minYear = $currentYear - 5;  
    $maxYear = $currentYear + 1;  
    if ($year < $minYear || $year > $maxYear) {
        $year = $currentYear;  
    }
    if ($month < 1 || $month > 12) {
        $month = $currentMonth;  
    }
    if ($year == $maxYear && $month > $currentMonth) {
        $month = $currentMonth;  
    }
    return [
        'month' => $month,
        'year' => $year,
        'isModified' => ($month != $requestMonth || $year != $requestYear)
    ];
}
$month = date('m');
$year = date('Y');
if (isset($_GET["month"]) && isset($_GET["year"])) {
    $requestMonth = $_GET['month'];
    $requestYear = $_GET['year'];
    $validatedDate = getValidatedDate($requestMonth, $requestYear);
    if ($validatedDate['isModified']) {
        $redirectUrl = 'Admin-Dashboard/Doctor-Section/index.php?month=' .
            $validatedDate['month'] . '&year=' . $validatedDate['year'];
        if (!headers_sent()) {
            header("Location: " . $redirectUrl);
            exit();
        } else {
            echo "<script>window.location.href = '" . $redirectUrl . "';</script>";
            exit();
        }
    }
    $month = $validatedDate['month'];
    $year = $validatedDate['year'];
}
$queryApproved = "SELECT AppointmentDate, Client_ID 
                  FROM tbl_transaction 
                  WHERE Status = 'Approved' 
                  AND Doctor_ID = ? ";
$stmtApproved = $conn->prepare($queryApproved);
$stmtApproved->bind_param("s", $validateUser['Admin_ID']); 
if ($stmtApproved->execute()) {
    $resultApproved = $stmtApproved->get_result();
    $approvedAppointments = [];
    while ($row = $resultApproved->fetch_assoc()) {
        $approvedAppointments[] = $row;
    }
    $stmtApproved->close();
}
$conn->close();
include("generate-calendar.php");
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
    <title>Calendar</title>
    <link href="../css/app.css" rel="stylesheet">
    <link href="../css/calendar-style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
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
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
        console.log(<?php echo json_encode($validateUser["Username"]); ?>);
        $.ajax({
            url: 'fetch-appointments.php',
            method: 'POST',
            data: {
                doctorID: <?php echo json_encode($validateUser["Username"]); ?>
            },
            success: function(response) {
                try {
                    var data = JSON.parse(response);
                    console.log(data);
                    var $appointmentCard = $('.appointment-card');
                    if (!data || data.length === 0) {
                        var noApproved = `
                    <p class="badge bg-secondary fs-6">No Appointments Today!</p>`;
                        $appointmentCard.append(noApproved);
                    } else {
                        var $tbody = $('#tbl-appointments-body');
                        $tbody.empty();
                        data.forEach(function(appointment) {
                            var row = `
                    <tr class="appointment-row  fs-6 " data-id="${appointment.Transaction_Code}" data-client="${appointment.Client_ID}" data-firstname="${appointment.FirstName}">
                        <td class="text-primary py-2" data-id="${appointment.Transaction_Code}"><span data-feather="flag" class="feather-m me-3"></span>${appointment.FirstName}</td>
                        <td class="text-primary py-2">${appointment.Service}</td>		                     	
                        <td class="text-primary py-2">${appointment.AppointmentTime}</td>                   
                    </tr>
                `;
                            $tbody.append(row);
                            console.log(appointment);
                        });
                        feather.replace();
                        $('[data-bs-toggle="tooltip"]').tooltip();
                    }
                } catch (error) {
                    console.error('Invalid JSON response', error);
                    console.log(response); 
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('An error occurred while trying to fetch appointments.');
            }
        });
        $(document).on('click', '.appointment-row', function() {
            var transactionID = $(this).data('id');
            var firstName = $(this).data('firstname');
            var service = $(this).data('service');
            var appointmentTime = $(this).data('time');
            var clientID = $(this).data('client');
            console.log(clientID);
            window.location.href =
                `encryptURL.php?clientID=${clientID}&transactionID=${transactionID}&firstName=${encodeURIComponent(firstName)}`;
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
</head>
<body>
    <div class="wrapper">
        <div id="loading-screen">
            <div class="spinner"></div>
            <div class="progress-container">
                <div class="progress-bar"></div>
            </div>
            <div class="loading-text">Loading... <span class="progress-text">0%</span></div>
        </div>
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
                    <h1 class="h3 mb-3">Appointment <strong>Calendar</strong> </h1>
                    <div class="row flex-fill">
                        <div class="col">
                            <div class="card flex-fill">
                                <div class="card-body d-flex py-0">
                                    <div class="align-self-center w-100">
                                        <?php
                                        echo build_calendar($month, $year);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-xl-3">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="h6 card-title">Legends</h5>
                                    <p class="badge bg-secondary me-1 my-1 fs-6"><span data-feather="check"
                                            class="feather-sm me-1"></span>Available</p>
                                    <p class="badge bg-secondary me-1 my-1 fs-6"><span data-feather="flag"
                                            class="feather-sm me-1"></span>Booked</p>
                                    <p class="badge bg-secondary me-1 my-1 fs-6"><span data-feather="lock"
                                            class="feather-sm me-1"></span>Locked</p>
                                    <p class="badge bg-secondary me-1 my-1 fs-6"><span data-feather="x"
                                            class="feather-sm me-1"></span>Closed</p>
                                </div>
                                <hr class="my-0" />
                                <div class="card-body">
                                    <h5 class="h6 card-title">About</h5>
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-1"><span data-feather="map-pin"
                                                class="feather-sm me-1"></span>Address: <span
                                                class="text-muted fs-6">570 purok, Gate 1
                                                Sucat, Muntinlupa City</span> </li>
                                        <li class="mb-1"><span data-feather="calendar"
                                                class="feather-sm me-1"></span>Closed: <span
                                                class="text-muted fs-6">Saturday, Sunday</span></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card ">
                                <div class="card-body appointment-card">
                                    <h5 class=" card-title"><span data-feather="calendar"
                                            class="feather-m me-2"></span>Your Appointments Today</h5>
                                    <div class="table-responsive text-nowrap">
                                        <table id="tbl-appointments"
                                            class="table table-sm w-auto table-borderless table-hover dt-responsive nowrap"
                                            cellspacing="0" width="100%">
                                            <tbody id="tbl-appointments-body">
                                            </tbody>
                                        </table>
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
    <script src="../js/app.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@4.2.0/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@4.2.0/main.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@4.2.0/main.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/uuid@8.3.2/dist/umd/uuidv4.min.js'></script>
</body>
</html>