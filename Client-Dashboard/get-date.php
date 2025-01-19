<?php
session_start();
if (!isset($_SESSION["userID"]) || $_SESSION['valid'] != true) {
    header("Location: ../Patient_Login/index.php");
    exit;
}
    $key = "diznuts";
    $decryptedDate = decryptDate($_GET["date"], $key);
    if ($decryptedDate !== false && preg_match('/^\d{4}-\d{2}-\d{2}$/', $decryptedDate)) {
            
        } else {
          
            header("Location: index.php");
            exit();
        }

function decryptDate($encrypted, $key) {
    $data = base64_decode(str_replace(['-', '_'], ['+', '/'], $encrypted));
    $iv_length = openssl_cipher_iv_length('AES-256-CBC');
    $iv = substr($data, 0, $iv_length);
    $decrypted = openssl_decrypt(substr($data, $iv_length), 'AES-256-CBC', $key, 0, $iv);
    return $decrypted;
}
?>