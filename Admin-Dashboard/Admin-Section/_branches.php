<?php
session_start();
if (!isset($_SESSION["userID"])) {
    header("Location: ../Login-Registration/");
    exit();
}
    $conn = require __DIR__ . "/connection.php";
    $query = "SELECT * FROM tbl_admin WHERE Admin_ID = {$_SESSION["userID"]}";
    $result = $conn->query($query);
    $validateUser = $result->fetch_assoc();
    if (!$validateUser || $validateUser['Access_Level'] != 3) {
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"
        crossorigin="anonymous"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
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
    .table-responsive {
        overflow-x: auto;
        width: 100%;
    }
    table {
        width: 100%;
        table-layout: auto;
    }
    .red-icon {
        color: red;
    }
    .table-responsive {
        overflow-x: auto;
        width: 95%;
        margin-right: auto;
        margin-left: auto;
        padding-right: 1rem;
    }
    table {
        width: 95%;
        margin-left: auto;
        margin-right: auto;
        table-layout: auto;
    }
    </style>
    <title>Admin Dashboard</title>
    <link href="../css/app.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
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
                <h1 class="h3 mb-3">Appointment<strong> Analytics</strong></h1>
                <div class="row">
                    <div class="col-xl-6 col-xxl-5 d-flex">
                        <div class="w-100">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="card">
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
                                            <h1 class="mt-1 mb-3 text-primary" id="total-done-appointments">0</h1>
                                            <div class="mb-0">
                                                <span class="text-muted">Overall Total</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
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
                                <div class="col-sm-6">
                                    <div class="card">
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
                                            <h1 class="mt-1 mb-3 text-primary" id="year-done-appointments">21.300</h1>
                                            <div class="mb-0">
                                                <span class="text-muted">Added this year</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
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
                                            <h1 class="mt-1 mb-3 text-primary" id="active-appointment-count">64</h1>
                                            <div class="mb-0">
                                                <span class="text-muted">Had appointments</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-xxl-7">
                        <div class="card">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between mb-3">
                                    <h5 class="card-title mt-1">
                                        <span data-feather="trending-up" class="feather-md me-1"></span> Appointment
                                        Statistics
                                    </h5>
                                    <span><select id="year-selector" class="form-select"></select></span>
                                </div>
                                <div class="chart chart-sm">
                                    <canvas id="chartjs-dashboard-line" width="634" height="252"
                                        class="chartjs-render-monitor"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="card  flex-fill w-100">
                            <div class="card-header">
                                <h5 class="card-title"> <span data-feather="bar-chart-2"
                                        class="feather-md me-1"></span>Appointment Status</h5>
                                <h6 class="card-subtitle text-muted">This bar chart shows this year's number of
                                    <span class="text-success">successful</span> and <span
                                        class="text-danger">unsuccessful </span>appointments
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="chart">
                                    <canvas class="my-3" id="chartjs-bar"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title"><span data-feather="pie-chart"
                                        class="feather-md me-1"></span>Services Chart</h5>
                                <h6 class="card-subtitle text-muted">This chart represents the most selected service
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="chart chart-sm">
                                    <canvas id="chartjs-doughnut"></canvas>
                                </div>
                                <div id="most-booked-service" class="text-center mt-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <h1 class="h3 mb-3">Financial<strong> Analytics</strong></h1>
                <div class="row">
                    <div class="col-12 col-lg-8 col-xxl-9 ">
                        <div class="card flex-fill w-100">
                            <div class="card-header">
                                <h5 class="card-title">Revenue Statistics</h5>
                                <h6 class="card-subtitle text-muted"></h6>
                            </div>
                            <div class="card-body">
                                <div class="chart">
                                    <div class="chartjs-size-monitor">
                                        <div class="chartjs-size-monitor-expand">
                                            <div class=""></div>
                                        </div>
                                        <div class="chartjs-size-monitor-shrink">
                                            <div class=""></div>
                                        </div>
                                    </div>
                                    <canvas id="chartjs-line" width="356" height="300"
                                        style="display: block; width: 356px; height: 300px;"
                                        class="chartjs-render-monitor"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xxl-3 d-flex order-2 order-xxl-3">
                        <div class="card flex-fill w-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Browser Usage</h5>
                            </div>
                            <div class="card-body d-flex">
                                <div class="align-self-center w-100">
                                    <div class="py-3">
                                        <div class="chart chart-xs">
                                            <div class="chartjs-size-monitor">
                                                <div class="chartjs-size-monitor-expand">
                                                    <div class=""></div>
                                                </div>
                                                <div class="chartjs-size-monitor-shrink">
                                                    <div class=""></div>
                                                </div>
                                            </div>
                                            <canvas id="chartjs-dashboard-pie" width="356" height="200"
                                                style="display: block; width: 356px; height: 200px;"
                                                class="chartjs-render-monitor"></canvas>
                                        </div>
                                        <div id="revenue-highlight"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </main>
            <?php include("components/footer.php")?>
        </div>
    </div>
    <script src="../js/app.js"></script>
    <script src=""></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.js"></script>
    <script>
    $(document).ready(function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
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
            url: 'get-yearly-status.php',
            method: 'GET',
            success: function(response) {
                const data = JSON.parse(response);
                let monthlyData = {
                    'Done': Array(12).fill(0),
                    'Cancelled': Array(12).fill(0),
                    'No Response': Array(12).fill(0)
                };
                data.forEach(item => {
                    const monthIndex = item.month -
                        1; 
                    monthlyData[item.Status][monthIndex] = parseInt(item.count);
                });
                new Chart(document.getElementById("chartjs-bar"), {
                    type: "bar",
                    data: {
                        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                            "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                        ],
                        datasets: [{
                            label: "Done",
                            backgroundColor: "#97DBC2", 
                            borderColor: "#97DBC2",
                            data: monthlyData['Done'],
                            barPercentage: .75,
                            categoryPercentage: .25
                        }, {
                            label: "Cancelled",
                            backgroundColor: "#F4D951", 
                            borderColor: "#F4D951",
                            data: monthlyData['Cancelled'],
                            barPercentage: .75,
                            categoryPercentage: .25
                        }, {
                            label: "No Response",
                            backgroundColor: "#FFB0B0", 
                            borderColor: "#FFB0B0",
                            data: monthlyData['No Response'],
                            barPercentage: .75,
                            categoryPercentage: .25
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        scales: {
                            yAxes: [{
                                gridLines: {
                                    display: true
                                },
                                stacked: false,
                                ticks: {
                                    beginAtZero: true,
                                    stepSize: 5
                                }
                            }],
                            xAxes: [{
                                stacked: false,
                                gridLines: {
                                    color: "transparent"
                                }
                            }]
                        },
                        tooltips: {
                            mode: 'index',
                            intersect: false
                        }
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error('Error fetching data:', error);
            }
        });
        $.ajax({
            url: 'fetch-treatment-revenue.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                const currentYear = new Date().getFullYear();
                const lastYear = currentYear - 1;
                const currentYearData = Array(12).fill(0);
                const lastYearData = Array(12).fill(0);
                data.forEach(record => {
                    const date = new Date(record.Treatment_Date);
                    const month = date.getMonth(); 
                    const year = date.getFullYear();
                    const cost = parseFloat(record.Treatment_Cost);
                    if (year === currentYear) {
                        currentYearData[month] += cost;
                    } else if (year === lastYear) {
                        lastYearData[month] += cost;
                    }
                });
                const currentYearDataRounded = currentYearData.map(value => Math.round(value *
                    100) / 100);
                const lastYearDataRounded = lastYearData.map(value => Math.round(value * 100) /
                    100);
                new Chart(document.getElementById("chartjs-line"), {
                    type: "line",
                    data: {
                        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug",
                            "Sep", "Oct", "Nov", "Dec"
                        ],
                        datasets: [{
                            label: `Revenue ${currentYear} (₱)`,
                            fill: true,
                            backgroundColor: "transparent",
                            borderColor: window.theme.primary,
                            data: currentYearDataRounded
                        }, {
                            label: `Revenue ${lastYear} (₱)`,
                            fill: true,
                            backgroundColor: "transparent",
                            borderColor: "#adb5bd",
                            borderDash: [4, 4],
                            data: lastYearDataRounded
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        legend: {
                            display: true 
                        },
                        tooltips: {
                            intersect: false,
                            callbacks: {
                                label: function(tooltipItem, data) {
                                    return data.datasets[tooltipItem.datasetIndex]
                                        .label + ': ₱' +
                                        tooltipItem.yLabel.toLocaleString(undefined, {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        });
                                }
                            }
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
                                    color: "rgba(0,0,0,0.05)"
                                }
                            }],
                            yAxes: [{
                                ticks: {
                                    stepSize: 500,
                                    callback: function(value) {
                                        return '₱' + value.toLocaleString();
                                    }
                                },
                                display: true,
                                borderDash: [5, 5],
                                gridLines: {
                                    color: "rgba(0,0,0,0)",
                                    fontColor: "#fff"
                                }
                            }]
                        }
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error('Error fetching revenue data:', error);
            }
        });
        function generateEarthyColor() {
            const earthyHues = [{
                    min: 0,
                    max: 30
                }, 
                {
                    min: 25,
                    max: 55
                }, 
                {
                    min: 45,
                    max: 85
                }, 
                {
                    min: 80,
                    max: 150
                }, 
                {
                    min: 170,
                    max: 190
                }, 
                {
                    min: 20,
                    max: 50
                } 
            ];
            const hueRange = earthyHues[Math.floor(Math.random() * earthyHues.length)];
            const hue = Math.floor(Math.random() * (hueRange.max - hueRange.min)) + hueRange.min;
            const saturation = Math.floor(Math.random() * (80 - 30)) + 30; 
            const lightness = Math.floor(Math.random() * (65 - 35)) + 35; 
            return `hsl(${hue}, ${saturation}%, ${lightness}%)`;
        }
        function generateDistinctColors(count) {
            const colors = [];
            const minDifference = 30; 
            for (let i = 0; i < count; i++) {
                let newColor;
                let isDistinct = false;
                while (!isDistinct) {
                    newColor = generateEarthyColor();
                    isDistinct = true;
                    const tempDiv = document.createElement('div');
                    tempDiv.style.backgroundColor = newColor;
                    document.body.appendChild(tempDiv);
                    const rgbColor = window.getComputedStyle(tempDiv).backgroundColor;
                    document.body.removeChild(tempDiv);
                    for (const existingColor of colors) {
                        const tempDiv2 = document.createElement('div');
                        tempDiv2.style.backgroundColor = existingColor;
                        document.body.appendChild(tempDiv2);
                        const existingRgbColor = window.getComputedStyle(tempDiv2).backgroundColor;
                        document.body.removeChild(tempDiv2);
                        if (areColorsSimilar(rgbColor, existingRgbColor)) {
                            isDistinct = false;
                            break;
                        }
                    }
                }
                colors.push(newColor);
            }
            return colors;
        }
        function areColorsSimilar(color1, color2) {
            const rgb1 = color1.match(/\d+/g).map(Number);
            const rgb2 = color2.match(/\d+/g).map(Number);
            const difference = Math.sqrt(
                Math.pow(rgb1[0] - rgb2[0], 2) +
                Math.pow(rgb1[1] - rgb2[1], 2) +
                Math.pow(rgb1[2] - rgb2[2], 2)
            );
            return difference < 100; 
        }
        $.ajax({
            url: 'fetch-treatment-totals.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                const totalRevenue = data.reduce((sum, item) => sum + parseFloat(item.total_cost),
                    0);
                const labels = data.map(item => item.Treatment_Name);
                const values = data.map(item => parseFloat(item.total_cost));
                const colors = generateDistinctColors(labels.length);
                const percentages = values.map(value => ((value / totalRevenue) * 100).toFixed(1));
                new Chart(document.getElementById("chartjs-dashboard-pie"), {
                    type: "pie",
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: colors,
                            borderColor: "transparent"
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        legend: {
                            display: true,
                            position: 'right',
                            labels: {
                                boxWidth: 12
                            }
                        },
                        tooltips: {
                            callbacks: {
                                label: function(tooltipItem, data) {
                                    const value = data.datasets[0].data[tooltipItem
                                        .index];
                                    const label = data.labels[tooltipItem.index];
                                    const percentage = percentages[tooltipItem.index];
                                    return `${label}: ₱${value.toLocaleString()} (${percentage}%)`;
                                }
                            }
                        }
                    }
                });
                $('#revenue-highlight').html(`
                <div class="alert alert-info mt-3 text-center" role="alert">
                    <h4 class="alert-heading mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        Total Revenue: ₱${totalRevenue.toLocaleString()}
                    </h4>
                </div>
            `);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching treatment data:', error);
                $('#revenue-highlight').html(`
                <div class="alert alert-danger mt-3" role="alert">
                    Error loading revenue data
                </div>
            `);
            }
        });
        $.ajax({
            url: 'fetch-services-count.php',
            method: 'GET',
            dataType: 'json', 
            success: function(data) { 
                try {
                    console.log('Received data:', data);
                    if (!data || !Array.isArray(data) || data.length === 0) {
                        console.error('Invalid or empty data received');
                        return;
                    }
                    const labels = data.map(item => item.Service);
                    const counts = data.map(item => parseInt(item.count, 10));
                    const maxCount = Math.max(...counts);
                    const maxIndex = counts.indexOf(maxCount);
                    const mostBookedService = labels[maxIndex];
                    $('#most-booked-service').html(`
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-crown me-2"></i>
                    <strong>${mostBookedService}</strong> is leading with ${maxCount} appointment${maxCount > 1 ? 's' : ''}
                </div>
            `);
                    const colors = generateDistinctColors(labels.length);
                    new Chart(document.getElementById("chartjs-doughnut"), {
                        type: "doughnut",
                        data: {
                            labels: labels,
                            datasets: [{
                                data: counts,
                                backgroundColor: colors,
                                borderColor: "transparent"
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            cutoutPercentage: 65,
                            legend: {
                                display: true,
                                position: 'right',
                                labels: {
                                    padding: 20,
                                    boxWidth: 12
                                }
                            },
                            tooltips: {
                                callbacks: {
                                    label: function(tooltipItem, data) {
                                        const dataset = data.datasets[tooltipItem
                                            .datasetIndex];
                                        const total = dataset.data.reduce((acc,
                                            current) => acc + current);
                                        const currentValue = dataset.data[tooltipItem
                                            .index];
                                        const percentage = Math.round((currentValue /
                                            total) * 100);
                                        return `${data.labels[tooltipItem.index]}: ${currentValue} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    });
                } catch (e) {
                    console.error('Error processing data:', e);
                    console.log('Raw data:', data);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching service data:', error);
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
        var defaultYear = new Date().getFullYear();
        fetchDataAndCreateChart(defaultYear);
        document.getElementById('year-selector').addEventListener('change', function() {
            var selectedYear = this.value;
            fetchDataAndCreateChart(selectedYear);
        });
        fetchDataAndRenderChart(currentYear);
    });
    </script>
</body>
</html>