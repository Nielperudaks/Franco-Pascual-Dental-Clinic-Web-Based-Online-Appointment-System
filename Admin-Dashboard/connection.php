<?php
    $servername = "localhost";
    $username = "rm2bgfu7h06d";
    $password = "oNQv1M!ejV0P";
    $dbName = "capstone_db";
    $conn = new mysqli(hostname: $servername, username: $username, password:$password, database:$dbName);
    if ($conn->connect_error) {
        die("Connectiona failed". $conn->connect_error);
    }
    return $conn;

