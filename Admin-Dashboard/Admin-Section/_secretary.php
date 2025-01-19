<?php
session_start();
if (!isset($_SESSION["userID"])) {
    header("Location: ../Login-Registration/");
    exit();
}
define('ACCESS_GRANTED', true);
include('../../connection.php');
$query1 = "SELECT * FROM tbl_admin WHERE Access_Level = 2";
$result1 = $conn->query($query1);
$data = [];
if ($result1) {
    while ($row = $result1->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    echo "Error in query1: " . $conn->error;
}
$query2 = "SELECT * FROM tbl_admin WHERE Admin_ID = {$_SESSION['userID']}";
$result2 = $conn->query($query2);
$validateUser = [];
if ($result2) {
    $validateUser = $result2->fetch_assoc();
} else {
    echo "Error in query2: " . $conn->error;
}
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
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4@4/bootstrap-4.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
    .admin-table .fit-content {
        width: 1%;
        white-space: nowrap;
    }
    .admin-table th,
    .admin-table td {
        vertical-align: middle;
    }
    .admin-table .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
        border-radius: 0.2rem;
    }
    @media (max-width: 768px) {
        .admin-table td {
            padding: 0.75rem 0.5rem;
        }
        .admin-table .btn-xs {
            width: 100%;
            margin: 0.25rem 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .admin-table .feather-md {
            width: 1.25rem;
            height: 1.25rem;
        }
    }
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
        width: 100%;
    }
    table {
        width: 100%;
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
                <h1 class="h3 mb-3">Manage <strong>Secretary</strong></h1>
                <div class="card-body table-responsive">
                    <div class="card flex-fill">
                        <div class="card-header d-flex justify-content-between">
                            <h5 class="card-title mt-2 fs-9" style="font-size:18px;">
                                <span data-feather="user" class="feather-md me-2"></span>Accounts
                            </h5>
                            <button class="btn btn-xs btn-secondary" style="background-color: #232E3C"
                                data-bs-toggle="modal" data-bs-target="#addSecretaryModal">
                                <span data-feather="user-plus" class="me-2"></span>Add Secretary
                            </button>
                        </div>
                        <hr class="my-0" />
                        <div class="card-body table-responsive">
                            <table class="table admin-table">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fit-content" style="width: 1%">#</th>
                                        <th style="width: 30%">Name</th>
                                        <th style="width: 30%" class="d-none d-md-table-cell">Username</th>
                                        <th style="width: 20%">Status</th>
                                        <th class="fit-content" style="width: 1%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $count = 1;
            foreach ($data as $admin): ?>
                                    <tr>
                                        <td class="fit-content"><?php echo $count++; ?></td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span><?php echo htmlspecialchars($admin['Name']); ?></span>
                                                <small
                                                    class="text-muted d-md-none"><?php echo htmlspecialchars($admin['Username']); ?></small>
                                            </div>
                                        </td>
                                        <td class="d-none d-md-table-cell">
                                            <?php echo htmlspecialchars($admin['Username']); ?></td>
                                        <td>
                                            <?php if ($admin['Status'] == "Active"): ?>
                                            <p class='badge me-1 my-1 fs-9' style="background-color: #97DBC2">
                                                <?php echo htmlspecialchars($admin['Status']); ?></p>
                                            <?php else: ?>
                                            <p class='badge me-1 my-1 fs-9' style="background-color: #FFB0B0">
                                                <?php echo htmlspecialchars($admin['Status']); ?></p>
                                            <?php endif; ?>
                                        </td>
                                        <td class="fit-content">
                                            <div class="d-flex flex-md-row flex-column gap-1">
                                                <?php if ($admin['Access_Level'] == 2): ?>
                                                <button class="btn btn-xs edit-secretary my-1"
                                                    style="background-color: #F5F7FB"
                                                    data-id="<?php echo $admin['Admin_ID']; ?>" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="Edit">
                                                    <i data-feather="edit" class="feather-md"></i>
                                                </button>
                                                <button class="btn btn-xs delete-secretary my-1"
                                                    style="background-color: #F5F7FB"
                                                    data-id="<?php echo $admin['Admin_ID']; ?>" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="Delete">
                                                    <i data-feather="trash" class="feather-md"></i>
                                                </button>
                                                <?php if ($admin['Status'] == "Active"): ?>
                                                <button class="btn btn-xs deactivate-secretary my-1"
                                                    style="background-color: #F5F7FB"
                                                    data-id="<?php echo $admin['Admin_ID']; ?>" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="Deactivate">
                                                    <i data-feather="pause" class="feather-md"></i>
                                                </button>
                                                <?php else: ?>
                                                <button class="btn btn-xs activate-secretary my-1"
                                                    style="background-color: #F5F7FB"
                                                    data-id="<?php echo $admin['Admin_ID']; ?>" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="activate">
                                                    <i data-feather="play" class="feather-md"></i>
                                                </button>
                                                <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
            <?php include("components/footer.php")?>
            <div class="modal fade custom-modal" id="addSecretaryModal" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addSecretaryModalLabel">Add Secretary</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addSecretaryForm">
                                <div class="mb-3">
                                    <label for="firstName" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="firstName" name="firstName" required>
                                </div>
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="add-username" name="username" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn " data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-secondary" onclick="addSecretary()">Add
                                Secretary</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="editSecretaryModal" tabindex="-1" aria-labelledby="editSecretaryModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form id="editForm" method="POST" action="update_user.php">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editSecretaryModalLabel">Edit Secretary</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="admin_id" id="admin_id">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="edit-username" name="username" required>
                                </div>
                                <div class="mb-3">
                                    <label for="Password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="Password" name="Password" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn  btn-secondary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../js/app.js"></script>
    <script src=""></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.js"></script>
    <script>
    document.querySelectorAll('.sidebar-item .sidebar-link').forEach(link => {
        link.addEventListener('click', function() {
            document.querySelectorAll('.sidebar-item').forEach(item => item.classList.remove('active'));
            this.closest('.sidebar-item').classList.add('active');
        });
    });
    </script>
    <script>
    document.getElementById('addSecretaryModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('addSecretaryForm').reset();
    });
    </script>
    <script>
    function submitSecretaryForm() {
        $.ajax({
            type: "POST",
            url: "<?php echo $_SERVER['PHP_SELF']; ?>",
            data: $('#addSecretaryForm').serialize(),
            success: function(response) {
                if (response === "success") {
                    alert("Secretary added successfully!");
                    $('#addSecretaryModal').modal('hide');
                    location.reload();
                } else {
                    alert("Error: " + response);
                }
            }
        });
    }
    document.getElementById('addSecretaryModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('addSecretaryForm').reset();
    });
    </script>
    <script>
    function validatePassword(password) {
        if (password.length < 6) {
            return {
                valid: false,
                message: 'Password must be at least 6 characters long'
            };
        }
        if (!/[a-zA-Z]/.test(password)) {
            return {
                valid: false,
                message: 'Password must contain at least one letter'
            };
        }
        if (!/\d/.test(password)) {
            return {
                valid: false,
                message: 'Password must contain at least one number'
            };
        }
        return {
            valid: true,
            message: ''
        };
    }
    function addSecretary() {
        const form = document.getElementById('addSecretaryForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        const password = $('#addSecretaryForm input[name="password"]').val();
        const passwordValidation = validatePassword(password);
        if (!passwordValidation.valid) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Password',
                text: passwordValidation.message,
                confirmButtonColor: '#3085d6',
            });
            return;
        }
        const username = $('#addSecretaryForm input[name="username"]').val();
        $.ajax({
            url: 'process/check_username.php',
            type: 'POST',
            data: {
                username: username
            },
            dataType: 'json',
            success: function(checkResponse) {
                if (checkResponse.exists) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Username Exists',
                        text: 'This username is already taken. Please choose a different username.',
                        confirmButtonColor: '#3085d6',
                    });
                } else {
                    $.ajax({
                        url: 'process/add_secretary.php',
                        type: 'POST',
                        data: $('#addSecretaryForm').serialize(),
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === "success") {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message,
                                    confirmButtonColor: '#3085d6',
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $('#addSecretaryModal').modal('hide');
                                        $('#addSecretaryForm')[0].reset();
                                        location.reload();
                                    }
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to add secretary. Please try again.'
                            });
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to check username availability. Please try again.'
                });
            }
        });
    }
    </script>
    <script>
    $(document).ready(function() {
        $('.delete-secretary').on('click', function() {
            var adminId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'process/delete_secretary.php',
                        type: 'POST',
                        data: {
                            admin_id: adminId
                        },
                        success: function(response) {
                            if (response == 'success') {
                                Swal.fire(
                                    'Deleted!',
                                    'The admin has been deleted.',
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            } else if (response == 'Invalid') {
                                Swal.fire(
                                    'Invalid!',
                                    'Should remain atleast 1 secretary.',
                                    'error'
                                )
                            } else {
                                Swal.fire(
                                    'Error!',
                                    'There was an issue deleting the admin.',
                                    'error'
                                );
                            }
                        },
                        error: function() {
                            Swal.fire(
                                'Error!',
                                'An error occurred while processing your request.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    });
    $('.deactivate-secretary').on('click', function() {
        var adminId = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Deactivate'
        }).then((result) => {
            var status = 'Inactive'
            if (result.isConfirmed) {
                $.ajax({
                    url: 'process/activate-deactivate.php',
                    type: 'POST',
                    data: {
                        admin_id: adminId,
                        status: status
                    },
                    success: function(response) {
                        if (response == 'success') {
                            Swal.fire(
                                'Deactivated!',
                                'The admin has been deactivated.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else if (response == 'Invalid') {
                            Swal.fire(
                                'Invalid!',
                                'Should remain atleast 1 secretary.',
                                'error'
                            )
                        } else {
                            Swal.fire(
                                'Error!',
                                'There was an issue deleting the admin.',
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Error!',
                            'An error occurred while processing your request.',
                            'error'
                        );
                    }
                });
            }
        });
    });
    $('.activate-secretary').on('click', function() {
        var adminId = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Activate'
        }).then((result) => {
            var status = 'Active'
            if (result.isConfirmed) {
                $.ajax({
                    url: 'process/activate-deactivate.php',
                    type: 'POST',
                    data: {
                        admin_id: adminId,
                        status: status
                    },
                    success: function(response) {
                        if (response == 'success') {
                            Swal.fire(
                                'Activated!',
                                'The admin has been Activated.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                'There was an issue deleting the admin.',
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Error!',
                            'An error occurred while processing your request.',
                            'error'
                        );
                    }
                });
            }
        });
    });
    </script>
    <script>
    $(document).ready(function() {
        $('.edit-secretary').click(function() {
            var adminId = $(this).data('id');
            $.ajax({
                url: 'process/get_user.php',
                type: 'POST',
                data: {
                    admin_id: adminId
                },
                dataType: 'json',
                success: function(data) {
                    $('#admin_id').val(data.Admin_ID);
                    $('#first_name').val(data.Name);
                    $('#edit-username').val(data.Username);
                    $('#edit-username').data('original', data.Username);
                    $('#editSecretaryModal').modal('show');
                },
                error: function() {
                    alert('Failed to fetch data.');
                }
            });
        });
        $('#editForm').submit(function(event) {
            event.preventDefault();
            const password = $('#editForm input[name="Password"]').val();
            const newUsername = $('#editForm input[name="username"]').val();
            const originalUsername = $('#edit-username').data('original');
            if (password) {
                const passwordValidation = validatePassword(password);
                if (!passwordValidation.valid) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Password',
                        text: passwordValidation.message,
                        confirmButtonColor: '#3085d6',
                    });
                    return;
                }
            }
            if (newUsername !== originalUsername) {
                $.ajax({
                    url: 'process/check_username.php',
                    type: 'POST',
                    data: {
                        username: newUsername
                    },
                    dataType: 'json',
                    success: function(checkResponse) {
                        if (checkResponse.exists) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Username Exists',
                                text: 'This username is already taken. Please choose a different username.',
                                confirmButtonColor: '#3085d6',
                            });
                        } else {
                            proceedWithUpdate();
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to check username availability. Please try again.'
                        });
                    }
                });
            } else {
                proceedWithUpdate();
            }
        });
        function proceedWithUpdate() {
            $.ajax({
                url: 'process/edit_secretary.php',
                type: 'POST',
                data: $('#editForm').serialize(),
                dataType: 'json',
                success: function(response) {
                    $('#editSecretaryModal').modal('hide');
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: response.message,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message,
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
    </script>
    <script>
    $('input[type="text"], input[type="password"], input[type="email"], textarea').attr('maxlength', '50');
    $('input[type="text"], input[type="password"], input[type="email"], textarea').on('input', function() {
        const maxLength = 50;
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
            $('#' + feedbackId).text(''); 
        }
    });
    $('input[type="text"], input[type="password"], input[type="email"], textarea').on('paste', function(e) {
        let pastedData = e.originalEvent.clipboardData || window.clipboardData;
        let pastedText = pastedData.getData('Text');
        if (pastedText.length > 50) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Too Long!',
                text: 'Pasted text exceeds the maximum length of 50 characters.',
                confirmButtonColor: '#3085d6',
            });
        }
    });
    $('form').on('submit', function(e) {
        let invalidInputs = $(this).find('input, textarea').filter(function() {
            return $(this).val().length > 50;
        });
        if (invalidInputs.length > 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Some fields exceed the maximum length of 50 characters. Please check your inputs.',
                confirmButtonColor: '#5085d6',
            });
            invalidInputs.addClass('is-invalid');
        }
    });
    </script>
</body>
</html>