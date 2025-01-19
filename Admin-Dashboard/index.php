<?php
session_start();
if (!isset($_SESSION["userID"])) {
    header("Location: Login-Registration/");
    exit();
}
$conn = require __DIR__ . "/../connection.php"; 
$query = "SELECT * FROM tbl_admin WHERE Admin_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION["userID"]); 
$stmt->execute();
$result = $stmt->get_result();
$validateUser = $result->fetch_assoc();
if (!$validateUser || $validateUser['Access_Level'] != 2) {
    header("Location: Login-Registration/");
    exit();
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
            $redirectUrl = '/try/Admin-Dashboard/index.php?month=' . 
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Dashboard</title>
    <link href="css/app.css" rel="stylesheet">
    <link href="css/calendar-style.css" rel="stylesheet">
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
    .table-scrollable {
        max-height: 300px;
        /* Adjust height as needed */
        overflow-y: auto;
    }
    .table-scrollable table {
        width: 100%;
    }
    .table-scrollable thead th {
        position: sticky;
        top: 0;
        background-color: #f8f9fa;
        z-index: 10;
    }
    /* Scrollbar styling for webkit browsers */
    .table-scrollable::-webkit-scrollbar {
        width: 8px;
    }
    .table-scrollable::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .table-scrollable::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }
    .table-scrollable::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    #chartjs-dashboard-pie {
        max-width: 100%;
        /* Ensure the canvas scales within its parent container */
        max-height: 100%;
        /* Ensure it doesn't exceed the parent's height */
        width: 100%;
        /* Allow responsiveness */
        height: auto;
        /* Maintain aspect ratio */
        display: block;
        /* Prevent inline block gaps */
        min-width: 200px;
        /* Prevent excessive shrinking */
        min-height: 100px;
    }
    .chart-md {
        width: 100%;
        /* Ensure the parent div adapts to its container */
        height: 100%;
        /* Utilize the available space */
        max-height: 33vh;
        /* Limit the height of the chart area */
    }
    </style>
    <script>
    $(document).ready(function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
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
        $.ajax({
            url: 'fetch-client-counts.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#total-clients').text(data.total_clients);
                $('#active-appointment-count').text(data.active_clients);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching client counts:', error);
            }
        });
        $.ajax({
            url: 'fetch-appointment-counts.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#total-done-appointments').text(data.total_done_appointments);
                $('#year-done-appointments').text(data.year_done_appointments);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching appointment counts:', error);
            }
        });
        $.ajax({
            url: 'fetch-services-count.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log('Received data:', data); 
                if (!data || data.length === 0) {
                    $('#service-counts-table').html(
                        '<tr><td colspan="2" class="text-center">No data available</td></tr>');
                    return;
                }
                $('#service-counts-table').empty();
                var serviceNames = [];
                var clientCounts = [];
                var colors = [];
                function getBlueShade(index, total) {
                    const baseHue = 210; 
                    const saturation = 70;
                    const lightness = Math.min(85, 40 + (index / total) * 40);
                    return `hsl(${baseHue}, ${saturation}%, ${lightness}%)`;
                }
                data.forEach(function(service, index) {
                    if (service.ServiceName && service.ClientCount !== undefined) {
                        serviceNames.push(service.ServiceName);
                        clientCounts.push(parseInt(service.ClientCount));
                        colors.push(getBlueShade(index, data.length));
                        var tableRow = `
                        <tr>
                            <td>
                                <span class="me-2" style="display: inline-block; width: 12px; height: 12px; background-color: ${getBlueShade(index, data.length)}; border-radius: 2px;"></span>
                                ${service.ServiceName}
                            </td>
                            <td class="text-end">${service.ClientCount}</td>
                        </tr>
                    `;
                        $('#service-counts-table').append(tableRow);
                    }
                });
                var ctx = document.getElementById('chartjs-dashboard-pie');
                if (!ctx) {
                    console.error('Cannot find pie chart canvas element');
                    return;
                }
                if (window.servicesPieChart) {
                    window.servicesPieChart.destroy();
                }
                window.servicesPieChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: serviceNames,
                        datasets: [{
                            data: clientCounts,
                            backgroundColor: colors,
                            borderColor: 'white',
                            borderWidth: 2,
                            hoverBorderColor: 'white',
                            hoverBorderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a,
                                            b) => a + b, 0);
                                        const percentage = ((value / total) * 100)
                                            .toFixed(1);
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        layout: {
                            padding: {
                                top: 10,
                                bottom: 10,
                                left: 10,
                                right: 10
                            }
                        }
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error('Error fetching pie chart data:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                $('#service-counts-table').html(
                    '<tr><td colspan="2" class="text-center text-danger">Error loading data</td></tr>'
                );
            }
        });
        const currentYear = new Date().getFullYear();
        const yearSelector = document.getElementById('year-selector');
        for (let i = currentYear; i >= currentYear - 10; i--) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = i;
            yearSelector.appendChild(option);
        }
        yearSelector.value = currentYear;
        function fetchDataAndCreateChart(year) {
            $.ajax({
                url: 'fetch-appointments.php',
                method: 'GET',
                data: {
                    year: year
                },
                dataType: 'json',
                success: function(data) {
                    createChart(data);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        }
        var ctx = document.getElementById("chartjs-dashboard-line").getContext("2d");
        var chart;
        function createChart(data) {
            var gradient = ctx.createLinearGradient(0, 0, 0, 225);
            gradient.addColorStop(0, "rgba(215, 227, 244, 1)");
            gradient.addColorStop(1, "rgba(215, 227, 244, 0)");
            if (chart) {
                chart.destroy();
            }
            chart = new Chart(ctx, {
                type: "line",
                data: {
                    labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct",
                        "Nov", "Dec"
                    ],
                    datasets: [{
                        label: "Appointments",
                        fill: true,
                        backgroundColor: gradient,
                        borderColor: window.theme.primary,
                        data: data
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    tooltips: {
                        intersect: false
                    },
                    hover: {
                        intersect: true
                    },
                    plugins: {
                        filler: {
                            propagate: false
                        }
                    },
                    scales: {
                        xAxes: [{
                            reverse: true,
                            gridLines: {
                                color: "rgba(0,0,0,0.0)"
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                stepSize: 1
                            },
                            display: true,
                            borderDash: [3, 3],
                            gridLines: {
                                color: "rgba(0,0,0,0.0)"
                            }
                        }]
                    }
                }
            });
        }
        fetchDataAndCreateChart(currentYear);
        document.getElementById('year-selector').addEventListener('change', function() {
            var selectedYear = this.value;
            fetchDataAndCreateChart(selectedYear);
        });
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
                        Data
                    </li>
                    <li class="sidebar-item active">
                        <a class="sidebar-link" href="index.php">
                            <i class="align-middle" data-feather="sliders"></i> <span
                                class="align-middle">Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-header">
                        Records
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="Transactions.php">
                            <i class="align-middle" data-feather="book"></i> <span
                                class="align-middle">Transactions</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="Micro-transactions/Microtransactions.php">
                            <i class="align-middle" data-feather="credit-card"></i> <span
                                class="align-middle">Microtransactions</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="Client-Records/Clients.php">
                            <i class="align-middle" data-feather="user"></i> <span class="align-middle">Clients</span>
                        </a>
                    </li>
                    <li class="sidebar-header">
                        Maintenance
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="Maintenance/maintenance.php">
                            <i class="align-middle" data-feather="settings"></i> <span class="align-middle">Management
                                Settings</span>
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
            <?php include "components/header.php";?>
            <main class="content">
                <div class="container-fluid p-0">
                    <h1 class="h3 mb-3">Appointment <strong>Statistics</strong> </h1>
                    <div class="row">
                        <div class="row dashboard-row">
                            <div class="col-xl-6 col-xxl-5">
                                <div class="h-100">
                                    <div class="row">
                                        <div class="col-sm-6 d-flex">
                                            <div class="w-100 ">
                                                <div class="card flex-grow-1">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col mt-0">
                                                                <h5 class="card-title">Total Appointments</h5>
                                                            </div>
                                                            <div class="col-auto">
                                                                <div class="stat text-primary">
                                                                    <i class="align-middle" data-feather="check"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <h1 class="mt-1 mb-3 text-primary" id="total-done-appointments">
                                                            0</h1>
                                                        <div class="mb-0">
                                                            <span class="text-muted">Overall Total</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card flex-grow-1">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col mt-0">
                                                                <h5 class="card-title">Clients</h5>
                                                            </div>
                                                            <div class="col-auto">
                                                                <div class="stat text-primary">
                                                                    <i class="align-middle" data-feather="users"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <h1 class="mt-1 mb-3 text-primary" id="total-clients"></h1>
                                                        <div class="mb-0">
                                                            <span class="text-muted">Overall Total</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 d-flex">
                                            <div class="w-100">
                                                <div class="card flex-grow-1">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col mt-0">
                                                                <h5 class="card-title">Added Appointments</h5>
                                                            </div>
                                                            <div class="col-auto">
                                                                <div class="stat text-primary">
                                                                    <i class="align-middle" data-feather="plus"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <h1 class="mt-1 mb-3 text-primary" id="year-done-appointments">
                                                        </h1>
                                                        <div class="mb-0">
                                                            <span class="text-muted">Added this year</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card flex-grow-1">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col mt-0">
                                                                <h5 class="card-title">Active Appointments</h5>
                                                            </div>
                                                            <div class="col-auto">
                                                                <div class="stat text-primary">
                                                                    <i class="align-middle" data-feather="calendar"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <h1 class="mt-1 mb-3 text-primary"
                                                            id="active-appointment-count">64</h1>
                                                        <div class="mb-0">
                                                            <span class="text-muted">Had appointments</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-xxl-7">
                                <div class="card" style="height: 33vh;">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0"><span data-feather="check-circle"
                                        class="feather-md me-2"></span>Daily completed appointments</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row w-100">
                                            <div class="col-md-7">
                                                <div class="chart chart-md">
                                                    <canvas id="chartjs-dashboard-pie"
                                                        class="chartjs-render-monitor"></canvas>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="table-responsive table-scrollable h-100">
                                                    <table class="table">
                                                        <tbody id="service-counts-table">
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row px-4">
                            <div class="card mb-4 ">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-3">
                                        <h5 class="card-title mt-1"><span data-feather="trending-up"
                                                class="feather-md me-1"></span> Appointment Statistics</h5>
                                        <span><select id="year-selector" class="form-select"></select></span>
                                    </div>
                                    <div class="chart chart-sm">
                                        <canvas id="chartjs-dashboard-line" width="634" height="252"
                                            class="chartjs-render-monitor"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h1 class="h3 my-3">Follow up <strong>Calendar</strong> </h1>
                        <div class="row flex-fill">
                            <div class="col-xl-6 col-xxl-9">
                                <div class="card flex-fill w-100">
                                    <div class="card-body d-flex py-0">
                                        <div class="align-self-center w-100">
                                            <?php
                                                include('generate-calendar.php');
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
                            <div class="col-xl-6 col-xxl-3">
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
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@4.2.0/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@4.2.0/main.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@4.2.0/main.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/uuid@8.3.2/dist/umd/uuidv4.min.js'></script>
</body>
</html>