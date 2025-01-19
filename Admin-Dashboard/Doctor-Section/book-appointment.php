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
include("../get-date.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="canonical" href="https://demo-basic.adminkit.io/" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4@4/bootstrap-4.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <title>Appointment</title>
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
    <!-- styleshit -->
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
            min-height: 250px;
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
            min-height: 200px;
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
        $('#timeslot').hide();
        $('#3d-teeth').hide();
        $('.legends-container').hide();
        var currentHour = new Date().getHours();
        var date = "<?php echo $decryptedDate ?> ";
        var duration;
        $('#selectedDate').data('originalValue', date);
        $.ajax({
            url: '../fetch-schedule.php',
            method: 'GET',
            success: function(response) {
                try {
                    var data = JSON.parse(response);
                    var schedule = data.schedules
                    var $tbody = $('.schedule-body');
                    $tbody.empty();
                    var title = '<h5 class="card-title mb-2">Schedule</h5>';
                    $tbody.append(title);
                    schedule.forEach(function(schedules) {
                        var row = `                     
                                <p class='badge fs-5 bg-secondary' ">
                                    ${schedules.Working_Hours}
                                </p>                           
                    `;
                        $tbody.append(row);
                    });
                    feather.replace();
                    // Re-initialize tooltips
                    $('[data-bs-toggle="tooltip"]').tooltip();
                    //$('#services-modal').modal('show'); // Show the modal
                } catch (error) {
                    console.error('Invalid JSON response', error);
                    console.log(response); // Log the raw response for debugging
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('An error occurred while trying to fetch schedules.');
            }
        });
        //Fetching the services from db to #serviceSelect selector
        $.ajax({
            url: '../fetch-services.php',
            method: 'GET',
            success: function(response) {
                var services = JSON.parse(response);
                services.forEach(function(service) {
                    $('#serviceSelect').append('<option id="' + service.Service_ID + '">' +
                        service.ServiceName + '</option>');
                });
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('An error occurred while trying to fetch services.');
            }
        });
        $('#serviceSelect').change(function() {
            var selectedService = $(this).children("option:selected").attr("id");
            //console.log(selectedService);
            //service-select
            // Clear previous options
            $('#doctor-selector').empty().append('<option selected>Select</option>').prop('disabled',
                true);
            if (selectedService !== 'select') {
                // Make AJAX request to fetch doctors for selected service
                $.ajax({
                    url: '../fetch-doctors.php',
                    method: 'POST',
                    data: {
                        serviceID: selectedService
                    },
                    success: function(response) {
                        // Parse the response as an array of Doctor_IDs
                        var doctors = JSON.parse(response);
                        // Populate the doctor selector with fetched doctors
                        doctors.forEach(function(doctor) {
                            //console.log(doctor.Doctor_ID);
                            $('#doctor-selector').append($('<option>', {
                                text: doctor.Doctor_Name,
                                id: doctor.Doctor_ID
                            }));
                        });
                        // Enable the doctor selector
                        $('#doctor-selector').prop('disabled', false);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        alert('An error occurred while trying to fetch doctors.');
                    }
                });
                $.ajax({
                    url: '../fetch-service-duration.php',
                    method: 'POST',
                    data: {
                        service_id: selectedService
                    },
                    success: function(response) {
                        var result = JSON.parse(response);
                        if (result.duration) {
                            duration = parseInt(result.duration);
                            // Generate time slots based on fetched duration
                        }
                    }
                });
            }
        });
        // Generate time slots
        function generateTimeSlots(duration, doctor) {
            // Time slots from 7:00 to 17:00
            var startHour = 7;
            var startMinute = 0;
            var endHour = 17;
            $('#timeslots').empty(); // Clear previous time slots
            while (startHour < endHour) {
                // Calculate the start time
                var slotStart = (startHour < 10 ? '0' : '') + startHour + ":" + (startMinute < 10 ? '0' : '') +
                    startMinute;
                // Calculate the end time
                var endTime = new Date(0, 0, 0, startHour, startMinute + duration); // Add duration in minutes
                var slotEndHour = endTime.getHours();
                var slotEndMinute = endTime.getMinutes();
                // If the slot end time exceeds the endHour (17:00), break out of the loop
                if (slotEndHour > endHour || (slotEndHour == endHour && slotEndMinute > 0)) {
                    break;
                }
                var slotEnd = (slotEndHour < 10 ? '0' : '') + slotEndHour + ":" + (slotEndMinute < 10 ? '0' :
                    '') + slotEndMinute;
                // Create button for the time slot
                var buttonId = 'timeSlot' + slotStart.replace(':', ''); // Button ID using slotStart
                var timeSlotBtn =
                    `<button id="${buttonId}"  value="${slotStart} - ${slotEnd}" class="btn btn-success fs-3 m-3 open-modal-btn">${slotStart} - ${slotEnd}</button>`;
                $('#timeslots').append(timeSlotBtn);
                // Update start time for the next slot
                startHour = slotEndHour;
                startMinute = slotEndMinute;
            }
            // Check for conflicts with existing appointments
            checkAppointments(doctor, duration);
        }
        // Check appointments and disable conflicting slots
        function checkAppointments(doctor, duration) {
            var serviceID = $('#serviceSelect').children("option:selected").attr("id");
            $.ajax({
                url: '../check-appointments.php',
                method: 'POST',
                data: {
                    doctor: doctor,
                    date: date,
                    serviceID: serviceID
                },
                success: function(response) {
                    var appointments = JSON.parse(response);
                    // Loop through existing appointments and disable conflicting slots
                    appointments.forEach(function(appointment) {
                        var startTime = appointment.time; // This is in "HH:MM" format
                        var appointmentDuration = appointment
                            .duration; // Duration in minutes
                        // Split start time into hours and minutes
                        var startHour = parseInt(startTime.split(':')[0]);
                        var startMinutes = parseInt(startTime.split(':')[1]);
                        // Calculate the total minutes of the appointment's start time
                        var totalMinutesStart = startHour * 60 + startMinutes;
                        // Calculate the end time based on the duration
                        var totalMinutesEnd = totalMinutesStart + appointmentDuration;
                        // Loop through and disable buttons for the time range from start to end
                        for (var currentMinutes = totalMinutesStart; currentMinutes <
                            totalMinutesEnd; currentMinutes += 30) {
                            var currentHour = Math.floor(currentMinutes / 60);
                            var currentMin = currentMinutes % 60;
                            // Format the button ID with hours and minutes, e.g., "timeSlot0830"
                            var formattedHour = currentHour.toString().padStart(2, '0');
                            var formattedMinutes = currentMin.toString().padStart(2, '0');
                            var buttonId = '#timeSlot' + formattedHour + formattedMinutes;
                            // Disable the button if it exists
                            var buttonElement = $(buttonId);
                            if (buttonElement.length) {
                                buttonElement.removeClass('btn-success').addClass(
                                    'btn-primary').prop('disabled', true);
                            }
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert('An error occurred while trying to check appointments.');
                }
            });
        }
        $('#doctor-selector').change(function() {
            var selectedDoctor = $(this).val();
            $('.open-modal-btn').removeClass('btn-primary').addClass('btn-success').prop('disabled',
                false);
            generateTimeSlots(duration, selectedDoctor);
        });
        $.ajax({
            url: '../fetch-clients.php',
            method: 'GET',
            success: function(response) {
                // Parse the JSON response
                var clients = JSON.parse(response);
                // Loop through each client and append to the select element
                clients.forEach(function(client) {
                    $('#client-selector').append(
                        $('<option>', {
                            id: client.Client_ID,
                            text: client.FirstName + ' ' + client.LastName
                        })
                    );
                });
            },
            error: function(xhr, status, error) {
                console.error('Error fetching clients:', error);
                alert('An error occurred while trying to fetch clients.');
            }
        });
        // Initialize the DataTable
        // Fetch the clients data via AJAX
        $('#serviceSelect').change(function() {
            if ($(this).val() === 'Select') {
                $('#doctor-selector').prop('disabled', true).val('Select');
                $('#timeslot').fadeOut(100);
                $('#3d-teeth').fadeOut(100);
                $('.legends-container').fadeOut(100);
            } else {
                $('#timeslot').fadeOut(100);
                $('.legends-container').fadeOut(100);
                $('#3d-teeth').fadeOut(100);
                $('#doctor-selector').prop('disabled', false);
            }
        });
        $('#doctor-selector').change(function() {
            if ($(this).val() !== 'Select') {
                $('#timeslot').fadeOut(100).fadeIn(600);
                $('#3d-teeth').fadeOut(100).fadeIn(600);
                $('.legends-container').fadeOut(100).fadeIn(1000);
                // Smooth scroll down to the elements
                $('html, body').animate({
                    scrollTop: $('#timeslot').offset().top - 30 // 50px offset from the top
                }, 100); // 800ms animation duration
            } else {
                $('#timeslot').fadeOut(100);
                $('#3d-teeth').fadeOut(100);
                $('.legends-container').fadeOut(100);
                // Scroll back to top
                $('html, body').animate({
                    scrollTop: 0
                }, 800);
            }
        });
        var today = new Date().toISOString().slice(0, 10);
        if (date === today) {
            var currentHour = new Date().getHours();
            $('button').each(function() {
                var appointmentHour = parseInt($(this).text().split(':')[0]);
                if (appointmentHour < currentHour) {
                    $(this).removeClass('btn-success').addClass('btn-danger').prop('disabled', true);
                    //$(this).prop('disabled', true).removeClass('btn-success').addClass('btn-danger');
                }
            });
        }
        var AppointmentTime;
        $('#timeslots').on('click', '.open-modal-btn', function() {
            var selectedService = $('#serviceSelect').val();
            var selectedDoctor = $('#doctor-selector').val();
            AppointmentTime = $(this).val();
            const targetTooth = selectedToothNumbers.join(', ');
            //console.log(selectedToothNumbers);
            $('#selectedDate').text(date);
            $('#selectedService').text(selectedService);
            $('#appointmentTime').text($(this).val());
            $('#selectedDoctor').text(selectedDoctor);
            $('#targetTooth').text(targetTooth);
            $('#myModal').modal('show');
        });
        $('#cancel').click(function() {
            $('#myModal').modal('hide');
        });
        $('.followup-btn').click(function() {
            $('#myModal').modal('hide');
            var createCode = generateTransactionCode();
            var selectedService = $('#serviceSelect').val();
            var appointmentTime = AppointmentTime;
            var targetTooth = selectedToothNumbers.join(', ');
            var selectedDoctor = $('#doctor-selector').val();
            var doctorID = $('#doctor-selector').children("option:selected").attr("id");
            var serviceID = $('#serviceSelect').children("option:selected").attr("id");
            var clientID = $(this).attr('id');
            var priority = 'No'
            var originalDate = $('#selectedDate').data('originalValue');
            var rdate = $('#selectedDate').text();
            if (originalDate !== rdate) {
                alert("Warning: Selected date has been modified!");
                location.reload();
            }
            Swal.fire({
                title: 'Processing...',
                html: 'Please wait while we update your appointment',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            $.ajax({
                url: '../process-appointment.php',
                method: 'POST',
                data: {
                    doctor: selectedDoctor,
                    service: selectedService,
                    time: appointmentTime,
                    date: date,
                    code: createCode,
                    doctorID: doctorID,
                    serviceID: serviceID,
                    clientID: clientID,
                    targetTooth: targetTooth,
                    priority: priority
                },
                success: function(response) {
                    Swal.fire(
                        'New appointment created!',
                        response,
                        'success'
                    ).then(() => {
                        window.location.href = "Transactions.php";
                    });
                    showToast(response);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert(
                        'An error occurred while trying to store the data in the database.'
                    );
                }
            });
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
        // Function to generate a transaction code
        function generateTransactionCode() {
            var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            var code = '';
            for (var i = 0; i < 6; i++) {
                code += characters.charAt(Math.floor(Math.random() * characters.length));
            }
            return code;
        }
        $('.dt-length label').remove();
        $('.col-md-auto.me-auto').removeClass('col-md-auto me-auto').addClass('col-md-auto');
        $('.dt-info').remove();
        $('.dt-length label').remove();
        $('.dt-search label').remove();
        $('.row.mt-2.justify-content-between').removeClass('row mt-2 justify-content-between').addClass(
            'row justify-content-between');
        $('.dt-search').prepend('<span class="me-3" data-feather="search"></span>');
        feather.replace();
    });
    </script>
</head>
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
<body>
    <div id="loading-screen">
        <div class="spinner"></div>
        <div class="progress-container">
            <div class="progress-bar"></div>
        </div>
        <div class="loading-text">Loading... <span class="progress-text">0%</span></div>
    </div>
    <div class="wrapper">
        <!-- Modal -->
        <!-- toast -->
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
            <div id="toastContainer"></div>
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
                    <h1 class="h3 mb-3"><a href="index.php"> Home</a> / <strong>Appointment</strong></h1>
                    <div class="row flex-fill">
                        <div class="col">
                            <div class="card flex-fill">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Appointment Date: <span class="fw-lighter"
                                            id="date"><?php echo $decryptedDate ?></span></h5>
                                </div>
                                <div class="card-body d-flex">
                                    <div class="align-self-center w-100">
                                        <form action="" method="post">
                                            <label class="form-label">Dental Service</label>
                                            <select class="form-select mb-3 service-select" id="serviceSelect">
                                                <option selected>Select</option>
                                            </select>
                                            <label class="form-label">Doctor</label>
                                            <select class="form-select mb-3" id="doctor-selector" disabled>
                                                <option selected>Select</option>
                                            </select>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- 3D Teeth Model Container -->
                            <div class="card flex-fill card-3d" id="3d-teeth">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Permanent Teeth 3d Model <span
                                            class="text-muted">(Optional)</span> </h5>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <div class="align-self-center" id="teeth-container">
                                        <div id="tooltip" style="display: none; position: absolute;"></div>
                                        <!-- The 3D model will be rendered here -->
                                        <!-- <div id="camera-controls">
                                            <button id="left-view">Left View</button>
                                            <button id="right-view">Right View</button>
                                            <button id="original-view">Original View</button>
                                        </div> -->
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
                            <div class="card flex-fill mt-3" id="timeslot">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Time Slots</h5>
                                </div>
                                <div class="card-body d-flex">
                                    <div class="align-self-center w-100">
                                        <div class="mb-3" id="timeslots">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-xl-3">
                            <div class="card">
                                <div class="card-body schedule-body pe-5">
                                </div>
                            </div>
                            <div class="card legends-container">
                                <div class="card-body">
                                    <h5 class="card-title legends-container">Legends</h5>
                                    <p class="badge bg-success me-1 my-1 fs-4">Available Slot</p>
                                    <p class="badge bg-primary me-1 my-1 fs-4">Unavailable Slot</p>
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
                                <h5 class="modal-title" id="exampleModalLabel">Appointment Confirmation</h5>
                            </div>
                            <div class="modal-body">
                                <div class="d-flex flex-column">
                                    <p class="badge bg-white text-muted me-1 my-1 fs-6 p-3">Appointment Date: <span
                                            id="selectedDate"></span></p>
                                    <p class="badge bg-white text-muted me-1 my-1 fs-6 p-3">Appointment Time: <span
                                            id="appointmentTime"></span></p>
                                    <p class="badge bg-white text-muted me-1 my-1 fs-6 p-3">Selected Service: <span
                                            id="selectedService"></span></p>
                                    <p class="badge bg-white text-muted me-1 my-1 fs-6 p-3">Selected Doctor: <span
                                            id="selectedDoctor"></span></p>
                                    <p class="badge bg-white text-muted me-1 my-1 fs-6 text-wrap p-3">Target tooth:
                                        <span id="targetTooth"></span></p>
                                </div>
                                <label class="form-label mt-3">Select Client</label>
                                <?php include('../generate-client-table.php') ?>
                            </div>
                            <div class="modal-footer">
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        </main>
    </div>
    </div>
    <script src="../js/app.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@4.2.0/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@4.2.0/main.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@4.2.0/main.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/uuid@8.3.2/dist/umd/uuidv4.min.js'></script>
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
        controls.maxDistance = 10;
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
    document.getElementById('timeslots').addEventListener('click', function() {
        //console.log("Selected teeth numbers:", selectedToothNumbers);
        // You can save selectedToothNumbers to a database, local storage, or display it in the UI
        // Example: localStorage.setItem('selectedTeeth', JSON.stringify(selectedToothNumbers));
    });
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
</body>
</html>