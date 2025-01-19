<?php
session_start();
if (!isset($_SESSION["userID"]) || $_SESSION['valid'] != true) {
    header("Location: ../../Patient_Login/index.php");
    exit;
}
    $conn = require __DIR__ . "../../../connection.php";
    $query = "SELECT * FROM tbl_clients WHERE Client_ID = {$_SESSION["userID"]}";
    $result = $conn->query($query);
    $validateUser = $result->fetch_assoc();
    $profileImage = $validateUser['Image'] ?? 'img/avatars/avatar-6.jpg';
    $profileImage = '../'.$profileImage;
	$query = "SELECT * FROM tbl_treatment_records WHERE Client_ID = {$_SESSION["userID"]}";
	$result = $conn->query($query);
	$transactions = [];
	while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
    ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
    <script src="https://cdn.tiny.cloud/1/ubg63rs3fsu2nhbko5duyq8jc65aixeut5x6yocylewwb5cw/tinymce/7/tinymce.min.js"
        referrerpolicy="origin"></script>
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
    </style>
    <script>
    $(document).ready(function() {
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
        $('.dt-length label').remove();
        $('.dt-search label').remove();
        $('.dt-search').prepend('<span class="me-3" data-feather="search"></span>');
        feather.replace();
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
    });
    </script>
    <title>Client Dashboard</title>
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
                        Appointment
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="../index.php">
                            <i class="align-middle" data-feather="calendar"></i> <span
                                class="align-middle">Calendar</span>
                        </a>
                    </li>
                    <li class="sidebar-header">
                        User
                    </li>
                    <li class="sidebar-item ">
                        <a class="sidebar-link" href="../Transactions.php">
                            <i class="align-middle" data-feather="user"></i> <span class="align-middle">Profile</span>
                        </a>
                    </li>
                    <li class="sidebar-item ">
                        <a class="sidebar-link" href="../appointment-records.php">
                            <i class="align-middle" data-feather="book"></i> <span class="align-middle">Appointment
                                Records</span>
                        </a>
                    </li>
                    <li class="sidebar-header">
                        Payment
                    </li>
                    <li class="sidebar-item active">
                        <a class="sidebar-link" href="Microtransactions.php">
                            <i class="align-middle" data-feather="credit-card"></i> <span
                                class="align-middle">Microtransactions</span>
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
                            <p class=" d-none d-sm-inline-block pt-2">
                                <img src="<?php echo htmlspecialchars($profileImage); ?>"
                                    class="avatar img-fluid rounded me-1" alt="Charles Amar" /> <span
                                    class="text-dark"><?= htmlspecialchars($validateUser["FirstName"]) ?></span>
                            </p>
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
                        <h1 class="h3 mb-3">Microtransaction <strong>Records</strong> </h1>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
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
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($transactions as $transaction): ?>
                                                <tr>
                                                    <td class="treatment-id">
                                                        <?= htmlspecialchars($transaction['Treatment_ID']) ?></td>
                                                    <td><?= htmlspecialchars($transaction['Transaction_Code']) ?></td>
                                                    <td><?= htmlspecialchars($transaction['Treatment_Date']) ?></td>
                                                    <td><?= htmlspecialchars($transaction['Treatment_Name']) ?></td>
                                                    <td><?= htmlspecialchars($transaction['Dentist']) ?></td>
                                                    <td>₱<?= htmlspecialchars($transaction['Treatment_Cost']) ?></td>
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
            $('.pay-btn').click(function() {
                var btn = $(this);
                var transactionCode = btn.data('transaction');
                var amount = btn.data('amount');
                showModal();
                $.ajax({
                    url: 'process-payment.php',
                    method: 'POST',
                    data: {
                        transaction_code: transactionCode,
                        amount: amount
                    },
                    success: function(response) {
                        try {
                            var data = JSON.parse(response);
                            if (data.success) {
                                window.open(data.checkout_url, '_blank');
                            } else {
                                hideModal();
                                alert('Payment processing failed: ' + data.message);
                            }
                        } catch (e) {
                            hideModal();
                            console.error('Error processing payment:', e);
                            alert('An error occurred while processing the payment');
                        }
                    },
                    error: function(xhr, status, error) {
                        hideModal();
                        console.error('AJAX Error:', error);
                        alert('An error occurred while processing the payment');
                    }
                });
            });
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
</body>
</html>
</html>