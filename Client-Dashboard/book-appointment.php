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



    include("get-date.php");
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

    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4@4/bootstrap-4.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>


    <title>Client Dashboard</title>

    <link href="css/app.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

    
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
        width: 114.7vh;
        height: 100%;
        position: relative;
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

    /* Make the card height and width responsive */
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
        max-width: 1200px;
        margin: 0 auto;
        position: relative;
        min-height: 300px;
        /* Minimum height for 3D model */
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
        var date = "<?php echo $decryptedDate ?>";
        var duration;

        $('#selectedDate').data('originalValue', date);


        
        $.ajax({
            url: 'fetch-services.php',
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
            console.log(selectedService);
            $.ajax({
                url: 'fetch_add_model.php', 
                type: 'POST',
                data: {
                    serviceId: selectedService
                },
                success: function(response) {
                    let result = JSON.parse(response);
                    if (result.status === 'success') {

                        
                        addModel = result.addModel;
                        console.log('AddModel value from DB:', addModel);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching AddModel:', error);
                }
            });
            
            

            
            $('#doctor-selector').empty().append('<option selected>Select</option>').prop('disabled',
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
                            
                            $('#doctor-selector').append($('<option>', {
                                text: doctor.Doctor_Name,
                                id: doctor.Doctor_ID
                            }));
                        });

                        
                        $('#doctor-selector').prop('disabled', false);
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

                            

                        }
                    }
                });
            }
        });




        
        function generateTimeSlots(duration, doctor) {
            
            var startHour = 9;
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
                    `<button id="${buttonId}" class="btn btn-success fs-3 m-3 open-modal-btn">${slotStart} - ${slotEnd}</button>`;

                $('#timeslots').append(timeSlotBtn);

                
                startHour = slotEndHour;
                startMinute = slotEndMinute;
            }


            
            checkAppointments(doctor, duration);
        }



        
        function checkAppointments(doctor, duration) {
            var serviceID = $('#serviceSelect').children("option:selected").attr("id");

            $.ajax({
                url: 'check-appointments.php',
                method: 'POST',
                data: {
                    doctor: doctor,
                    date: date,
                    serviceID: serviceID
                },
                success: function(response) {
                    var appointments = JSON.parse(response);

                    
                    appointments.forEach(function(appointment) {
                        var startTime = appointment.time; 
                        var appointmentDuration = appointment
                            .duration; 

                        
                        var startHour = parseInt(startTime.split(':')[0]);
                        var startMinutes = parseInt(startTime.split(':')[1]);

                        
                        var totalMinutesStart = startHour * 60 + startMinutes;

                        
                        var totalMinutesEnd = totalMinutesStart + appointmentDuration;

                        
                        for (var currentMinutes = totalMinutesStart; currentMinutes <
                            totalMinutesEnd; currentMinutes += 30) {
                            var currentHour = Math.floor(currentMinutes / 60);
                            var currentMin = currentMinutes % 60;

                            
                            var formattedHour = currentHour.toString().padStart(2, '0');
                            var formattedMinutes = currentMin.toString().padStart(2, '0');
                            var buttonId = '#timeSlot' + formattedHour + formattedMinutes;

                            
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



        



        
        $('#serviceSelect').change(function() {
            if ($(this).val() === 'Select') {
                $('#doctor-selector').prop('disabled', true).val('Select');
                $('#timeslot').fadeOut(100);
                $('#3d-teeth').fadeOut(100);

            } else {
                $('#timeslot').fadeOut(100);
                $('#3d-teeth').fadeOut(100);
                $('#doctor-selector').prop('disabled', false);
            }
        });
        $('#doctor-selector').change(function() {
            if ($(this).val() !== 'Select') {
                $('#timeslot').fadeOut(100).fadeIn(600);
                console.log(addModel);
                if (addModel == 'true') {
                    $('#3d-teeth').fadeOut(100).fadeIn(600);
                } else {
                    $('#3d-teeth').fadeOut(0);
                }

                $('.legends-container').fadeOut(100).fadeIn(1000);


                
                $('html, body').animate({
                    scrollTop: $('#timeslot').offset().top - 30 
                }, 100); 
            } else {
                $('#timeslot').fadeOut(100);
                $('#3d-teeth').fadeOut(100);
                $('.legends-container').fadeOut(100);
                
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
                    
                }
            });
        }

        $('#timeslots').on('click', '.open-modal-btn', function() {
            var selectedService = $('#serviceSelect').val();
            var selectedDoctor = $('#doctor-selector').val();
            var appointmentTime = $(this).text();
            const targetTooth = selectedToothNumbers.join(', ');
            

            $('#selectedDate').text(date);
            $('#selectedService').text(selectedService);
            $('#appointmentTime').text(appointmentTime);
            $('#selectedDoctor').text(selectedDoctor);
            $('#targetTooth').text(targetTooth);
            $('#myModal').modal('show');
        });

        $('#priority-select').on('change', function() {
            var selectedValue = $(this).val();

            if (selectedValue === 'Yes') {
                $('#priority-message').text(
                    `Pay 100 pesos to get prioritized 
                    (Note: Getting a priority will guarantee an appointment spot. No returns when availed)`
                );
            } else {
                $('#priority-message').text("Standard Priority (Note: Appointment is not guaranteed)");
            }
        });
        
        $('#priority-select').trigger('change');

        $('#cancel').click(function() {
            $('#myModal').modal('hide');
        });

        $('#confirmBtn').click(function() {
            $('#myModal').modal('hide');
            var createCode = generateTransactionCode();

            var priority = $('#priority-select').val();
            console.log(priority);
            if (priority == 'Yes') {
                const transactionCode = createCode;
                const amount = 100;

                
                webhookModal.show();
                pollingInterval = setInterval(checkForNewWebhook, 5000);

                
                $.ajax({
                    url: 'Micro-transactions/process-payment.php',
                    method: 'POST',
                    data: {
                        transaction_code: transactionCode,
                        amount: amount
                    },
                    success: function(response) {
                        try {
                            const data = JSON.parse(response);
                            if (data.success) {
                                window.open(data.checkout_url, '_blank');



                            } else {
                                stopPollingAndHideModal();
                                alert('Payment processing failed: ' + data.message);
                            }
                        } catch (e) {
                            stopPollingAndHideModal();
                            console.error('Error processing payment:', e);
                            alert('An error occurred while processing the payment');
                        }
                    },
                    error: function(xhr, status, error) {
                        stopPollingAndHideModal();
                        console.error('AJAX Error:', error);
                        alert('An error occurred while processing the payment');
                    }
                });

            } else {

                createCode = generateTransactionCode();
                var selectedService = $('#selectedService').text();
                var appointmentTime = $('#appointmentTime').text();
                var targetTooth = $('#targetTooth').text();
                var selectedDoctor = $('#doctor-selector').val();
                var doctorID = $('#doctor-selector').children("option:selected").attr("id");
                var serviceID = $('#serviceSelect').children("option:selected").attr("id");
                var clientID = <?php echo $validateUser["Client_ID"]?>;
                var priority = $('#priority-select').val();


                var originalDate = $('#selectedDate').data('originalValue');
                var rdate = $('#selectedDate').text();
                if (originalDate !== rdate) {
                    alert("Warning: Selected date has been modified!");
                    location.reload();
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
                    url: 'process-appointment.php',
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

                        showToast('New appointment created');
                        Swal.fire(
                            'Appointment created!',
                            response,
                            'success'
                        ).then(() => {
                            window.location.href = "Transactions.php";

                        });


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

        
        let lastWebhook = '';
        const webhookModal = new bootstrap.Modal(document.getElementById('webhookModal'));
        let pollingInterval;
        let countdown;
        const modal = document.getElementById('webhookModal');
        const modalInstance = new bootstrap.Modal(modal);

        
        function showModal() {
            document.getElementById('webhookDetails').innerText =
                "Verifying payment, this may take a while...";
            modalInstance.show();
            pollingInterval = setInterval(checkForNewWebhook, 5000);
        }

        
        function hideModal() {
            modalInstance.hide();
            if (pollingInterval) {
                clearInterval(pollingInterval);
            }
            clearWebhookLog();
        }

        
        function checkForNewWebhook() {
            fetch('Micro-transactions/get-latest-webhook.php', {
                    cache: "no-store"
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Webhook data received:', data);
                    if (data.isNew) {
                        
                        document.querySelector('.loader').style.display = 'none';
                        document.querySelector('.check-icon').style.display = 'block';

                        
                        document.getElementById('modalTitle').textContent = 'Payment Successful';
                        document.getElementById('webhookDetails').innerHTML = `
							<div class="alert alert-success">
								<i data-feather="check-circle" class="me-2"></i>
								${data.message}
							</div>
						`;

                        
                        feather.replace();

                        
                        const webhookModal = new bootstrap.Modal(document.getElementById('webhookModal'));
                        webhookModal.show();

                        
                        confetti({
                            particleCount: 100,
                            spread: 70,
                            origin: {
                                y: 0.6
                            }
                        });

                        
                        clearInterval(pollingInterval);

                        
                        
                        var createCode = generateTransactionCode();
                        var selectedService = $('#selectedService').text();
                        var appointmentTime = $('#appointmentTime').text();
                        var targetTooth = $('#targetTooth').text();
                        var selectedDoctor = $('#doctor-selector').val();
                        var doctorID = $('#doctor-selector').children(
                            "option:selected").attr("id");
                        var serviceID = $('#serviceSelect').children(
                            "option:selected").attr("id");
                        var clientID = <?php echo $validateUser["Client_ID"];?>;
                        var priority = $('#priority-select').val();


                        var originalDate = $('#selectedDate').data('originalValue');
                        var rdate = $('#selectedDate').text();
                        if (originalDate !== rdate) {
                            alert("Warning: Selected date has been modified!");
                            location.reload();
                        }

                        $.ajax({
                            url: 'process-appointment.php',
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

                                showToast('New appointment created');
                                setTimeout(function() {
                                    window.location.href =
                                        'Transactions.php';
                                }, 3500);


                            },
                            error: function(xhr, status, error) {

                                console.error(error);
                                alert(
                                    'An error occurred while trying to store the data in the database.'
                                );
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error checking webhooks:', error);
                });
        }


        
        function clearWebhookLog() {
            fetch('Micro-transactions/modules/clear-webhook-log.php')
                .then(response => response.text())
                .then(data => {
                    console.log(data);
                })
                .catch(error => console.error('Error clearing log:', error));
        }
        modal.querySelector('.btn-close').addEventListener('click', function() {
            hideModal();
            if (countdown) {
                clearInterval(countdown);
            }
        });

        
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                hideModal();
                if (countdown) {
                    clearInterval(countdown);
                }
            }
        });

        
        modal.addEventListener('hidden.bs.modal', function() {
            hideModal();
            if (countdown) {
                clearInterval(countdown);
            }
        });

        function stopPollingAndHideModal() {
            clearInterval(pollingInterval);
            webhookModal.hide();
        }

        
        document.querySelector('.btn-close').addEventListener('click', function() {
            stopPollingAndHideModal();
        });

        
        document.getElementById('webhookModal').addEventListener('hidden.bs.modal', function() {
            stopPollingAndHideModal();
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

<body>
    <div class="wrapper">
        
        <div class="modal fade" id="webhookModal" tabindex="-1" aria-labelledby="webhookModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="modalTitle">Processing Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="icon-container mb-4">
                            <div class="spinner-border text-primary loader" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <i data-feather="check-circle" class="check-icon text-success"
                                style="display: none; width: 48px; height: 48px;"></i>
                        </div>
                        <div id="webhookDetails" class="mt-3">
                            Please wait while we verify your payment. This may take a moment.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
            <div id="toastContainer"></div>
        </div>

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

                            
                            <div class="card flex-fill card-3d" id="3d-teeth">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Teeth Model</h5>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <div class="align-self-center" id="teeth-container">
                                        <div id="tooltip" style="display: none; position: absolute;"></div>
        
                                        <button id="selectAllButton" onclick="toggleSelectAll()"><span
                                                data-feather="grid"></span> Select / Deselect</button>
                                    </div>
                                    <table id="toothTable">
                                        <tr id="upperTeethRow">
                                            
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

                            <div class="card flex-fill" id="timeslot">
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
                                    <h5 class="card-title">Schedule</h5>
                                    <p class='badge fs-6 bg-secondary'>9:00 - 10:00</p>
                                    <p class='badge fs-6 bg-secondary'>10:00 - 11:00</p>
                                    <p class='badge fs-6 bg-secondary'>11:00 - 12:00</p>
                                    <p class='badge fs-6 bg-secondary'>12:00 - 13:00</p>
                                    <p class='badge fs-6 bg-secondary'>13:00 - 14:00</p>
                                    <p class='badge fs-6 bg-secondary'>14:00 - 15:00</p>
                                    <p class='badge fs-6 bg-secondary'>15:00 - 16:00</p>
                                    <p class='badge fs-6 bg-secondary'>16:00 - 17:00</p>
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
                                    <p class="badge bg-white text-muted me-1 my-1 fs-6 text-wrap p-3">Target tooth: <span
                                            id="targetTooth"></span></p>
                                </div>
                                <p class=" me-1 my-1 fs-6 mt-3">Priority: </p>
                                <p id="priority-message" class="font-weight-light"></p>
                                <select class="form-select mb-3 service-select" id="priority-select">
                                    <option selected>No</option>
                                    <option>Yes</option>
                                </select>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn " data-dismiss="modal" id="cancel">Cancel</button>
                                <button type="button" class="btn btn-secondary" id="confirmBtn">Confirm
                                    Appointment</button>
                            </div>
                        </div>
                    </div>
                </div>

        </div>

        </main>


    </div>

    </div>


    <script src="js/app.js"></script>
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
    var raycaster = new THREE.Raycaster(); 
    var mouse = new THREE.Vector2(); 
    var selectedTeeth = []; 
    var hoveredTooth = null; 
    var tooltip = document.getElementById('tooltip'); 
    var clock = new THREE.Clock(); 
    var allSelected = false; 
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

        // Add zoom limits
        controls.minDistance = 3; // Minimum zoom distance (closer to object)
        controls.maxDistance = 10; // Maximum zoom distance (further from object)
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
        
        loadTooth('r_letter', '3d-teeth/teeth/r_letter.stl', {
            x: 0,
            y: 0,
            z: 0
        }, true, 0xCFE2F3);

        
        loadTooth('l_letter', '3d-teeth/teeth/l_letter.stl', {
            x: 0,
            y: 0,
            z: 0
        }, true, 0xCFE2F3);


        
        
        var hemisphereLight = new THREE.HemisphereLight(0xffffff, 0x444444, 1.2);
        hemisphereLight.position.set(0, 1, 0); 
        scene.add(hemisphereLight);

        
        var directionalLight = new THREE.DirectionalLight(0xffffff, 1.0);
        directionalLight.position.set(2, 1, 3); 
        directionalLight.castShadow = true;
        scene.add(directionalLight);

        
        var fillLight = new THREE.PointLight(0xffffff, 0.8);
        fillLight.position.set(0, -3, 3); 
        scene.add(fillLight);

        
        renderer = new THREE.WebGLRenderer({
            antialias: true,
            alpha: true 
        });
        renderer.setPixelRatio(window.devicePixelRatio);
        renderer.setSize(container.clientWidth, container.clientHeight);
        renderer.gammaInput = true;
        renderer.gammaOutput = true;

        container.appendChild(renderer.domElement);

        window.addEventListener('resize', onWindowResize, false);
        window.addEventListener('mousemove', onDocumentMouseMove, false); 
        window.addEventListener('click', onDocumentMouseDown, false); 


    }

    function loadTooth(name, stlPath, position, isLetter = false, color = 0xAAAAAA) {
        var loader = new THREE.STLLoader();
        var material = new THREE.MeshPhongMaterial({
            color: color, 
            specular: 0xffffff,
            shininess: 100
        });
        loader.load(stlPath, function(geometry) {
            var toothMesh = new THREE.Mesh(geometry, material);
            toothMesh.scale.set(0.3, 0.3, 0.3);
            toothMesh.name = name; 
            toothMesh.position.set(position.x, position.y, position.z); 
            toothMesh.isLetter = isLetter; 
            scene.add(toothMesh);
           
        });


    }


    
    
    function toggleSelectAll() {
        if (allSelected) {
            
            scene.children.forEach(function(object) {
                if (!object.isLetter && object.type === "Mesh") {
                    object.material.color.set(0xAAAAAA); 

                }
            });

            
            selectedTeeth = [];
            selectedToothNumbers = []

            
            
            
            
            document.querySelectorAll('.tooth-cell').forEach(cell => {

                if (cell.classList.contains('selected')) {
                    cell.classList.remove('selected'); 
                }

            });

        } else {
            
            scene.children.forEach(function(object) {
                if (!object.isLetter && object.type === "Mesh") {
                    object.material.color.set(0xFF0000); 
                    if (!selectedTeeth.includes(object)) {
                        selectedTeeth.push(object); 
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

            

        }
        console.log(selectedToothNumbers);

        allSelected = !allSelected; 
    }


    
    document.getElementById('selectAllButton').addEventListener('mouseenter', function() {
        scene.children.forEach(function(object) {
            if (!object.isLetter && object.type === "Mesh") {
                object.material.color.set(0xFFFF00); 
            }
        });
    });

    
    document.getElementById('selectAllButton').addEventListener('mouseleave', function() {
        scene.children.forEach(function(object) {
            if (!object.isLetter && object.type === "Mesh") {
                if (selectedTeeth.includes(object)) {
                    object.material.color.set(0xFF0000); 
                } else {
                    object.material.color.set(0xAAAAAA); 
                }
            }
        });
    });

    document.querySelectorAll('.tooth-cell').forEach(cell => {
        cell.addEventListener('mouseenter', function() {
            let toothNumber = this.getAttribute('data-tooth');

            highlightToothInModel(toothNumber); 
            this.classList.add('hovered');



        });

        cell.addEventListener('mouseleave', function() {
            let toothNumber = this.getAttribute('data-tooth');
            resetToothInModel(toothNumber); 
            this.classList.remove('hovered');

        });

        cell.addEventListener('click', function() {
            let toothNumber = parseInt(this.getAttribute('data-tooth'));
            

            if (toothNumber) {
                if (selectedToothNumbers.includes(toothNumber)) {
                    
                    selectedToothNumbers = selectedToothNumbers.filter(num => num !== toothNumber);
                } else {
                    
                    selectedToothNumbers.push(toothNumber);
                }
            }

            
            toggleSelectToothInModel(toothNumber); 

            this.classList.toggle('selected');
        });
    });




    function onWindowResize() {
        
        const width = container.clientWidth;
        const height = container.clientHeight;

        
        camera.aspect = width / height;
        camera.updateProjectionMatrix();

        
        renderer.setSize(width, height);

        
        controls.handleResize();
    }

    
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
        
        const rect = container.getBoundingClientRect();
        mouse.x = ((event.clientX - rect.left) / container.clientWidth) * 2 - 1;
        mouse.y = -((event.clientY - rect.top) / container.clientHeight) * 2 + 1;

        
        raycaster.setFromCamera(mouse, camera);
        const intersects = raycaster.intersectObjects(scene.children, true);

        if (intersects.length > 0) {
            const currentTooth = intersects[0].object;

            
            if (currentTooth.isLetter) {
                tooltip.style.display = 'none'; 
                return; 
            }

            
            if (hoveredTooth && hoveredTooth !== currentTooth && !selectedTeeth.includes(hoveredTooth)) {
                hoveredTooth.material.color.set(0xAAAAAA); 
                resetTableCell(hoveredTooth.name); 

            }

            
            if (hoveredTooth !== currentTooth && !selectedTeeth.includes(currentTooth)) {
                highlightTableCell(currentTooth.name); 
                currentTooth.material.color.set(0xFFFF00); 
                hoveredTooth = currentTooth; 
                
            }

            
            updateTooltip(currentTooth.name);
        } else {
            resetTableCell(); 
            tooltip.style.display = 'none'; 

            if (hoveredTooth && !selectedTeeth.includes(hoveredTooth)) {
                hoveredTooth.material.color.set(0xAAAAAA); 
            }
            hoveredTooth = null;
        }
    }


    function onDocumentMouseDown(event) {
        
        const rect = container.getBoundingClientRect();
        mouse.x = ((event.clientX - rect.left) / container.clientWidth) * 2 - 1;
        mouse.y = -((event.clientY - rect.top) / container.clientHeight) * 2 + 1;

        raycaster.setFromCamera(mouse, camera);
        var intersects = raycaster.intersectObjects(scene.children, true);

        if (intersects.length > 0) {
            var clickedTooth = intersects[0].object;

            
            if (clickedTooth.isLetter) {
                return; 
            }

            
            if (selectedTeeth.includes(clickedTooth)) {
                clickedTooth.material.color.set(0xAAAAAA); 
                selectedTeeth = selectedTeeth.filter(tooth => tooth !== clickedTooth); 

                
                const toothNumber = getToothNumberByName(clickedTooth.name);
                if (toothNumber) {
                    selectedToothNumbers = selectedToothNumbers.filter(num => num !== parseInt(toothNumber));
                }


            } else {
                clickedTooth.material.color.set(0xFF0000); 
                selectedTeeth.push(clickedTooth); 

                
                const toothNumber = getToothNumberByName(clickedTooth.name);
                if (toothNumber && !selectedToothNumbers.includes(parseInt(toothNumber))) {
                    selectedToothNumbers.push(parseInt(toothNumber));
                }

            }
        }
        if (intersects.length > 0) {
            var clickedTooth = intersects[0].object;
            if (!clickedTooth.isLetter) {
                toggleTableCell(clickedTooth.name);
               
            }
        }
    }
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
        
    };

    function getToothNumberByName(toothName) {
        return Object.keys(toothMapping).find(key => toothMapping[key] === toothName);
    }

    function highlightToothInModel(toothNumber) {
        var toothName = toothMapping[toothNumber]
        scene.children.forEach(function(object) {
            if (object.name === toothName) {
                object.material.color.set(0xFFFF00); 
                updateTooltip(toothName);
              
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