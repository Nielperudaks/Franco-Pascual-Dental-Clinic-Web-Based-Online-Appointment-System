<?php
$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conn = require __DIR__ . "../../../connection.php";

    // Query to get the user data from the database
    $query = sprintf(
        "SELECT * FROM tbl_admin WHERE Username = '%s'",
        $conn->real_escape_string($_POST["username"])
    );

    $result = $conn->query($query);
    $validateUser = $result->fetch_assoc();

    // Verify the password
    if ($validateUser && password_verify($_POST["lPass"], $validateUser["Password"])) {
        session_start();
        session_regenerate_id();  // Secure session regeneration

        // Set session variables
        $_SESSION["userID"] = $validateUser["Admin_ID"];

        // Check the Access_Level value
        if ($validateUser["Access_Level"] == 2) {
            // Redirect for Access_Level 2 (Admin, etc.)
            header("Location: ../index.php");
            exit;
        } elseif ($validateUser["Access_Level"] == 1) {
            // Redirect for Access_Level 1 (Doctor)
            header("Location: ../Doctor-Section/index.php");
            exit;
        } else {
            echo "Invalid Access Level";
            exit;
        }
    } else {
        // Handle invalid password or username
        echo 'Invalid username or password';
        $is_invalid = true;
    }
}
?>
