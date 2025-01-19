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
	$query = "SELECT * FROM tbl_treatment_records";
	$result = $conn->query($query);
	$transactions = [];
	while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
    .icon-container {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 20px 0;
    }
    .check-icon {
        display: none;
        opacity: 0;
        transform: scale(0.5);
        transition: all 0.3s ease-in-out;
    }
    .check-icon.show {
        opacity: 1;
        transform: scale(1);
    }
    .loader {
        transition: opacity 0.3s ease-in-out;
    }
    .feather-small {
        width: 14px;
        height: 14px;
        stroke-width: 2;
        margin-right: 4px;
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
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
        var treatmentID;
        var table = $('#datatable').DataTable({
            columnDefs: [{
                targets: 6, 
                type: 'string',
                render: function(data, type, row) {
                    if (type === 'sort') {
                        return $(data).text().trim() === 'Pending' ? '0' : '1';
                    }
                    return data;
                }
            }],
            order: [
                [6, 'asc'], 
                [2, 'desc'] 
            ],
            pageLength: 10,
            responsive: true,
            dom: '<"row justify-content-between"<"col-md-auto"l><"col-md-auto"f>>rt<"row justify-content-between"<"col-md-auto"i><"col-md-auto"p>>',
            language: {
                search: "",
                searchPlaceholder: "Search records..."
            },
            footerCallback: function(row, data, start, end, display) {
                var api = this.api();
                var intVal = function(i) {
                    if (typeof i === 'string') {
                        return parseFloat(i.replace(/[₱,]/g, '')) || 0;
                    }
                    return typeof i === 'number' ? i : 0;
                };
                var total = api
                    .column(5, {
                        search: 'applied'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                var formattedTotal = '₱' + total.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                $(api.column(5).footer()).html(formattedTotal);
            }
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
        $('.dt-length label').remove();
        $('.dt-search label').remove();
        $('.dt-search').prepend('<span class="me-3" data-feather="search"></span>');
        feather.replace();
        let pollingInterval;
        let lastWebhook = '';
        const webhookModal = new bootstrap.Modal(document.getElementById('webhookModal'));
        function checkForNewWebhook() {
            fetch('get-latest-webhook.php', {
                    cache: "no-store"
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Webhook data received:', data);
                    if (data.isNew) {
                        console.log(treatmentID);
                        $.ajax({
                            method: 'POST',
                            url: 'update-payment.php',
                            data: {
                                TreatmentID: treatmentID
                            },
                            error: function() {
                                alert('An error occurred while updating the records.');
                            }
                        });
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
                        setTimeout(() => {
                            location.reload();
                        }, 3000);
                    }
                })
                .catch(error => {
                    console.error('Error checking webhooks:', error);
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
        $('.pay-btn').click(function() {
            var row = $(this).closest('tr');
            treatmentID = row.find('.treatment-id').text();
            var treatmentName = row.find('.treatment-name').text();
            const btn = $(this);
            const transactionCode = btn.data('transaction');
            const originalAmount = btn.data('amount');
            Swal.fire({
                title: 'Discount Eligibility',
                text: 'Are you a PWD or Senior Citizen?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
                reverseButtons: true,
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Enter ID Number',
                        html: `
                <select id="discount-type" class="swal2-input">
                    <option value="PWD">PWD</option>
                    <option value="Senior">Senior Citizen</option>
                </select>
                <input type="text" id="id-number" class="swal2-input" placeholder="Enter ID Number">
            `,
                        confirmButtonText: 'Proceed',
                        showCancelButton: true,
                        preConfirm: () => {
                            const idNumber = document.getElementById('id-number')
                                .value;
                            const discountType = document.getElementById(
                                'discount-type').value;
                            if (!idNumber) {
                                Swal.showValidationMessage(
                                'Please enter ID number');
                                return false;
                            }
                            if (!/^\d+$/.test(idNumber)) {
                                Swal.showValidationMessage(
                                    'ID number must contain only numeric characters'
                                    );
                                return false;
                            }
                            if (idNumber.length > 11) {
                                Swal.showValidationMessage(
                                    'ID number must not exceed 11 characters');
                                return false;
                            }
                            return {
                                idNumber,
                                discountType
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const discountedAmount = originalAmount * 0.8;
                            webhookModal.show();
                            pollingInterval = setInterval(checkForNewWebhook, 5000);
                            processPayment({
                                transaction_code: transactionCode,
                                amount: discountedAmount,
                                treatmentName: treatmentName,
                                pwdNumber: result.value.idNumber,
                                discountType: result.value.discountType,
                                originalAmount: originalAmount
                            });
                        }
                    });
                } else {
                    webhookModal.show();
                    pollingInterval = setInterval(checkForNewWebhook, 5000);
                    processPayment({
                        transaction_code: transactionCode,
                        amount: originalAmount,
                        treatmentName: treatmentName
                    });
                }
            });
        });
        function processPayment(paymentData) {
            $.ajax({
                url: 'process-payment.php',
                method: 'POST',
                data: paymentData,
                success: function(response) {
                    try {
                        const data = JSON.parse(response);
                        if (data.success) {
                            window.open(data.checkout_url, '_blank');
                        } else {
                            stopPollingAndHideModal();
                            Swal.fire({
                                icon: 'error',
                                title: 'Payment Failed',
                                text: data.message || 'Payment processing failed'
                            });
                        }
                    } catch (e) {
                        stopPollingAndHideModal();
                        console.error('Error processing payment:', e);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while processing the payment'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    stopPollingAndHideModal();
                    console.error('AJAX Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while processing the payment'
                    });
                }
            });
        }
        $('#btn-cash').click(function() {
            var row = $(this).closest('tr');
            var treatmentID = row.find('.treatment-id').text();
            $('#TreatmentID').val(treatmentID);
            $('#confirm-modal').modal('show');
        });
        $('.confirmBtn').click(function() {
            console.log($('#TreatmentID').val());
            $.ajax({
                method: 'POST',
                url: 'update-payment.php',
                data: {
                    TreatmentID: $('#TreatmentID').val()
                },
                success: function(response) {
                    $('#confirm-modal').modal('hide');
                    showToast(
                        response); 
                    setTimeout(function() {
                        window.location.href = "Microtransactions.php";
                    }, 2000);
                },
                error: function() {
                    alert('An error occurred while updating the records.');
                }
            });
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
    });
    </script>
    <title>Microtransactions</title>
    <link href="../css/app.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
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
    <div class="wrapper">
        <nav id="sidebar" class="sidebar js-sidebar">
            <div class="sidebar-content js-simplebar">
                <div class="text-center">
                    <img src="../img/photos/Logo.png" class="img-fluid rounded-circle" width="144" height="144" />
                    <a class="sidebar-brand" href="../index.php">
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
                    <li class="sidebar-item active">
                        <a class="sidebar-link" href="Microtransactions.php">
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
                </ul>
            </div>
        </nav>
        <div class="main">
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
                    <div class="modal fade custom-modal" id="confirm-modal" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Cash Payment Confirmation
                                    </h5>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to confirm this action?</p>
                                    <input type="hidden" id="TreatmentID">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn " data-dismiss="modal" id="cancel">No</button>
                                    <button type="button" class="btn btn-secondary confirmBtn">Confirm</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid p-0">
                        <h1 class="h3 mb-3">Payment <strong>Records</strong> </h1>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table id="datatable" class="table dt-responsive nowrap" cellspacing="0"
                                                width="100%">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Treatment ID</th>
                                                        <th>Transaction Code</th>
                                                        <th>Treatment Date</th>
                                                        <th>Treatment Name</th>
                                                        <th>Doctor</th>
                                                        <th>Treatment Cost</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($transactions as $transaction): ?>
                                                    <tr>
                                                        <td class="treatment-id">
                                                            <?= htmlspecialchars($transaction['Treatment_ID']) ?></td>
                                                        <td><?= htmlspecialchars($transaction['Transaction_Code']) ?>
                                                        </td>
                                                        <td><?= htmlspecialchars($transaction['Treatment_Date']) ?></td>
                                                        <td class="treatment-name">
                                                            <?= htmlspecialchars($transaction['Treatment_Name']) ?></td>
                                                        <td><?= htmlspecialchars($transaction['Dentist']) ?></td>
                                                        <td>₱<?= htmlspecialchars($transaction['Treatment_Cost']) ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($transaction['Payment_Status'] === 'Pending'): ?>
                                                            <p class="badge me-1 my-1 fs-9"
                                                                style="background-color: #F4D951">
                                                                <?= htmlspecialchars($transaction['Payment_Status']) ?>
                                                            </p>
                                                            <?php elseif ($transaction['Payment_Status'] === 'Paid'): ?>
                                                            <p class="badge me-1 my-1 fs-9"
                                                                style="background-color: #97DBC2">
                                                                <?= htmlspecialchars($transaction['Payment_Status']) ?>
                                                            </p>
                                                            <?php else: ?>
                                                            <?= htmlspecialchars($transaction['Payment_Status']) ?>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($transaction['Payment_Status'] === 'Pending'): ?>
                                                            <button class="btn btn-success btn-sm pay-btn"
                                                                data-transaction="<?= htmlspecialchars($transaction['Transaction_Code']) ?>"
                                                                data-amount="<?= htmlspecialchars($transaction['Treatment_Cost']) ?>">
                                                                <i data-feather="credit-card" class="feather-small"></i>
                                                                Pay
                                                                Online
                                                            </button>
                                                            <button class="btn btn-primary btn-sm" id="btn-cash">
                                                                <i data-feather="dollar-sign" class="feather-small"></i>
                                                                Cash
                                                            </button>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="5" style="text-align:right">Total:</th>
                                                        <th></th>
                                                        <th colspan="2"></th>
                                                    </tr>
                                                </tfoot>
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
                                    <a class="text-muted" href="#" target="_blank"><strong>Franco - Pascual</strong></a>
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
        <script src="../js/app.js"></script>
        <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>
        <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.js"></script>
        <script src="https://unpkg.com/feather-icons"></script>
        <script>
        feather.replace();
        </script>
        <script>
        $(document).ready(function() {
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
                fetch('get-latest-webhook.php', {
                        cache: "no-store"
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.isNew) {
                            document.querySelector('.loader').style.display = 'none';
                            document.querySelector('.check-icon').style.display = 'block';
                            document.getElementById('modalTitle').textContent = 'Payment Successful';
                            document.getElementById('webhookDetails').innerHTML = `
										<div class="alert alert-success">
											${data.message}
										</div>`;
                            feather.replace();
                            clearInterval(pollingInterval);
                            setTimeout(() => {
                                location.reload();
                            }, 3000);
                        }
                    })
                    .catch(error => {
                        console.error('Error checking webhooks:', error);
                    });
            }
            function clearWebhookLog() {
                fetch('modules/clear-webhook-log.php')
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
        });
        </script>
        </script>
</body>
</html>
</html>