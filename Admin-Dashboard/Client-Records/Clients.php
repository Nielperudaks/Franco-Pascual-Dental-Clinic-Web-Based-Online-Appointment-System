<?php
session_start();
if (!isset($_SESSION["userID"])) {
    header("Location: ../Login-Registration/");
    exit();
}
$conn = require __DIR__ . "/../connection.php";
    $query = "SELECT * FROM tbl_admin WHERE Admin_ID = {$_SESSION["userID"]}";
    $result = $conn->query($query);
    $validateUser = $result->fetch_assoc();
    if (!$validateUser || $validateUser['Access_Level'] != 2) {
        header("Location: ../Login-Registration/");
        exit();
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.2/feather.min.js"
        integrity="sha512-zMm7+ZQ8AZr1r3W8Z8lDATkH05QG5Gm2xc6MlsCdBz9l6oE8Y7IXByMgSm/rdRQrhuHt99HAYfMljBOEZ68q5A=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"
        crossorigin="anonymous"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
        .btn-sm {
            padding: 0.2rem 0.4rem;
        }
    }
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
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
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
        $('#ClientsTable').DataTable({
            responsive: true,
            language: {
                search: "",
                searchPlaceholder: "Search clients...",
                lengthMenu: "Show _MENU_ entries"
            },
        });
        $('.dt-length label').remove();
        $('.col-md-auto.me-auto').removeClass('col-md-auto me-auto').addClass('col-md-auto');
        $('.dt-length label').remove();
        $('.dt-search label').remove();
        $('.row.mt-2.justify-content-between').removeClass('row mt-2 justify-content-between').addClass(
            'row justify-content-between');
        $('.dt-search').prepend('<span class="me-3" data-feather="search"></span>');
        feather.replace();
        $("#ClientsTable").on("click", ".btn-view", function() {
            var row = $(this).closest('tr');
            var clientId = row.find('.client-id').text().trim();
            $.ajax({
                url: 'fetch-user-details.php',
                method: 'POST',
                data: {
                    clientId: clientId
                },
                success: function(response) {
                    try {
                        console.log(response);
                        var clients = JSON.parse(response);
                        $('#first-name').val(clients.FirstName);
                        $('#last-name').val(clients.LastName);
                        $('#middle-name').val(clients.MiddleName);
                        // $('#age').val(clients.Age);
                        $('#occupation').val(clients.Occupation);
                        $('#address').val(clients.Address);
                        $('#number').val(clients.Number);
                        $('#email').val(clients.Email);
                        $('.client-ID').text(clients.Client_ID);
                        var imageUrl = clients.Image ? clients.Image :
                            'img/avatars/avatar-6.jpg';
                        $('#client-img').attr('src', '../../Client-Dashboard/' + imageUrl);
                        $('#view-modal').modal('show');
                    } catch (e) {
                        console.error("Error parsing response:", e);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error in AJAX call:", error);
                    alert('An error occurred while fetching client details.');
                }
            });
        });
    });
    </script>
    <title>Clients</title>
    <link href="../css/app.css" rel="stylesheet">
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
                    <img src="../img/photos/Logo.png" class="img-fluid rounded-circle" width="144" height="144" />
                    <a class="sidebar-brand" href="#">
                        <p class="align-middle">Franco - Pascual</p>
                    </a>
                </div>
                <ul class="sidebar-nav">
                    <li class="sidebar-header">
                        Data
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="../index.php">
                            <i class="align-middle" data-feather="sliders"></i> <span
                                class="align-middle">Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-header">
                        Records
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="../Transactions.php">
                            <i class="align-middle" data-feather="book"></i> <span
                                class="align-middle">Transactions</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="../Micro-transactions/Microtransactions.php">
                            <i class="align-middle" data-feather="credit-card"></i> <span
                                class="align-middle">Microtransactions</span>
                        </a>
                    </li>
                    <li class="sidebar-item active">
                        <a class="sidebar-link" href="Clients.php">
                            <i class="align-middle" data-feather="user"></i> <span class="align-middle">Clients</span>
                        </a>
                    </li>
                    <li class="sidebar-header">
                        Maintenance
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="../Maintenance/maintenance.php">
                            <i class="align-middle" data-feather="settings"></i> <span class="align-middle">Management
                                Settings</span>
                        </a>
                    </li>
                    <li class="sidebar-header">
                        Others
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="../logout.php">
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
                        <li class="nav-item dropdown">
                            <p class=" d-none d-sm-inline-block pt-2">
                                (Secretary) <span
                                    class="text-dark"><?= htmlspecialchars($validateUser["Name"]) ?></span>
                            </p>
                        </li>
                    </ul>
                </div>
            </nav>
            <main class="content">
                <div class="container-fluid p-0 ">
                    <h1 class="h3 mb-3">Client <strong>List</strong></h1>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <?php
                                    $conn = require __DIR__ . "../../../connection.php";
                                    $query = "
                                            SELECT Client_ID, Email, FirstName, LastName, MiddleName, Age, Address, Occupation, Number
                                            FROM tbl_clients
                                    ";
                                    $result = $conn->query($query);
                                    $clients = [];
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $clients[] = $row;
                                        }
                                    }
                                    $conn->close();
                                    ?>
                                        <table id="ClientsTable" class="table table-hover w-100 mt-1">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Client ID</th>
                                                    <th>First Name</th>
                                                    <th>Last Name</th>
                                                    <th>Middle Name</th>
                                                    <th>Address</th>
                                                    <th>Occupation</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($clients as $client) : ?>
                                                <tr>
                                                    <td class="client-id">
                                                        <?php echo htmlspecialchars($client['Client_ID']); ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($client['FirstName']); ?></td>
                                                    <td><?php echo htmlspecialchars($client['LastName']); ?></td>
                                                    <td><?php echo htmlspecialchars($client['MiddleName']); ?></td>
                                                    <td class="text-truncate" style="max-width: 200px;">
                                                        <?php echo htmlspecialchars($client['Address']); ?></td>
                                                    <td><?php echo htmlspecialchars($client['Occupation']); ?></td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            <button class="btn btn-sm btn-view me-1"
                                                                style="background-color: #F5F7FB"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="View More">
                                                                <i data-feather="search"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade custom-modal" id="view-modal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Client Details</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="text-center">
                                        <img id="client-img" src="" alt="Christina Mason"
                                            class="img-fluid rounded-circle mb-2" width="128" height="128" />
                                        <div class="text-muted mb-2">Client ID: <span class="client-ID"></span>
                                        </div>
                                    </div>
                                    <label class="form-label">First Name:</label>
                                    <input type="text" id="first-name" class="form-control mb-2" disabled></input>
                                    <label class="form-label">Last Name:</label>
                                    <input type="text" id="last-name" class="form-control mb-2" disabled></input>
                                    <label class="form-label">Middle Name:</label>
                                    <input type="text" id="middle-name" class="form-control mb-2" disabled></input>
                                    <label class="form-label">Address:</label>
                                    <input type="text" id="address" class="form-control mb-2" disabled></input>
                                    <label class="form-label">Occupation:</label>
                                    <input type="text" id="occupation" class="form-control mb-2" disabled></input>
                                    <label class="form-label">Number:</label>
                                    <input type="text" id="number" class="form-control mb-2" disabled></input>
                                    <label class="form-label">Email:</label>
                                    <input type="email" id="email" class="form-control mb-2" disabled></input>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn " data-bs-dismiss="modal"
                                        id="delete-cancel">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
                        <div id="toastContainer"></div>
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
    <script>
    $(document).ready(function() {
        new DataTable('#ClientsTable');
        $(function() {
            $('#start-time').datetimepicker({
                format: 'HH:mm'
            });
            $('#end-time').datetimepicker({
                format: 'HH:mm'
            });
            $('#duration-time').datetimepicker({
                format: 'mm'
            });
            $('#deactivation-start').datetimepicker({
                format: 'YYYY-MM-DD'
            });
            $('#duration').datetimepicker({
                format: 'HH:mm',
                stepping: 30,
                minDate: moment().startOf('day').add(30, 'minutes'),
                maxDate: moment().startOf('day').add(2, 'hours'),
                useCurrent: false,
            });
        });
    });
    </script>
