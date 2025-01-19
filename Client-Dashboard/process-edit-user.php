<?php
session_start();
if (!isset($_SESSION["userID"]) || $_SESSION['valid'] != true) {
    header("Location: ../Patient_Login/index.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clientID = trim($_POST['clientID']);
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
   
    $address = trim($_POST['address']);
    $occupation = trim($_POST['occupation']);
   
    $number = trim($_POST['number']);
   
    $isFileChanged = filter_var($_POST['isFileChanged'], FILTER_VALIDATE_BOOLEAN);
    $file = isset($_FILES['file']) ? $_FILES['file'] : null;

    $conn = require __DIR__ . "../../connection.php";

    // Validate inputs
    function validate_input($input, $max_length, $input_name, $is_name = false) {
        // Check for empty or whitespace
        if (empty($input) || ctype_space($input)) {
            echo "$input_name cannot be empty or whitespace.";
            exit;
        }
    
        // Check length
        if (strlen($input) > $max_length) {
            echo "$input_name cannot be longer than $max_length characters.";
            exit;
        }
    
        // Additional validation for names (first name and last name)
        if ($is_name) {
            // Remove any whitespace
            $input = trim($input);
            
            // Check if input contains only letters and spaces
            if (!preg_match("/^[a-zA-Z\s-]*$/", $input)) {
                echo "$input_name can only contain letters, spaces, and hyphens.";
                exit;
            }
            
            // Check if input starts and ends with a letter
            if (!preg_match("/^[a-zA-Z].*[a-zA-Z]$/", $input)) {
                echo "$input_name must start and end with a letter.";
                exit;
            }
        }
    
        return $input;
    }

    $firstname = validate_input($firstName, 50, "First name", true);
    $lastname = validate_input($lastName, 50, "Last name", true);
    // $email = validate_input($_POST['email'], 100, "Email"); 
   
    $address = validate_input($address, 40, 'Address');
    $occupation = validate_input($occupation, 40, 'Occupation');
    

    
    if (!is_numeric($number) || strlen($number) > 11) {
        echo "Number must be a numeric value and not more than 11 characters.";
        exit;
    }
    
   

    // Validate file if changed and if file is provided
    if ($isFileChanged && $file) {
        if ($file['size'] > 0) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file['type'], $allowedTypes) || $file['size'] > 3 * 1024 * 1024) {
                echo "Invalid file. Only images are allowed and must be less than 3MB.";
                exit;
            }

            $imagePath = 'uploads/' . basename($file['name']);
            if (!move_uploaded_file($file['tmp_name'], $imagePath)) {
                echo "Failed to upload image.";
                exit;
            }
        }
    }

    

    // Build the update query
    $query = "UPDATE tbl_clients SET FirstName = ?, LastName = ?, Address = ?, Occupation = ?,  Number = ?";
    if ($isFileChanged && $file) {
        $query .= ", Image = ?";
    }
    $query .= " WHERE Client_ID = ?";
    
    // Prepare the statement
    if ($isFileChanged && $file) {
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssssssi', $firstName, $lastName,  $address, $occupation,  $number, $imagePath, $clientID);
    } else {
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sssssi', $firstName, $lastName,  $address, $occupation, $number, $clientID);
    }

    // Execute the statement
    if ($stmt->execute()) {
        echo "Profile updated successfully.";
    } else {
        echo "Failed to update profile.";
    }

    $stmt->close(); 
    $conn->close();
}
?>