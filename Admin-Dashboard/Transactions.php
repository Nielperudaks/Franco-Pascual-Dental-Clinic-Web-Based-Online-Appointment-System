<?php
session_start();
if (!isset($_SESSION["userID"])) {
    header("Location: Login-Registration/");
    exit();
}
$conn = require __DIR__ . "/../connection.php";
    $query = "SELECT * FROM tbl_admin WHERE Admin_ID = {$_SESSION["userID"]}";
    $result = $conn->query($query);
    $validateUser = $result->fetch_assoc();
    if (!$validateUser || $validateUser['Access_Level'] != 2) {
        header("Location: Login-Registration/");
        exit();
    }
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	date_default_timezone_set('Asia/Manila');
	$userId = $_SESSION["userID"];
	$query = "SELECT AppointmentTime, AppointmentDate, Transaction_Code, Status FROM tbl_transaction";
	$stmt = $conn->prepare($query);
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
	$query = "SELECT COUNT(*) AS count FROM tbl_transaction WHERE Status IN ('Approved', 'Waiting Approval')";
	$stmt = $conn->prepare($query);
	if ($stmt->execute()) {
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$count = $row['count'];
		if ($count == 0) {
			$updateClientQuery = "UPDATE tbl_clients SET Status = NULL ";
			$updateClientStmt = $conn->prepare($updateClientQuery);
			$updateClientStmt->execute();
			$updateClientStmt->close();
		}
	}
	$stmt->close();
	$conn->close();
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
    /* ==========================================CSS FOR TABLE================================ */
    /* Custom CSS for better responsiveness */
    @media (max-width: 768px) {
        /* Stack filter selects on mobile */
        .filter-section select {
            width: 100%;
            max-width: none;
        }
        /* Make table scroll horizontally on mobile */
        .table-responsive {
            margin: 0;
            padding: 0;
        }
        /* Adjust button spacing in action column */
        .btn-xs {
            padding: 0.25rem;
            margin: 0.125rem;
        }
        /* Make status badges wrap properly */
        .badge {
            white-space: normal;
            text-align: center;
        }
    }
    /* General table improvements */
    .table th {
        white-space: nowrap;
        background-color: #f8f9fa;
    }
    .table td {
        vertical-align: middle;
    }
    /* Status badge colors */
    .badge[data-status="Waiting Approval"] {
        background-color: #F4D951;
    }
    .badge[data-status="Approved"] {
        background-color: #9AA3D2;
    }
    .badge[data-status="Done"] {
        background-color: #97DBC2;
    }
    .badge[data-status="Cancelled"] {
        background-color: #FFB0B0;
    }
    .badge[data-status="Rejected"] {
        background-color:
            #FFB0B0;
    }
    .badge[data-status="No Response"] {
        background-color:
            #FFB0B0;
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
        var duration;
        var currentHour = new Date().getHours();
        var transactionCode
        var date;
        tinymce.init({
            selector: "#report",
            menubar: false,
            toolbar: "bold italic",
            statusbar: false,
            height: 160
        });
        let progress = 0;
        const progressInterval = setInterval(function() {
            progress += Math.random() * 400;
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
        $('#timeslot').fadeOut(0);
        $("#cancel").click(function() {
            $("#myModal").modal('hide');
        });
        $("#reject-cancel").click(function() {
            $("#reject-modal").modal('hide');
        });
        $("#cancel2").click(function() {
            $("#myModal2").modal('hide');
        });
        $("#cancel3").click(function() {
            $("#myModal3").modal('hide');
        });
        $("#cancel4").click(function() {
            $("#report-modal").modal('hide');
        });
        $('#edit-service-selector').change(function() {
            if ($(this).val() === 'Select') {
                $('#edit-doctor-selector').prop('disabled', true).val('Select');
                $('#timeslot').fadeOut(100);
            } else {
                $('#timeslot').fadeOut(100);
                $('#edit-doctor-selector').prop('disabled', false);
            }
        });
        $('#edit-doctor-selector').change(function() {
            if ($(this).val() !== 'Select') {
                $('#timeslot').fadeOut(100);
                $('#timeslot').fadeIn(100);
            } else {
                $('#timeslot').fadeOut(100);
            }
        });
        $('#edit-doctor-selector').change(function() {
            var selectedDoctor = $(this).val();
            console.log(selectedDoctor);
            $('.open-modal-btn').removeClass('btn-primary').addClass('btn-success').prop('disabled',
                false);
            generateTimeSlots(duration, selectedDoctor);
        });
        function generateTimeSlots(duration, doctor) {
            var startHour = 7;
            var startMinute = 0;
            var endHour = 17;
            $('#timeslots').empty();
            while (startHour < endHour) {
                var slotStart = (startHour < 10 ? '0' : '') + startHour + ":" + (startMinute < 10 ? '0' : '') +
                    startMinute;
                var endTime = new Date(0, 0, 0, startHour, startMinute + duration);
                var slotEndHour = endTime.getHours();
                var slotEndMinute = endTime.getMinutes();
                if (slotEndHour > endHour || (slotEndHour == endHour && slotEndMinute > 0)) {
                    break;
                }
                var slotEnd = (slotEndHour < 10 ? '0' : '') + slotEndHour + ":" + (slotEndMinute < 10 ? '0' :
                    '') + slotEndMinute;
                var buttonId = 'timeSlot' + slotStart.replace(':', '');
                var timeSlotBtn =
                    `<button id="${buttonId}"  value="${slotStart} - ${slotEnd}" class="btn btn-success fs-3 m-3 open-modal-btn">${slotStart} - ${slotEnd}</button>`;
                $('#timeslots').append(timeSlotBtn);
                startHour = slotEndHour;
                startMinute = slotEndMinute;
            }
            checkAppointments(doctor, duration);
        }
        $('#timeslots').on('click', '.open-modal-btn', function() {
            var selectedService = $('#edit-service-selector').val();
            var appointmentTime = $(this).val();
            var selectedDoctor = $('#edit-doctor-selector').val();
            var doctorID = $('#edit-doctor-selector').children("option:selected").attr("id");
            var serviceID = $('#edit-service-selector').children("option:selected").attr("id");
            Swal.fire({
                title: 'Confirmation',
                text: "Are you want to edit this appointment?",
                icon: 'question',
                confirmButtonText: 'Yes',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
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
                        url: 'process-appointment-edit.php',
                        method: 'POST',
                        data: {
                            doctor: selectedDoctor,
                            service: selectedService,
                            time: appointmentTime,
                            code: transactionCode,
                            doctorID: doctorID,
                            serviceID: serviceID,
                        },
                        success: function(response) {
                            Swal.fire(
                                'Appointment Edited!',
                                response,
                                'success'
                            ).then(() => {
                                window.location.href = "Transactions.php";
                                showToast(response);
                            });
                        },
                        error: function(xhr, status, error) {
                            Swal.fire(
                                'Error!',
                                'An error occurred while trying to store the data in the database.',
                                'error'
                            );
                            console.error(error);
                        }
                    });
                }
            });
        });
        // Check appointments and disable conflicting slots
        function checkAppointments(doctor, duration) {
            var serviceID = $('#edit-service-selector').children("option:selected").attr("id");
            console.log(date);
            console.log(duration);
            console.log(serviceID);
            $.ajax({
                url: 'check-appointments.php',
                method: 'POST',
                data: {
                    doctor: doctor,
                    date: date,
                    serviceID: serviceID
                },
                success: function(response) {
                    console.log(response);
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
        $.ajax({
            url: 'fetch-services.php',
            method: 'GET',
            success: function(response) {
                var services = JSON.parse(response);
                services.forEach(function(service) {
                    $('#edit-service-selector').append('<option id="' + service.Service_ID +
                        '">' + service.ServiceName + '</option>');
                });
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('An error occurred while trying to fetch services.');
            }
        });
        $('#edit-service-selector').change(function() {
            var selectedService = $(this).children("option:selected").attr("id");
            console.log('selected service: ' + selectedService);
            $('#edit-doctor-selector').empty().append('<option selected>Select</option>').prop(
                'disabled',
                true);
            if (selectedService !== 'select') {
                $.ajax({
                    url: 'fetch-doctors.php',
                    method: 'POST',
                    data: {
                        serviceID: selectedService
                    },
                    success: function(response) {
                        var doctors = JSON.parse(response);
                        doctors.forEach(function(doctor) {
                            $('#edit-doctor-selector').append($('<option>', {
                                text: doctor.Doctor_Name,
                                id: doctor.Doctor_ID
                            }));
                        });
                        $('#edit-doctor-selector').prop('disabled', false);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        alert('An error occurred while trying to fetch doctors.');
                    }
                });
                $.ajax({
                    url: 'fetch-service-duration.php',
                    method: 'POST',
                    data: {
                        service_id: selectedService
                    },
                    success: function(response) {
                        var result = JSON.parse(response);
                        if (result.duration) {
                            duration = parseInt(result.duration);
                            console.log('duration: ' + duration);
                        }
                    }
                });
            }
        });
        $("#confirmBtn").click(function() {
            $('#myModal').modal('hide');
            var reason = $('#reason').val();
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
                url: 'process-cancellation.php',
                method: 'POST',
                data: {
                    transactionCode: transactionCode,
                    reason: reason
                },
                success: function(response) {
                    Swal.fire(
                        'Appointment cancelled!',
                        response,
                        'success'
                    ).then(() => {
                        location.reload();
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
        $("#reject-confirm").click(function() {
            $('#reject-modal').modal('hide');
            var reason = $('#reject-reason').val();
            //console.log(transactionCode,reason);
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
                url: 'process-rejection.php',
                method: 'POST',
                data: {
                    transactionCode: transactionCode,
                    reason: reason
                },
                success: function(response) {
                    Swal.fire(
                        'Appointment rejected!',
                        response,
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert(
                        'An error occurred while trying to store the data in the database.'
                    );
                }
            });
        });
        $('#confirmBtn4').click(function() {
            var report = tinymce.get('report').getContent();
            var formData = new FormData();
            formData.append('code', transactionCode);
            formData.append('report', report);
            $('#report-modal').modal('hide');
            Swal.fire({
                title: 'Processing...',
                html: 'Please wait while we update your report',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            $.ajax({
                url: 'process-report.php',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    Swal.fire(
                        'Report sent!',
                        response,
                        'success'
                    ).then(() => {
                        location.reload();
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
        function fetchSelectors() {
            $.ajax({
                url: 'fetch-selectors.php',
                method: 'GET',
                success: function(response) {
                    try {
                        var data = JSON.parse(response);
                        var doctors = data.doctors;
                        var statuses = data.statuses;
                        var $doctorSelector = $('#doctor-selector');
                        var $statusSelector = $('#status-selector');
                        $doctorSelector.empty();
                        $statusSelector.empty();
                        $doctorSelector.append('<option selected>All</option>');
                        doctors.forEach(function(doctor) {
                            $doctorSelector.append('<option>' + doctor.Doctor_Name +
                                '</option>');
                        });
                        $statusSelector.append('<option selected>All</option>');
                        statuses.forEach(function(status) {
                            $statusSelector.append('<option>' + status + '</option>');
                        });
                    } catch (error) {
                        console.error('Invalid JSON response', error);
                        console.log(response); 
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert('An error occurred while trying to fetch selectors.');
                }
            });
        }
        fetchSelectors();
        filterTransactions();
        var table;
        function filterTransactions() {
            var selectedDoctor = $('#doctor-selector').val();
            var selectedStatus = $('#status-selector').val();
            $.ajax({
                url: 'filter-transactions.php',
                method: 'POST',
                data: {
                    doctor: selectedDoctor,
                    status: selectedStatus
                },
                success: function(response) {
                    try {
                        if ($.fn.DataTable.isDataTable('#datatable')) {
                            $('#datatable').DataTable().destroy();
                        }
                        var transactions = JSON.parse(response);
                        var $tbody = $('#datatable tbody');
                        $tbody.empty();
                        $.fn.dataTable.ext.type.order['status-sort-pre'] = function(data) {
                            const statusOrder = {
                                'Waiting Approval': 1,
                                'Approved': 2,
                                'Done': 3,
                                'Cancelled': 4,
                                'Rejected': 5,
                                'No Response': 6
                            };
                            if ($(data).is('span')) {
                                data = $(data).text().trim();
                            }
                            return statusOrder[data] || 7;
                        };
                        transactions.forEach(transaction => {
                            let statusBadge, actions;
                            switch (transaction.Status) {
                                case 'Waiting Approval':
                                    statusBadge =
                                        `<span class="badge" data-status="${transaction.Status}">${transaction.Status}</span>`;
                                    actions = `
                                <div class="d-flex gap-1 justify-content-center">
                                    <button class="btn btn-xs reject-appointment" style="background-color: #F5F7FB" 
                                    id="${transaction.Transaction_Code}" data-bs-toggle="tooltip" data-bs-placement="top" title="Reject">
                                    <i data-feather="x"></i>
                                    </button>
                                    <button class="btn btn-xs approve-appointment" style="background-color: #F5F7FB" 
                                    id="${transaction.Transaction_Code}" data-bs-toggle="tooltip" data-bs-placement="top" title="Accept">
                                    <i data-feather="check-circle"></i>
                                    </button>
                                </div>`;
                                    break;
                                case 'Approved':
                                    statusBadge =
                                        `<span class="badge" data-status="${transaction.Status}">${transaction.Status}</span>`;
                                    actions = `
                                <div class="d-flex gap-1 justify-content-center">
                                    <button class="btn btn-xs cancel-appointment" style="background-color: #F5F7FB" 
                                    id="${transaction.Transaction_Code}" data-bs-toggle="tooltip" data-bs-placement="top" title="Cancel">
                                    <i data-feather="x"></i>
                                    </button>
                                    <button class="btn btn-xs edit-appointment" style="background-color: #F5F7FB" 
                                    id="${transaction.Transaction_Code}" value="${transaction.AppointmentDate}" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                    <i data-feather="edit"></i>
                                    </button>
                                </div>`;
                                    break;
                                case 'Done':
                                    statusBadge =
                                        `<span class="badge" data-status="${transaction.Status}">${transaction.Status}</span>`;
                                    actions = `
                                <div class="d-flex gap-1 justify-content-center">
                                    <button class="btn btn-xs send-report" style="background-color: #F5F7FB" 
                                    id="${transaction.Transaction_Code}" data-bs-toggle="tooltip" data-bs-placement="top" title="Send Result">
                                    <i data-feather="send"></i>
                                    </button>
                                </div>`;
                                    break;
                                default:
                                    statusBadge =
                                        `<span class="badge" data-status="${transaction.Status}">${transaction.Status}</span>`;
                                    actions = `<div></div>`;
                            }
                            const row = `
                        <tr>
                            <td>${transaction.Client_ID}</td>
                            <td>${transaction.FirstName}</td>
                            <td>${transaction.LastName}</td>
                            <td>${transaction.Service}</td>
                            <td>${transaction.Doctor}</td>
                            <td>${transaction.AppointmentDate}</td>
                            <td>${transaction.AppointmentTime}</td>
                            <td>${statusBadge}</td>
                             <td>${transaction.IsPriority}</td>
                            <td><span class="transaction-code" id="${transaction.Transaction_Code}">${transaction.Transaction_Code}</span></td>
                            <td>${actions}</td>
                        </tr>`;
                            $tbody.append(row);
                        });
                        // Reinitialize DataTable with custom sorting
                        table = new DataTable('#datatable', {
                            columnDefs: [{
                                targets: 7, // Status column index
                                type: 'status-sort'
                            }],
                            order: [
                                [7, 'asc']
                            ], // Sort by status column in ascending order
                            drawCallback: function() {
                                // Reinitialize Feather icons after each draw (with a slight delay)
                                setTimeout(function() {
                                        feather
                                            .replace(); // Reinitialize Feather icons
                                    },
                                    100
                                ); // Adjust this timeout if necessary, 100ms is usually enough
                                // Reinitialize Bootstrap tooltips
                                var tooltipTriggerList = [].slice.call(document
                                    .querySelectorAll('[data-bs-toggle="tooltip"]'));
                                var tooltipList = tooltipTriggerList.map(function(
                                    tooltipTriggerEl) {
                                    return new bootstrap.Tooltip(
                                        tooltipTriggerEl);
                                });
                            }
                        });
                        // Custom DataTable styling and event bindings (rest of your existing code)
                        $('.dt-length label').remove();
                        $('.col-md-auto.me-auto').removeClass('col-md-auto me-auto').addClass(
                            'col-md-auto');
                        $('.dt-length label').remove();
                        $('.dt-search label').remove();
                        $('.row.mt-2.justify-content-between').removeClass(
                            'row mt-2 justify-content-between').addClass(
                            'row justify-content-between');
                        $('.dt-search').prepend('<span class="me-3" data-feather="search"></span>');
                        $('#doctor-selector, #status-selector').change(filterTransactions);
                        var urlParams = new URLSearchParams(window.location.search);
                        var dateValue = urlParams.get('date');
                        $('#dt-search-1').val(dateValue);
                        $('#dt-search-1').trigger('input');
                        $('#dt-search-1').val('');
                        // Initialize Feather icons
                        feather.replace();
                        // Event delegation for buttons (your existing code)
                        $(document).on('click', '.cancel-appointment', function() {
                            transactionCode = $(this).attr('id');
                            $("#myModal").modal('show');
                        });
                        $(document).on('click', '.reject-appointment', function() {
                            transactionCode = $(this).attr('id');
                            $("#reject-modal").modal('show');
                            $("#reject-code").text(transactionCode);
                        });
                        $(document).on('click', '.approve-appointment', function() {
                            transactionCode = $(this).attr('id');
                            $("#transactionCode2").text(transactionCode);
                            Swal.fire({
                                title: 'Confirmation',
                                text: "Do you want to approve this appointment?",
                                icon: 'warning',
                                confirmButtonText: 'Yes',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) {
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
                                        url: 'process-approval.php',
                                        method: 'POST',
                                        data: {
                                            code: transactionCode
                                        },
                                        success: function(response) {
                                            Swal.fire(
                                                'Appointment approved!',
                                                response,
                                                'success'
                                            ).then(() => {
                                                location
                                                    .reload();
                                            });
                                            showToast(response);
                                        },
                                        error: function(xhr, status,
                                            error) {
                                            console.error(error);
                                            alert(
                                                'An error occurred while trying to store the data in the database.'
                                            );
                                        }
                                    });
                                }
                            });
                        });
                        $(document).on('click', '.delete-record', function() {
                            transactionCode = $(this).attr('id');
                            $("#transactionCode3").text(transactionCode);
                            if (result.isConfirmed) {
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
                                    url: 'process-delete.php',
                                    method: 'POST',
                                    data: {
                                        code: transactionCode
                                    },
                                    success: function(response) {
                                        Swal.fire(
                                            'Transaction removed!',
                                            response,
                                            'success'
                                        ).then(() => {
                                            location.reload();
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
                            }
                        });
                        $(document).on('click', '.send-report', function() {
                            transactionCode = $(this).attr('id');
                            $("#report-modal").modal('show');
                            $("#transactionCode4").text(transactionCode);
                        });
                        $(document).on('click', '.edit-appointment', function() {
                            transactionCode = $(this).attr('id');
                            $("#edit-appointment-modal").modal('show');
                            date = $(this).val();
                        });
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
        }
    });
    </script>
    <title>Transactions</title>
    <link href="css/app.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
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
                            Data
                        </li>
                        <li class="sidebar-item ">
                            <a class="sidebar-link" href="index.php">
                                <i class="align-middle" data-feather="sliders"></i> <span
                                    class="align-middle">Dashboard</span>
                            </a>
                        </li>
                        <li class="sidebar-header">
                            Records
                        </li>
                        <li class="sidebar-item active">
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
                                <i class="align-middle" data-feather="user"></i> <span
                                    class="align-middle">Clients</span>
                            </a>
                        </li>
                        <li class="sidebar-header">
                            Maintenance
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="Maintenance/maintenance.php">
                                <i class="align-middle" data-feather="settings"></i> <span
                                    class="align-middle">Management
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
                        <h1 class="h3 mb-3">Transaction <strong>Records</strong> </h1>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="filter-section mb-3">
                                            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                                <label class="form-label mb-0">
                                                    <i data-feather="filter" class="me-2"></i>Filters
                                                </label>
                                            </div>
                                            <div class="d-flex flex-wrap gap-2">
                                                <select class="form-select" style="max-width: 200px"
                                                    id="doctor-selector">
                                                    <option selected>All</option>
                                                </select>
                                                <select class="form-select" style="max-width: 200px"
                                                    id="status-selector">
                                                    <option selected>All</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table id="datatable" class="table table-hover w-100">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Client ID</th>
                                                        <th>First Name</th>
                                                        <th>Last Name</th>
                                                        <th>Service</th>
                                                        <th>Doctor</th>
                                                        <th>Book Date</th>
                                                        <th>Time Slot</th>
                                                        <th>Status</th>
                                                        <th>Priority?</th>
                                                        <th>Transaction Code</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="body"></tbody>
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
                                        <p>Are you sure you want to cancel appointment?</p>
                                        <textarea id="reason" class="form-control" rows="4"
                                            placeholder="Reason for cancelling appointment"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn " data-dismiss="modal" id="cancel">No</button>
                                        <button type="button" class="btn btn-secondary" id="confirmBtn">Cancel
                                            Appointment</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade custom-modal" id="reject-modal" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Appointment Rejection</h5>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to reject appointment?</p>
                                        <textarea id="reject-reason" class="form-control" rows="4"
                                            placeholder="Reason for rejecting appointment"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn" data-dismiss="modal"
                                            id="reject-cancel">No</button>
                                        <button type="button" class="btn btn-secondary" id="reject-confirm">Yes</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade custom-modal" id="report-modal" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Appointment Report</h5>
                                    </div>
                                    <div class="modal-body">
                                        <textarea id="report" class="form-control" rows="4"
                                            placeholder="Enter Report Here"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn " data-dismiss="modal" id="cancel4">No</button>
                                        <button type="button" class="btn btn-secondary" id="confirmBtn4">Send</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade custom-modal" id="myModal2" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Appointment Approval</h5>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to Approve this appointment?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn " data-dismiss="modal" id="cancel2">No</button>
                                        <button type="button" class="btn btn-secondary"
                                            id="confirmBtn2">Confirm</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade custom-modal" id="myModal3" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Appointment Approval</h5>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to delete this record?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn" data-dismiss="modal" id="cancel3">No</button>
                                        <button type="button" class="btn btn-secondary"
                                            id="confirmBtn3">Confirm</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade custom-modal" id="edit-appointment-modal" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Appointment Edit</h5>
                                    </div>
                                    <div class="modal-body">
                                        <label class="form-label mt-2" for="customFile">Service</label>
                                        <select class="form-select mb-2 me-3" id="edit-service-selector">
                                            <option selected>Select</option>
                                        </select>
                                        <label class="form-label mt-2" for="customFile">Doctor</label>
                                        <select class="form-select mb-2 me-3" id="edit-doctor-selector" disabled>
                                            <option selected>Select</option>
                                        </select>
                                        <div class="align-self-center w-100" id="timeslot">
                                            <label class="form-label mt-2" for="customFile">AppointmentTime</label>
                                            <div class="mb-3" id="timeslots">
                                            </div>
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
        var table = new DataTable('#datatable', {
            columnDefs: [{
                targets: 7, 
                type: 'status-sort',
                render: function(data, type, row) {
                    if (type === 'sort') {
                        const statusOrder = {
                            'Waiting Approval': 1,
                            'Approved': 2,
                            'Done': 3,
                            'Cancelled': 4,
                            'Rejected': 5,
                            'No Response': 6
                        };
                        return statusOrder[data] ||
                            7; 
                    }
                    return data;
                }
            }]
        });
        </script>
        <script src="js/app.js"></script>
    </body>
</html>