<?php
session_start();
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
if (!$validateUser || $validateUser['Access_Level'] != 1) {
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
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.29.2/dist/feather.min.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"
        crossorigin="anonymous"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
    <script>
    $(document).ready(function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
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
        table = $('#ClientsTable').DataTable({
            responsive: true,
            language: {
                search: "",
                searchPlaceholder: "Search clients...",
                lengthMenu: "Show _MENU_ entries"
            },
        });
        // Reinitialize tooltips and icons
        $('[data-bs-toggle="tooltip"]').tooltip();
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        $('.dt-length label').remove();
        $('.col-md-auto.me-auto').removeClass('col-md-auto me-auto').addClass('col-md-auto');
        $('.dt-length label').remove();
        $('.dt-search label').remove();
        $('.row.mt-2.justify-content-between').removeClass('row mt-2 justify-content-between').addClass(
            'row justify-content-between');
        $('.dt-search').prepend('<span class="me-3" data-feather="search"></span>');
        feather.replace();
        $('#ClientsTable').on('click', '.btn-view', function() {
            var row = $(this).closest('tr');
            // Get the value from the first <td> with class "client-id" in the same row
            var clientId = row.find('.client-id').text().trim();
            // Do something with the client ID
            console.log("Client ID: " + clientId);
            $.ajax({
                url: 'fetch-user-details.php',
                method: 'POST',
                data: {
                    clientId: clientId,
                },
                success: function(response) {
                    var clients = JSON.parse(response);
                    console.log(clients.FirstName);
                    $('#first-name').val(clients.FirstName);
                    $('#last-name').val(clients.LastName);
                    $('#middle-name').val(clients.MiddleName);
                    $('#age').val(clients.Age);
                    $('#occupation').val(clients.Occupation);
                    $('#address').val(clients.Address);
                    $('#number').val(clients.Number);
                    $('#email').val(clients.Email);
                    $('.client-ID').text(clients.Client_ID);
                    var imageUrl = clients.Image ? clients.Image :
                        'img/avatars/avatar-6.jpg';
                    $('#client-img').attr('src', '../../Client-Dashboard/' + imageUrl);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert(
                        'An error occurred while trying to store the data in the database.'
                    );
                }
            });
            $('#view-modal').modal('show');
        });
        $('#view-record').on('click', function() {
            var clientId = $('.client-ID').text();
            // Basic encryption function using Base64 encoding
            // Note: For production, use a more secure encryption method
            function encryptId(id) {
                return btoa(id);
            }
            // Encrypt the Client_ID
            var encryptedId = encryptId(clientId);
            // Redirect to appointment-records with encrypted ID
            window.location.href = 'appointment-records.php?id=' + encodeURIComponent(encryptedId);
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
    <title>Admin Dashboard</title>
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
        <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
        <?php
        include("components/sidebar.php");
        ?>
        <div class="main">
            <?php
            include("components/header.php");
            ?>
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
                                                    <th>Age</th>
                                                    <th>Address</th>
                                                    <th>Occupation</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($clients as $client): ?>
                                                <tr>
                                                    <td class="client-id">
                                                        <?php echo htmlspecialchars($client['Client_ID']); ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($client['FirstName']); ?></td>
                                                    <td><?php echo htmlspecialchars($client['LastName']); ?></td>
                                                    <td><?php echo htmlspecialchars($client['MiddleName']); ?></td>
                                                    <td><?php echo htmlspecialchars($client['Age']); ?></td>
                                                    <td class="text-truncate" style="max-width: 200px;">
                                                        <?php echo htmlspecialchars($client['Address']); ?>
                                                    </td>
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
                                    <label class="form-label">Age:</label>
                                    <input type="number" id="age" class="form-control mb-2" disabled></input>
                                    <label class="form-label">Number:</label>
                                    <input type="text" id="number" class="form-control mb-2" disabled></input>
                                    <label class="form-label">Email:</label>
                                    <input type="email" id="email" class="form-control mb-2" disabled></input>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn " data-dismiss="modal" id="close">Close</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                        id="view-record">View Records</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- toast -->
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
</body>
</html>