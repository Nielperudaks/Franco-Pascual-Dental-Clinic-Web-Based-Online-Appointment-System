<?php
session_start();
if (isset($_SESSION['valid']) && $_SESSION['valid'] === true) {
    header("Location: /Client-Dashboard");
} else if (isset($_SESSION['validAdmin']) && $_SESSION['validAdmin'] === true) {
    header("Location: /Admin-Dashboard");
} else {
    header("Location: /error-404.php");
    exit;
}
?>