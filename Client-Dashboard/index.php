<?php
session_start();
if (!isset($_SESSION["userID"]) || $_SESSION['valid'] != true) {
    header("Location: ../Patient_Login/");
    exit;
}
    $conn = require __DIR__ . "../../connection.php";
    $query = "SELECT * FROM tbl_clients WHERE Client_ID = {$_SESSION["userID"]}";
    $result = $conn->query($query);
    $validateUser = $result->fetch_assoc();


    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    date_default_timezone_set('Asia/Manila');

    $userId = $_SESSION["userID"];
    $query = "SELECT AppointmentTime, AppointmentDate, Transaction_Code, Status FROM tbl_transaction WHERE Client_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);

    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $updateQueries = [];
    while ($row = $result->fetch_assoc()) {
        preg_match('/ - (\d{1,2}:\d{2})/', $row['AppointmentTime'], $time);
        $appointmentDateTime = new DateTime($row['AppointmentDate'] . ' ' . ltrim($time[0], '0 -'));
        $currentDateTime = new DateTime();
        if ($appointmentDateTime <= $currentDateTime) {
            if ($row['Status'] === 'Approved') {
                $updateQueries[] = [
                    'transaction_status' => 'Done',
                    'transaction_id' => $row['Transaction_Code']
                ];
                $clientStatus = 'Done';
            } else if ($row['Status'] === 'Waiting Approval') {
                $updateQueries[] = [
                    'transaction_status' => 'No Response',
                    'transaction_id' => $row['Transaction_Code']
                ];
                $clientStatus = 'No Response';
            }
        }
    }
    foreach ($updateQueries as $update) {
        $updateTransactionQuery = "UPDATE tbl_transaction SET Status = ? WHERE Transaction_Code = ?";
        $updateTransactionStmt = $conn->prepare($updateTransactionQuery);
        $updateTransactionStmt->bind_param("ss", $update['transaction_status'], $update['transaction_id']);
        $updateTransactionStmt->execute();
        $updateTransactionStmt->close();
    }

    $query = "SELECT COUNT(*) AS count FROM tbl_transaction WHERE Status IN ('Approved', 'Waiting Approval') AND Client_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "s",
        $userId
    );
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $validateUser = $row['count'];

        if ($validateUser == 0) {
            $updateClientQuery = "UPDATE tbl_clients SET Status = NULL WHERE Client_ID = ?";
            $updateClientStmt = $conn->prepare($updateClientQuery);
            $updateClientStmt->bind_param("s", $userId);
            $updateClientStmt->execute();
            $updateClientStmt->close();
        }
    }

    $query = "SELECT AppointmentDate FROM tbl_transaction WHERE Status = 'Approved' AND Client_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $userId);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $appointment = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
    } else {
        $appointment = null;
        $stmt->close();
        $conn->close();
    }
    function getValidatedDate($requestMonth, $requestYear) {
        
        $currentYear = (int)date('Y');
        $currentMonth = (int)date('n');
        
        
        $month = isset($requestMonth) ? (int)$requestMonth : $currentMonth;
        $year = isset($requestYear) ? (int)$requestYear : $currentYear;
        
        
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
            $redirectUrl = 'index.php?month=' . 
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



include("generate-calendar.php");
$profileImage = $validateUser['Image'] ?? 'img/avatars/avatar-6.jpg';
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
    <style>
        .tutorial-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .tutorial-card {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .sidebar-highlight {
            background-color: #f0f0f0;
            border: 2px solid #007bff;
        }
    </style>


    <title>Client Dashboard</title>

    <link href="css/app.css" rel="stylesheet">
    <link href="css/calendar_style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

    <script>
    $(document).ready(function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
    </script>
    <script>
        let currentCard = 1;
        const totalCards = 6;

        function nextCard() {
            $(`#card${currentCard}`).hide();
            currentCard++;
            $(`#card${currentCard}`).show();

            // Add specific highlight function for each card
            switch(currentCard) {
                case 2: highlightCalendar(); break;
                case 3: highlightProfile(); break;
                case 4: highlightAppointmentRecords(); break;
                case 5: highlightMicrotransactions(); break;
                case 6: highlightLogout(); break;
            }
        }

        function skipTutorial() {
            $.ajax({
                url: 'update_tutorial.php',
                method: 'POST',
                data: { updateTutorial: true },
                success: function() {
                    $('#tutorialOverlay').remove();
                }
            });
        }

        function completeTutorial() {
            $.ajax({
                url: 'update_tutorial.php',
                method: 'POST',
                data: { updateTutorial: true },
                success: function() {
                    $('#tutorialOverlay').remove();
                }
            });
        }
    </script>
</head>

<body>
<?php
    // Check if tutorial should be shown
    $userID = $_SESSION['userID'];
    $tutorialQuery = "SELECT Tutorial FROM tbl_clients WHERE Client_ID = ?";
    $stmt = $conn->prepare($tutorialQuery);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();

    $showTutorial = ($userData['Tutorial'] === null || $userData['Tutorial'] == 0);
    ?>

    <?php if($showTutorial): ?>
    <div class="tutorial-overlay" id="tutorialOverlay">
        <div class="tutorial-card" id="tutorialCard">
            <div id="card1" class="tutorial-step">
                <h3>Welcome to Franco - Pascual</h3>
                <p>This quick tutorial will help you navigate through our platform's key features.</p>
                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-secondary me-2" onclick="skipTutorial()">Skip</button>
                    <button class="btn btn-primary" onclick="nextCard()">Next</button>
                </div>
            </div>

            <div id="card2" class="tutorial-step" style="display:none;">
                <h3>Calendar</h3>
                <p>Here you can choose your preferred date and set up appointments easily.</p>
                <script>
                    function highlightCalendar() {
                        $('.sidebar-item').removeClass('sidebar-highlight');
                        $('.sidebar-item:contains("Calendar")').addClass('sidebar-highlight');
                    }
                    highlightCalendar();
                </script>
                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-secondary me-2" onclick="skipTutorial()">Skip</button>
                    <button class="btn btn-primary" onclick="nextCard()">Next</button>
                </div>
            </div>

            <div id="card3" class="tutorial-step" style="display:none;">
                <h3>Profile</h3>
                <p>Manage your profile information and view your transaction records here.</p>
                <script>
                    function highlightProfile() {
                        $('.sidebar-item').removeClass('sidebar-highlight');
                        $('.sidebar-item:contains("Profile")').addClass('sidebar-highlight');
                    }
                    highlightProfile();
                </script>
                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-secondary me-2" onclick="skipTutorial()">Skip</button>
                    <button class="btn btn-primary" onclick="nextCard()">Next</button>
                </div>
            </div>

            <div id="card4" class="tutorial-step" style="display:none;">
                <h3>Appointment Records</h3>
                <p>View and manage your medical appointment records conveniently.</p>
                <script>
                    function highlightAppointmentRecords() {
                        $('.sidebar-item').removeClass('sidebar-highlight');
                        $('.sidebar-item:contains("Appointment Records")').addClass('sidebar-highlight');
                    }
                    highlightAppointmentRecords();
                </script>
                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-secondary me-2" onclick="skipTutorial()">Skip</button>
                    <button class="btn btn-primary" onclick="nextCard()">Next</button>
                </div>
            </div>

            <div id="card5" class="tutorial-step" style="display:none;">
                <h3>Microtransactions</h3>
                <p>Access and review your payment records in the microtransactions section.</p>
                <script>
                    function highlightMicrotransactions() {
                        $('.sidebar-item').removeClass('sidebar-highlight');
                        $('.sidebar-item:contains("Microtransactions")').addClass('sidebar-highlight');
                    }
                    highlightMicrotransactions();
                </script>
                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-secondary me-2" onclick="skipTutorial()">Skip</button>
                    <button class="btn btn-primary" onclick="nextCard()">Next</button>
                </div>
            </div>

            <div id="card6" class="tutorial-step" style="display:none;">
                <h3>Logout</h3>
                <p>When you're finished, you can easily log out from this section.</p>
                <script>
                    function highlightLogout() {
                        $('.sidebar-item').removeClass('sidebar-highlight');
                        $('.sidebar-item:contains("Log out")').addClass('sidebar-highlight');
                    }
                    highlightLogout();
                </script>
                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-primary" onclick="completeTutorial()">Done</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>



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

                    <li class="sidebar-item active">
                        <a class="sidebar-link" href="index.php">
                            <i class="align-middle" data-feather="calendar"></i> <span
                                class="align-middle">Calendar</span>
                        </a>
                    </li>

                    <li class="sidebar-header">
                        User
                    </li>

                    <li class="sidebar-item ">
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
            <nav class="navbar navbar-expand navbar-light navbar-bg ">
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

                    <h1 class="h3 mb-3">Appointment <strong>Calendar</strong> </h1>



                    <div class="row flex-fill">
                        <div class="col">
                            <div class="card flex-fill">
                                <div class="card-body d-flex py-0">
                                    <div class="align-self-center w-100">
                                        <?php
                                            $dateComponents = getdate();
                                            if (isset($_GET["month"]) && isset($_GET["year"])) {
                                                $month = $_GET["month"];
                                                $year = $_GET["year"];
                                            } else {
                                                $month = date('m');
                                                $year = date('Y');
                                            }
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
                                                class="text-muted fs-6">Sunday</span></li>

                                    </ul>
                                </div>
                                <hr class="my-0" />
                                <div class="card-body">
                                    <h5 class="h6 card-title">Appointments</h5>
                                    <ul class="list-unstyled mb-0">
                                        <p class="fs-6"><span data-feather="flag" class="feather-sm me-2"></span>
                                            <?php
                                                if ($appointment === null || $appointment === false) {
                                                    echo 'No approved appointments';
                                                } else {
                                                    echo $appointment['AppointmentDate'];
                                                }
                                                ?></p>
                                    </ul>
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

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        var date = new Date(Date.now() - 5 * 24 * 60 * 60 * 1000);
        var defaultDate = date.getUTCFullYear() + "-" + (date.getUTCMonth() + 1) + "-" + date.getUTCDate();
        document.getElementById("datetimepicker-dashboard").flatpickr({
            inline: true,
            prevArrow: "<span title=\"Previous month\">&laquo;</span>",
            nextArrow: "<span title=\"Next month\">&raquo;</span>",
            defaultDate: defaultDate
        });
    });
    </script>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@4.2.0/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@4.2.0/main.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@4.2.0/main.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/uuid@8.3.2/dist/umd/uuidv4.min.js'></script>


</body>

</html>