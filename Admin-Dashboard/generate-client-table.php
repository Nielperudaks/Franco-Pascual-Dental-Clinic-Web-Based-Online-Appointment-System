<?php
session_start();
if (!isset($_SESSION["userID"])) {
    header("Location: Login-Registration/");
    exit();
}

$conn = require __DIR__ . "/../connection.php"; // Note the corrected path

// Use prepared statement
$query = "SELECT * FROM tbl_admin WHERE Admin_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION["userID"]); // "i" for integer
$stmt->execute();
$result = $stmt->get_result();
$validateUser = $result->fetch_assoc();

if (!$validateUser || ($validateUser['Access_Level'] != 1 && $validateUser['Access_Level'] != 2)) {
    header("Location: Login-Registration/");
    exit();
}


// Query to fetch data from tbl_clients
$query = "SELECT Client_ID, FirstName, LastName FROM tbl_clients";
$result = $conn->query($query);

// Check if there are any rows returned
if ($result->num_rows > 0) {
    // Output table header
    echo '<table id="datatable" class="table table-sm   table-hover dt-responsive " cellspacing="0" width="100%">
            <thead class="table-light">
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>';

    // Loop through each row and display it in the table
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td></td>';
        echo '<td>' . $row['Client_ID'] . '</td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td></td>';
        
        echo '<td>' . $row['FirstName'] . '</td>';
        echo '<td>' . $row['LastName'] . '</td>';
        echo '<td><button class="btn btn-xs followup-btn btn-outline-secondary" id="'.$row['Client_ID'].'" data-bs-toggle="tooltip" data-bs-placement="top" title="Confirm"><span data-feather="check"></span></button> </td>';
        echo '</tr>';
    }

    // Close table body and table
    echo '</tbody>
        </table>';
} else {
    // If no rows are returned, display a message
    echo 'No clients found';
}

// Close the database connection
$conn->close();