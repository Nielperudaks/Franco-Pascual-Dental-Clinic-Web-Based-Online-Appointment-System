<?php
    $servername = "127.0.0.1";
    $username = "root";
    $password = "Kaspersky052403";
    $dbName = "capstone_db";
    $conn = new mysqli(hostname: $servername, username: $username, password:$password, database:$dbName);
    if ($conn->connect_error) {
        die("Connectiona failed". $conn->connect_error);
    }
    return $conn;