</body>
</html><?php
session_start();
if (!isset($_SESSION["userID"])) {
    header("Location: ../Login-Registration/");
    exit();
}
$conn = require __DIR__ . "/../connection.php";
    $query = "SELECT * FROM tbl_admin WHERE Admin_ID = {$_SESSION["userID"]}";
    $result = $conn->query($query);
    $validateUser = $result->fetch_assoc();
    if (!$validateUser || $validateUser['Access_Level'] != 2) {
        header("Location: ../Login-Registration/");
        exit();
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.2/feather.min.js"
        integrity="sha512-zMm7+ZQ8AZr1r3W8Z8lDATkH05QG5Gm2xc6MlsCdBz9l6oE8Y7IXByMgSm/rdRQrhuHt99HAYfMljBOEZ68q5A=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"
        crossorigin="anonymous"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
        .btn-sm {
            padding: 0.2rem 0.4rem;
        }
    }
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
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
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
        $('#ClientsTable').DataTable({
            responsive: true,
            language: {
                search: "",
                searchPlaceholder: "Search clients...",
                lengthMenu: "Show _MENU_ entries"
            },
        });
        $('.dt-length label').remove();
        $('.col-md-auto.me-auto').removeClass('col-md-auto me-auto').addClass('col-md-auto');
        $('.dt-length label').remove();
        $('.dt-search label').remove();
        $('.row.mt-2.justify-content-between').removeClass('row mt-2 justify-content-between').addClass(
            'row justify-content-between');
        $('.dt-search').prepend('<span class="me-3" data-feather="search"></span>');
        feather.replace();
        $("#ClientsTable").on("click", ".btn-view", function() {
            var row = $(this).closest('tr');
            var clientId = row.find('.client-id').text().trim();
            $.ajax({
                url: 'fetch-user-details.php',
                method: 'POST',
                data: {
                    clientId: clientId
                },
                success: function(response) {
                    try {
                        console.log(response);
                        var clients = JSON.parse(response);
                        $('#first-name').val(clients.FirstName);
                        $('#last-name').val(clients.LastName);
                        $('#middle-name').val(clients.MiddleName);
                        // $('#age').val(clients.Age);
                        $('#occupation').val(clients.Occupation);
                        $('#address').val(clients.Address);
                        $('#number').val(clients.Number);
                        $('#email').val(clients.Email);
                        $('.client-ID').text(clients.Client_ID);
                        var imageUrl = clients.Image ? clients.Image :
                            'img/avatars/avatar-6.jpg';
                        $('#client-img').attr('src', '../../Client-Dashboard/' + imageUrl);
                        $('#view-modal').modal('show');
                    } catch (e) {
                        console.error("Error parsing response:", e);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error in AJAX call:", error);
                    alert('An error occurred while fetching client details.');
                }
            });
        });
    });
    </script>
    <title>Clients</title>
    <link href="../css/app.css" rel="stylesheet">
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
                    <img src="../img/photos/Logo.png" class="img-fluid rounded-circle" width="144" height="144" />
                    <a class="sidebar-brand" href="#">
                        <p class="align-middle">Franco - Pascual</p>
                    </a>
                </div>
                <ul class="sidebar-nav">
                    <li class="sidebar-header">
                        Data
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="../index.php">
                            <i class="align-middle" data-feather="sliders"></i> <span
                                class="align-middle">Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-header">
                        Records
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="../Transactions.php">
                            <i class="align-middle" data-feather="book"></i> <span
                                class="align-middle">Transactions</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="../Micro-transactions/Microtransactions.php">
                            <i class="align-middle" data-feather="credit-card"></i> <span
                                class="align-middle">Microtransactions</span>
                        </a>
                    </li>
                    <li class="sidebar-item active">
                        <a class="sidebar-link" href="Clients.php">
                            <i class="align-middle" data-feather="user"></i> <span class="align-middle">Clients</span>
                        </a>
                    </li>
                    <li class="sidebar-header">
                        Maintenance
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="../Maintenance/maintenance.php">
                            <i class="align-middle" data-feather="settings"></i> <span class="align-middle">Management
                                Settings</span>
                        </a>
                    </li>
                    <li class="sidebar-header">
                        Others
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="../logout.php">
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
                        <li class="nav-item dropdown">
                            <p class=" d-none d-sm-inline-block pt-2">
                                (Secretary) <span
                                    class="text-dark"><?= htmlspecialchars($validateUser["Name"]) ?></span>
                            </p>
                        </li>
                    </ul>
                </div>
            </nav>
            <main class="content">
                <div class="container-fluid p-0 ">
                    <h1 class="h3 mb-3">Client <strong>List</strong></h1>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <?php
                                    $conn = require __DIR__ . "../../../connection.php";
                                    $query = "
                                            SELECT Client_ID, Email, FirstName, LastName, MiddleName, Age, Address, Occupation, Number
                                            FROM tbl_clients
                                    ";
                                    $result = $conn->query($query);
                                    $clients = [];
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $clients[] = $row;
                                        }
                                    }
                                    $conn->close();
                                    ?>
                                        <table id="ClientsTable" class="table table-hover w-100 mt-1">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Client ID</th>
                                                    <th>First Name</th>
                                                    <th>Last Name</th>
                                                    <th>Middle Name</th>
                                                    <th>Address</th>
                                                    <th>Occupation</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($clients as $client) : ?>
                                                <tr>
                                                    <td class="client-id">
                                                        <?php echo htmlspecialchars($client['Client_ID']); ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($client['FirstName']); ?></td>
                                                    <td><?php echo htmlspecialchars($client['LastName']); ?></td>
                                                    <td><?php echo htmlspecialchars($client['MiddleName']); ?></td>
                                                    <td class="text-truncate" style="max-width: 200px;">
                                                        <?php echo htmlspecialchars($client['Address']); ?></td>
                                                    <td><?php echo htmlspecialchars($client['Occupation']); ?></td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            <button class="btn btn-sm btn-view me-1"
                                                                style="background-color: #F5F7FB"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="View More">
                                                                <i data-feather="search"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade custom-modal" id="view-modal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Client Details</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="text-center">
                                        <img id="client-img" src="" alt="Christina Mason"
                                            class="img-fluid rounded-circle mb-2" width="128" height="128" />
                                        <div class="text-muted mb-2">Client ID: <span class="client-ID"></span>
                                        </div>
                                    </div>
                                    <label class="form-label">First Name:</label>
                                    <input type="text" id="first-name" class="form-control mb-2" disabled></input>
                                    <label class="form-label">Last Name:</label>
                                    <input type="text" id="last-name" class="form-control mb-2" disabled></input>
                                    <label class="form-label">Middle Name:</label>
                                    <input type="text" id="middle-name" class="form-control mb-2" disabled></input>
                                    <label class="form-label">Address:</label>
                                    <input type="text" id="address" class="form-control mb-2" disabled></input>
                                    <label class="form-label">Occupation:</label>
                                    <input type="text" id="occupation" class="form-control mb-2" disabled></input>
                                    <label class="form-label">Number:</label>
                                    <input type="text" id="number" class="form-control mb-2" disabled></input>
                                    <label class="form-label">Email:</label>
                                    <input type="email" id="email" class="form-control mb-2" disabled></input>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn " data-bs-dismiss="modal"
                                        id="delete-cancel">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
                        <div id="toastContainer"></div>
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
    <script>
    $(document).ready(function() {
        new DataTable('#ClientsTable');
        $(function() {
            $('#start-time').datetimepicker({
                format: 'HH:mm'
            });
            $('#end-time').datetimepicker({
                format: 'HH:mm'
            });
            $('#duration-time').datetimepicker({
                format: 'mm'
            });
            $('#deactivation-start').datetimepicker({
                format: 'YYYY-MM-DD'
            });
            $('#duration').datetimepicker({
                format: 'HH:mm',
                stepping: 30,
                minDate: moment().startOf('day').add(30, 'minutes'),
                maxDate: moment().startOf('day').add(2, 'hours'),
                useCurrent: false,
            });
        });
    });
    </script>
</body>
</html>