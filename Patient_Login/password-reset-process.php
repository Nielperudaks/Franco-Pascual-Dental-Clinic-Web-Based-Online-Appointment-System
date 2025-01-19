<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST' ) {
header("Location: ../404.php");  // Redirect to 404 page if accessed directly
exit();
}
$token = $_POST["token"];
$hashedToken = hash("sha256", $token);
$conn = require __DIR__ . "../../connection.php";
$query = "SELECT * FROM tbl_clients WHERE ResetTokenHash = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $hashedToken);
$stmt->execute();
$result = $stmt->get_result();
$validateUser = $result->fetch_assoc();
if ($validateUser === null) {
    header('index.php');
}
if (strtotime($validateUser["ResetTokenExpiration"]) <= time()) {
    header('index.php');
}
if (strlen($_POST["password"]) < 8) {
    header('index.php');
}

if (!preg_match("/[a-z]/i", $_POST["password"])) {
    header('index.php');
}
if (!preg_match("/[0-9]/i", $_POST["password"])) {
    header('index.php');
}

if ($_POST["password"] !== $_POST["rPassword"]) {
    header('index.php');
}

$passHash = password_hash($_POST["password"], PASSWORD_DEFAULT);
echo $validateUser["Client_ID"];
$query = "UPDATE tbl_clients SET 
    Password = ?, 
    ResetTokenExpiration = NULL, 
    ResetTokenHash = NULL 
    WHERE Client_ID = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $passHash, $validateUser["Client_ID"]);
$stmt->execute();

header("location: index.php")
?>

