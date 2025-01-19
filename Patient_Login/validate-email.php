<?php
session_start();
if ($_SERVER['PHP_SELF'] === '/validate-email.php' && $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
    // Redirect to error page if accessed directly (not as an AJAX request)
    header("Location: ../404.php"); // Change to your desired error page
    exit;
}
// Database connection
$conn = require __DIR__ . "../../connection.php";
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Ensure the request is a POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    header("Location: ../404.php"); // Change to your desired error pages
    exit;
}

// Validate CSRF token
$csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
if (!$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
    http_response_code(403); // Forbidden
    echo json_encode(["error" => "Invalid CSRF token"]);
    exit;
}

// Parse JSON request body
$input = json_decode(file_get_contents('php://input'), true);
$email = filter_var($input['email'] ?? '', FILTER_SANITIZE_EMAIL);

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400); // Bad Request
    header("Location: ../404.php"); // Change to your desired error page
    exit;
}

// Check if email exists in the database
$stmt = $conn->prepare("SELECT * FROM tbl_clients WHERE email = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $email);
if (!$stmt->execute()) {
    die("Execution failed: " . $stmt->error);
}

$result = $stmt->get_result();
if ($result && $result->num_rows === 0) {
    echo json_encode(["available" => true]);
} else {
    echo json_encode(["available" => false]);
}

$stmt->close();
$conn->close();
?>