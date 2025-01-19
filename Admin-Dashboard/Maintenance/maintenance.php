<?php
session_start();
if (!isset($_SESSION["userID"])) {
    header("Location: ../Login-Registration/");
    exit();
}
$conn = require __DIR__ . "../../connection.php";
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
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.29.2/dist/feather.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.js"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
        new DataTable('#datatable');
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
                try {
                    var data = JSON.parse(response);
                    var services = data.services
                    var $tbody = $('#services-body');
                    $tbody.empty();
                    services.forEach(function(service) {
                        console.log(service.Service_ID, service.ServiceName);
                        var row = `
                        <tr>
                            <td>${service.ServiceName}</td>
                            <td>  </td>
                            <td>  </td>
                            <td>  </td>
                            <td>  </td>
                            <td>  </td>
                            <td>  </td>
                            <td>  </td>	
                            <td>  </td>	
                            <td>  </td>							
                            <td>  </td>	
                            <td>
                                <button class="btn btn-xs edit-services" data-id="${service.Service_ID}" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><span data-feather="edit"></span></button>
                                <button class="btn btn-xs delete-services" data-id="${service.Service_ID}" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete"><span data-feather="trash"></span></button>
                            </td>
                        </tr>
                    `;
                        $tbody.append(row);
                    });
                    feather.replace();
                    $('[data-bs-toggle="tooltip"]').tooltip();
                } catch (error) {
                    console.error('Invalid JSON response', error);
                    console.log(response); 
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('An error occurred while trying to fetch services.');
            }
        });
        $.ajax({
            url: 'fetch-schedule.php',
            method: 'GET',
            success: function(response) {
                try {
                    var data = JSON.parse(response);
                    var schedule = data.schedules
                    var $tbody = $('.schedule-body');
                    $tbody.empty();
                    schedule.forEach(function(schedules) {
                        var row = `                     
                                <p class='badge fs-6 bg-secondary' ">
                                    ${schedules.Working_Hours}
                                </p>                           
                    `;
                        $tbody.append(row);
                    });
                    feather.replace();
                    $('[data-bs-toggle="tooltip"]').tooltip();
                } catch (error) {
                    console.error('Invalid JSON response', error);
                    console.log(response); 
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('An error occurred while trying to fetch schedules.');
            }
        });
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
        $("#cancel").click(function() {
            $("#myModal").modal('hide');
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
        $('.edit-doctor').on('click', function() {
            var doctorId = $(this).data('id');
            $('#edit-id').val(doctorId);
            $('#edit-modal').modal('show');
        });
        $('.delete-doctor').on('click', function() {
            var doctorId = $(this).data('id');
            $('#delete-id').val(doctorId);
            $('#delete-modal').modal('show');
        });
        $('.add-doctor').on('click', function() {
            $('#add-modal').modal('show');
        });
        $('#add-cancel').on('click', function() {
            $('#add-modal').modal('hide');
        });
        $('.edit-services').on('click', function() {
            var doctorId = $(this).data('id');
        });
        $('.add-services').on('click', function() {
            $('#add-services-modal').modal('show');
        });
        $('#add-services-cancel').on('click', function() {
            $('#add-services-modal').modal('hide');
        });
        $('#add-services-confirm').on('click', function() {
            $('#add-services-modal').modal('hide');
            let addModel = false;
            addModel = $('#addModel').prop('checked');
            $('#addModel').on('change', function() {
                addModel = $(this).prop('checked');
            });
            var name = $('#service-name').val();
            var price = $('#service-price').val();
            var description = $('#service-description').val();
            var duration = $('#service-duration').val();
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
                url: 'process-add-service.php',
                method: 'POST',
                data: {
                    name: name,
                    description: description,
                    duration: duration,
                    addModel: addModel,
                    price: price,
                },
                success: function(response) {
                    Swal.fire(
                        'Service created!',
                        response,
                        'success'
                    ).then(() => {
                        location.reload();;
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
        $('#services-body').on('click', '.delete-services', function() {
            var serviceId = $(this).data('id');
            $('#delete-services-id').text(serviceId);
            $('#delete-services-modal').modal('show');
        });
        $('#delete-services-cancel').on('click', function() {
            $('#delete-services-modal').modal('hide');
        });
        $('#delete-services-confirm').on('click', function() {
            var serviceID = $('#delete-services-id').text();
            $('#delete-services-modal').modal('hide');
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
                url: 'process-delete-service.php',
                method: 'POST',
                data: {
                    serviceID: serviceID
                },
                success: function(response) {
                    Swal.fire(
                        'Service removed!',
                        response,
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                    showToast(response);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert('An error occurred while trying to delete the service.');
                }
            });
        });
        $('#services-body').on('click', '.edit-services', function() {
            var serviceID = $(this).data('id');
            $('#edit-services-modal').modal('hide');
            $('#edit-services-id').text(serviceID);
            $.ajax({
                url: 'fetch-services.php',
                method: 'POST',
                data: {
                    serviceID: serviceID
                },
                success: function(response) {
                    var values = JSON.parse(response);
                    var name = values.ServiceName;
                    var description = values.Description;
                    var price = values.Price;
                    $('#edit-service-name').val(name);
                    $('#edit-service-price').val(price);
                    $('#edit-service-description').val(description);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert('An error occurred while trying to delete the service.');
                }
            });
            $('#edit-services-modal').modal('show');
        });
        $('#edit-services-cancel').on('click', function() {
            $('#edit-services-modal').modal('hide');
        });
        $('#edit-services-confirm').on('click', function() {
            var serviceID = $('#edit-services-id').text();
            var name = $('#edit-service-name').val();
            var description = $('#edit-service-description').val();
            var price = $('#edit-service-price').val();
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
                url: 'process-edit-service.php',
                method: 'POST',
                data: {
                    serviceID: serviceID,
                    name: name,
                    description: description,
                    price: price
                },
                success: function(response) {
                    Swal.fire(
                        'Service Edited!',
                        response,
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                    showToast(response);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert('An error occurred while trying to delete the service.');
                }
            });
        });
        //edit schedule
        $('.edit-schedule').on('click', function() {
            $('#edit-schedule-modal').modal('show');
        });
        $('#edit-schedule-cancel').on('click', function() {
            $('#edit-schedule-modal').modal('hide');
        });
        $('#edit-schedule-confirm').on('click', function() {
            $('#edit-schedule-modal').modal('hide');
            var startTime = $('#start-time').find('input').val();
            var endTime = $('#end-time').find('input').val();
            var duration = $('#duration-time').val();
            console.log(startTime, endTime, duration);
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
                url: 'process-schedule.php',
                method: 'POST',
                data: {
                    start_time: startTime,
                    end_time: endTime,
                    duration: duration
                },
                success: function(response) {
                    Swal.fire(
                        'schedule edited!',
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
        //add doctors
        $('#add-confirm').click(function() {
            var doctorName = $('#add-doctor-name').val();
            var doctorStatus = $('#add-doctor-status').val();
            var doctorPassword = $('#add-doctor-password').val().trim();
            var selectedServices = [];
            // Check if password is empty
            if (!doctorPassword) {
                showToast('Password field is empty');
                return; // Exit function if password is empty
            }
            // Collect selected services
            $('input[name="services"]:checked').each(function() {
                selectedServices.push($(this).attr('id'));
            });
            Swal.fire({
                title: 'Processing...',
                html: 'Please wait while we update',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            // Proceed with AJAX if all checks pass
            $.ajax({
                url: 'process-add-doctor.php',
                method: 'POST',
                data: {
                    doctorName: doctorName,
                    doctorStatus: doctorStatus,
                    services: selectedServices,
                    password: doctorPassword
                },
                success: function(response) {
                    Swal.fire(
                        'Doctor Added!',
                        'Doctor has been added',
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                    showToast(response);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert('An error occurred while adding the doctor.');
                }
            });
        });
        //delete doctors
        $('#delete-confirm').click(function() {
            $('#delete-modal').modal('hide');
            var id = $('#delete-id').val();
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
                url: 'process-delete-doctor.php',
                method: 'POST',
                data: {
                    id: id
                },
                success: function(response) {
                    Swal.fire(
                        'Doctor Removed!',
                        response,
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                    showToast(response);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert('An error occurred while adding the doctor.');
                }
            });
        });
        //edit doctors
        $('#edit-modal').on('show.bs.modal', function(event) {
            var doctorId = $('#edit-id').val();
            console.log(doctorId);
            $.ajax({
                url: 'fetch-doctor-details.php',
                method: 'GET',
                data: {
                    doctorId: doctorId
                },
                success: function(response) {
                    try {
                        var data = JSON.parse(response);
                        $('#edit-doctor-name').val(data.doctor.Doctor_Name);
                        $('.edit-check input').prop('checked', false);
                        data.services.forEach(function(service) {
                            $('#' + service.Service_ID).prop('checked', true);
                        });
                    } catch (error) {
                        console.error('Error parsing JSON:', error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                    alert('An error occurred while fetching doctor details.');
                }
            });
        });
        $('#edit-confirm').on('click', function() {
            $('#edit-modal').modal('hide');
            var doctorId = $('#edit-id').val();
            var doctorName = $('#edit-doctor-name').val();
            var doctorPassword = $('#edit-doctor-password').val().trim();
            var selectedServices = [];
            if (!doctorPassword) {
                showToast('Password field is empty');
                return; 
            }
            $('input[name="edit-services"]:checked').each(function() {
                selectedServices.push($(this).attr('id'));
            });
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
                url: 'process-edit-doctor.php',
                method: 'POST',
                data: {
                    doctorId: doctorId,
                    doctorName: doctorName,
                    services: selectedServices,
                    password: doctorPassword
                },
                success: function(response) {
                    Swal.fire(
                        'Doctor edit successful!',
                        response,
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                    showToast(response);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                    alert('An error occurred while updating doctor details.');
                }
            });
        });
        $('#edit-cancel').on('click', function() {
            $('#edit-modal').modal('hide');
        });
        //activate doctor
        $('.activate-doctor').on('click', function() {
            var doctorID = $(this).data('id');
            $('#activate-modal').modal('show');
            console.log(doctorID);
            $('#activate-doctor-id').val(doctorID);
        });
        $('#activate-confirm').on('click', function() {
            $('#activate-modal').modal('hide');
            var doctorID = $('#activate-doctor-id').val();
            Swal.fire({
                title: 'Processing...',
                html: 'Please wait while we update',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
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
                url: 'process-doctor-activation.php',
                method: 'POST',
                data: {
                    doctorID: doctorID,
                },
                success: function(response) {
                    Swal.fire(
                        'Doctor Activated!',
                        response,
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                    showToast(response);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                    alert('An error occurred while updating doctor details.');
                }
            });
        });
        $('#deactivate-cancel').on('click', function() {
            $('#deactivate-modal').modal('hide');
        });
        $('.deactivate-doctor').on('click', function() {
            var doctorID = $(this).data('id');
            $('#deactivate-modal').modal('show');
            console.log(doctorID);
            $('#deactivate-doctor-id').val(doctorID);
        });
        $('#deactivate-confirm').on('click', function() {
            $('#deactivate-modal').modal('hide');
            var doctorID = $('#deactivate-doctor-id').val();
            console.log(doctorID);
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
                url: 'process-doctor-deactivation.php',
                method: 'POST',
                data: {
                    doctorID: doctorID,
                },
                success: function(response) {
                    Swal.fire(
                        'Doctor Deactivated!',
                        response,
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                    showToast(response);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                    alert('An error occurred while updating doctor details.');
                }
            });
        });
        $('#activate-cancel').on('click', function() {
            $('#activate-modal').modal('hide');
        });
        $('#doctorsTable').DataTable({
            responsive: true,
            columnDefs: [{
                className: 'text-center',
                targets: [3, 4]
            }]
        });
        // Initialize
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
        $('input[type="text"], input[type="password"], input[type="email"], textarea').attr('maxlength', '300');
        $('input[type="text"], input[type="password"], input[type="email"], textarea').on('input', function() {
            const maxLength = 300;
            const currentLength = $(this).val().length;
            const remainingChars = maxLength - currentLength;
            if (currentLength > maxLength) {
                $(this).val($(this).val().substring(0, maxLength));
            }
            let feedbackId = $(this).attr('id') + '-feedback';
            if ($('#' + feedbackId).length === 0) {
                $(this).after('<small id="' + feedbackId + '" class="text-muted"></small>');
            }
            if (currentLength > 0) {
                $('#' + feedbackId).text(`${remainingChars} characters remaining`);
                if (remainingChars <= 5) {
                    $('#' + feedbackId).removeClass('text-muted').addClass('text-danger');
                } else {
                    $('#' + feedbackId).removeClass('text-danger').addClass('text-muted');
                }
            } else {
                $('#' + feedbackId).text(''); // Clear feedback if input is empty
            }
        });
        $('input[type="text"], input[type="password"], input[type="email"], textarea').on('paste', function(e) {
            let pastedData = e.originalEvent.clipboardData || window.clipboardData;
            let pastedText = pastedData.getData('Text');
            if (pastedText.length > 300) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Too Long!',
                    text: 'Pasted text exceeds the maximum length of 300 characters.',
                    confirmButtonColor: '#3085d6',
                });
            }
        });
        $('form').on('submit', function(e) {
            let invalidInputs = $(this).find('input, textarea').filter(function() {
                return $(this).val().length > 300;
            });
            if (invalidInputs.length > 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Some fields exceed the maximum length of 300 characters. Please check your inputs.',
                    confirmButtonColor: '#5085d6',
                });
                invalidInputs.addClass('is-invalid');
            }
        });
        $("#service-price, #edit-service-price").on("input", function(e) {
            let value = $(this).val().replace(/[^0-9.]/g, '');
            let decimalCount = (value.match(/\./g) || []).length;
            if (decimalCount > 1) {
                value = value.replace(/\.(?=.*\.)/g, '');
            }
            if (value.includes('.')) {
                let parts = value.split('.');
                if (parts[1].length > 2) {
                    parts[1] = parts[1].substring(0, 2);
                    value = parts.join('.');
                }
            }
            $(this).val(value);
        });
        $("#service-price, #edit-service-price").on("paste", function(e) {
            e.preventDefault();
            let pastedText = (e.originalEvent.clipboardData || window.clipboardData)
                .getData('text');
            let cleanedText = pastedText.replace(/[^0-9.]/g, '');
            $(this).val(cleanedText);
        });
    });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalTriggers = {
            'edit-modal': '.edit-doctor',
        };
        Object.keys(modalTriggers).forEach(modalId => {
            const modal = document.getElementsByClassName(modalId);
            if (modal) {
                $(modal).on('show.bs.modal', function() {
                    console.log(`${modalId} is about to show`);
                }).on('shown.bs.modal', function() {
                    console.log(`${modalId} has been shown`);
                }).on('hide.bs.modal', function() {
                    console.log(`${modalId} is about to hide`);
                }).on('hidden.bs.modal', function() {
                    console.log(`${modalId} has been hidden`);
                });
            } else {
                console.error(`Modal ${modalId} not found in DOM`);
            }
        });
    });
    </script>
    <title>Management</title>
    <link href="../css/app.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
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
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="../Client-Records/Clients.php">
                            <i class="align-middle" data-feather="user"></i> <span class="align-middle">Clients</span>
                        </a>
                    </li>
                    <li class="sidebar-header">
                        Maintenance
                    </li>
                    <li class="sidebar-item active">
                        <a class="sidebar-link" href="">
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
                    <h1 class="h3 mb-3">Appointment <strong>Details</strong></h1>
                    <div class="row flex-fill">
                        <div class="col-xl-9 col-md-10">
                            <div class="card flex-fill">
                                <div class="card-header d-flex justify-content-between">
                                    <h5 class="card-title mt-2 fs-9" style="font-size:18px;"> <span data-feather="user"
                                            class="feather-md me-2"></span>Doctors<span></h5>
                                    <button class="btn btn-xs btn-secondary add-doctor"
                                        style="background-color: #232E3C"><span data-feather="user-plus"
                                            class="me-2"></span>Add Doctors</button></span>
                                </div>
                                <hr class="my-0" />
                                <div class="card-body ">
                                    <div class="table-responsive">
                                        <?php
                                        $conn = require __DIR__ . "../../../connection.php";
                                        $query = "
                                                SELECT 
                                                d.Doctor_ID,
                                                d.Doctor_Name,
                                                d.Status,
                                                GROUP_CONCAT(s.ServiceName SEPARATOR ', ') AS services
                                                FROM 
                                                    tbl_doctors d
                                                LEFT JOIN 
                                                    tbl_doctor_services ds ON d.Doctor_ID = ds.Doctor_ID
                                                LEFT JOIN 
                                                    tbl_services s ON ds.Service_ID = s.Service_ID
                                                GROUP BY 
                                                    d.Doctor_ID, d.Doctor_Name, d.Status
                                                ";
                                        $result = $conn->query($query);
                                        $doctors = [];
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                $doctors[] = $row;
                                            }
                                        }
                                        $conn->close();
                                        ?>
                                        <table id="doctorsTable" class="table dt-responsive nowrap " cellspacing="0"
                                            width="100%">
                                            <thead class="table-light ">
                                                <tr>
                                                    <th>Doctor ID</th>
                                                    <th>Name</th>
                                                    <th>Services</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($doctors as $doctor): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($doctor['Doctor_ID']); ?></td>
                                                    <td><?php echo htmlspecialchars($doctor['Doctor_Name']); ?></td>
                                                    <td><?php echo htmlspecialchars($doctor['services']); ?></td>
                                                    <?php
                                                        if ($doctor['Status'] === 'Active') {
                                                            $td = '<td><span class="badge fs-6" style="background-color: #97DBC2">' . htmlspecialchars($doctor['Status']) . '</span></td>';
                                                            $button = '<button class="btn btn-xs  deactivate-doctor" style ="background-color: #F5F7FB"   data-id="' . $doctor['Doctor_ID'] . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Deactivate"><i data-feather="pause"></i></button>';
                                                        } else {
                                                            $td = '<td><span class="badge fs-6" style="background-color: #FFB0B0">' . htmlspecialchars($doctor['Status']) . '</span></td>';
                                                            $button = '<button class="btn btn-xs  activate-doctor" style ="background-color: #F5F7FB"  data-id="' . $doctor['Doctor_ID'] . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Activate"><i data-feather="play"></i></button>';
                                                        }
                                                        ?>
                                                    <?php echo $td; ?>
                                                    <td>
                                                        <button class="btn btn-xs  edit-doctor"
                                                            style="background-color: #F5F7FB"
                                                            data-id="<?php echo $doctor['Doctor_ID']; ?>"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Edit"><i data-feather="edit"
                                                                class="feather-md"></i></button>
                                                        <button class="btn btn-xs  delete-doctor"
                                                            style="background-color: #F5F7FB"
                                                            data-id="<?php echo $doctor['Doctor_ID']; ?>"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Delete"><i data-feather="trash"
                                                                class="feather-md"></i></button>
                                                        <?php echo $button; ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-xl-3">
                            <div class="card mb-3">
                                <div class="card-header d-flex justify-content-between ">
                                    <h5 class="card-title mt-2 fs-9" style="font-size:18px;"> <span data-feather="tool"
                                            class="feather-md me-2"></span>Services<span></h5>
                                    <button class="btn btn-xs btn-secondary add-services"
                                        style="background-color: #232E3C"><span class="me-3"
                                            data-feather="plus-square"></span>Add</button>
                                </div>
                                <hr class="my-0" />
                                <div class="card-body">
                                    <div class="table-responsive text-nowrap">
                                        <table id="servicesTable"
                                            class="table table-sm w-auto table-borderless table-hover dt-responsive nowrap "
                                            cellspacing="0" width="100%">
                                            <tbody id="services-body">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade custom-modal" id="edit-modal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <?php
                                try {
                                    error_reporting(E_ALL);
                                    ini_set('display_errors', 1);
                                    $connectionPath = $_SERVER['DOCUMENT_ROOT'] . '/connection.php';
                                    if (!file_exists($connectionPath)) {
                                        throw new Exception("Connection file not found at: " . $connectionPath);
                                    }
                                    $conn = require $connectionPath;
                                    if (!$conn) {
                                        throw new Exception("Database connection failed");
                                    }
                                    $query = "SELECT * FROM tbl_services";
                                    $result = $conn->query($query);
                                    if (!$result) {
                                        throw new Exception("Query failed: " . $conn->error);
                                    }
                                    $services = [];
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $services[] = $row;
                                        }
                                    }
                                    $conn->close();
                                } catch (Exception $e) {
                                    echo "<!-- Error: " . htmlspecialchars($e->getMessage()) . " -->";
                                    $services = []; // Ensure services is defined even if there's an error
                                }
                                ?>
                                <input type="hidden" id="edit-id">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Edit Doctor</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="">
                                        <label class="form-label">Doctor Name:</label>
                                        <input type="text" id="edit-doctor-name" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label mt-2">Services:</label>
                                        <div id="doctor-services" class="form-control" style="height: auto;">
                                            <?php if (empty($services)): ?>
                                            <p class="text-danger">No services found or error loading services</p>
                                            <?php else: ?>
                                            <?php foreach ($services as $service): ?>
                                            <div class="form-check edit-check">
                                                <input class="form-check-input" type="checkbox"
                                                    id="<?php echo htmlspecialchars($service['Service_ID']); ?>"
                                                    name="edit-services">
                                                <label class="form-check-label">
                                                    <?php echo htmlspecialchars($service['ServiceName']); ?>
                                                </label>
                                            </div>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <label class="form-label">Password:</label>
                                    <input type="password" id="edit-doctor-password" class="form-control">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn" data-dismiss="modal" id="edit-cancel">No</button>
                                    <button type="button" class="btn btn-secondary" id="edit-confirm">Confirm</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Add similar error handling to other modals -->
                    <div class="modal fade custom-modal" id="delete-modal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Delete Doctor</h5>
                                </div>
                                <div class="modal-body">
                                    <p class="mb-3">Are you sure you want to remove this doctor?</p>
                                    <p class="text-muted">(Note: Move the appointment to another doctor if it has one,
                                        or else it won't be removed)</p>
                                    <input type="hidden"><span id="delete-id"></span></input>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn " data-dismiss="modal"
                                        id="delete-cancel">Close</button>
                                    <button type="button" class="btn btn-secondary" id="delete-confirm">Confirm</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade custom-modal" id="add-modal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Add Doctor</h5>
                                </div>
                                <?php
                                $conn = require __DIR__ . "../../../connection.php";
                                $query = "SELECT * FROM tbl_services";
                                $result = $conn->query($query);
                                $services = [];
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $services[] = $row;
                                    }
                                }
                                $conn->close();
                                ?>
                                <div class="modal-body">
                                    <div class="">
                                        <label class="form-label">Doctor Name:</label>
                                        <input type="text" id="add-doctor-name" class="form-control"></input>
                                    </div>
                                    <label class="form-label mt-2">Services:</label>
                                    <div class="form-group">
                                        <div id="doctor-services" class="form-control" style="height: auto;">
                                            <?php foreach ($services as $service): ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    id="<?php echo $service['Service_ID'] ?>" name="services">
                                                <label class="form-check-label">
                                                    <?php echo $service['ServiceName'] ?></label><br>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <label class="form-label mt-2">Status:</label>
                                    <select class="form-select mb-2" id="add-doctor-status">
                                        <option selected>Active</option>
                                        <option>Inactive</option>
                                    </select>
                                    <label class="form-label">Password:</label>
                                    <input type="password" id="add-doctor-password" class="form-control"
                                        placeholder=""></input>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn " data-dismiss="modal" id="add-cancel">No</button>
                                    <button type="button" class="btn btn-secondary" id="add-confirm">Confirm</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade custom-modal" id="add-services-modal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Add Services</h5>
                                </div>
                                <div class="modal-body">
                                    <div class=""><label class="form-label">Service Name</label>
                                        <input type="text" id="service-name" class="form-control" required></input>
                                    </div>
                                    <label class="form-label mt-3">Description</label>
                                    <textarea id="service-description" class="form-control" rows="5"
                                        required></textarea>
                                    <label class="form-label mt-3">Duration</label>
                                    <select class="form-select mb-2" id="service-duration">
                                        <option value="30" selected>30 mins</option>
                                        <option value="60">60 mins</option>
                                        <option value="90">90 mins</option>
                                        <option value="120">120 mins</option>
                                        <option value="150">150 mins</option>
                                        <option value="180">180 mins</option>
                                        <option value="210">210 mins</option>
                                        <option value="240">240 mins</option>
                                    </select>
                                    <div class="mb-2">
                                        <label class="form-label">Service Price</label>
                                        <input type="text" id="service-price" class="form-control" placeholder="0.00"
                                            pattern="^\d*\.?\d{0,2}$" required>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="true" id="addModel">
                                        <label class="form-check-label" for="addModel">
                                            Add Permanent 3d Teeth Model
                                        </label>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn " data-dismiss="modal"
                                        id="add-services-cancel">cancel</button>
                                    <button type="button" class="btn btn-secondary"
                                        id="add-services-confirm">Add</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade custom-modal" id="delete-services-modal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Delete Service</h5>
                                </div>
                                <div class="modal-body">
                                    <label class="form-label">Are you sure you want to remove this service?</label>
                                    <p class="text-muted">(Note: This service wont be removed if there are appointments
                                        made with this)</p>
                                    <input type="hidden" id="delete-services-id">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn " data-dismiss="modal"
                                        id="delete-services-cancel">No</button>
                                    <button type="button" class="btn btn-secondary"
                                        id="delete-services-confirm">Yes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade custom-modal" id="edit-services-modal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Edit Services</h5>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" id="edit-services-id">
                                    <div class="">
                                        <label class="form-label">Service Name</label>
                                        <input type="text" id="edit-service-name" class="form-control"></input>
                                    </div>
                                    <label class="form-label mt-3">Description</label>
                                    <textarea id="edit-service-description" class="form-control" rows="5"></textarea>
                                    <div class="my-2">
                                        <label class="form-label">Service Price</label>
                                        <input type="text" id="edit-service-price" class="form-control"
                                            placeholder="0.00" pattern="^\d*\.?\d{0,2}$" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn " data-dismiss="modal"
                                        id="edit-services-cancel">cancel</button>
                                    <button type="button" class="btn btn-secondary" id="edit-services-confirm">Save
                                        Changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade custom-modal" id="edit-schedule-modal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Edit Schedule</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Start time:</label>
                                        <div class="input-group date" id="start-time" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input"
                                                data-target="#start-time" />
                                            <div class="input-group-append" data-target="#start-time"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text h-100"><i class="fa fa-clock-o"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label">End time:</label>
                                        <div class="input-group date" id="end-time" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input"
                                                data-target="#end-time" />
                                            <div class="input-group-append" data-target="#end-time"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text h-100"><i class="fa fa-clock-o"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label">Duration time: <span class="text-muted">(in
                                                minutes)</span></label>
                                        <input type="text" id="duration-time" class="form-control"></input>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn " data-dismiss="modal"
                                            id="edit-schedule-cancel">cancel</button>
                                        <button type="button" class="btn btn-secondary" id="edit-schedule-confirm">Save
                                            Changes</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade custom-modal" id="activate-modal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Activate Doctor</h5>
                                </div>
                                <div class="modal-body">
                                    <label class="form-label">Are you sure you want to activate this doctor?</label>
                                    <input type="hidden" id="activate-doctor-id">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-muted" data-dismiss="modal"
                                        id="activate-cancel">No</button>
                                    <button type="button" class="btn btn-secondary" id="activate-confirm">Yes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade custom-modal" id="deactivate-modal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Deactivate Doctor</h5>
                                </div>
                                <div class="modal-body">
                                    <label class="form-label text-muted">(Note: There might be appointments already made
                                        for this doctor! Please move the appointment to another doctor if it has
                                        one)</label>
                                    <label class="form-label">Would you still wish to <span
                                            class="text-danger">deactivate</span>?</label>
                                    <input type="hidden" id="deactivate-doctor-id">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-muted" data-dismiss="modal"
                                        id="deactivate-cancel">No</button>
                                    <button type="button" class="btn btn-secondary" id="deactivate-confirm">Yes</button>
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
    <script>
    $(document).ready(function() {
    });
    </script>
</body>
</html>