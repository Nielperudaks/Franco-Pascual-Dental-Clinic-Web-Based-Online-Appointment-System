<?php
// Include your database connection logic
$conn = require __DIR__ . "../../connection.php";

$clientID = $_POST['clientID'];
$query = "SELECT Client_ID FROM tbl_clients WHERE Client_ID = ?";
$stmtCheck = $conn->prepare($query);
$stmtCheck->bind_param("i", $clientID);
$stmtCheck->execute();
$result = $stmtCheck->get_result();

if ($result->num_rows > 0) {
    // Build dynamic UPDATE query based on provided values
    $updateFields = [];
    $types = "";
    $values = [];

    // Array of all possible fields and their corresponding POST keys
    $fields = [
        'FirstName' => 'firstName',
        'LastName' => 'lastName',
        'MiddleName' => 'middleName',
        'Address' => 'address',
        'Occupation' => 'occupation',
        'Number' => 'phoneNumber',
        'Birthday' => 'birthday',
        'Sex' => 'sex',
        'Religion' => 'religion',
        'Nationality' => 'nationality',
        'OfficeAddress' => 'officeAddress',
        'DentalInsurance' => 'dentalInsurance',
        'PreviousDentist' => 'previousDentist',
        'LastVisit' => 'lastVisit',
        'PhysicianName' => 'physicianName',
        'Specialty' => 'specialty',
        'BloodType' => 'bloodType',
        'BloodPressure' => 'bloodPressure',
        'HealthStatus' => 'healthStatus',
        'MedicalStatus' => 'medicalStatus',
        'ConditionStatus' => 'conditionStatus',
        'ViceStatus' => 'viceStatus',
        'Allergies' => 'allergies',
        'illness' => 'illness'
    ];

    // Build the update query dynamically based on provided values
    foreach ($fields as $dbField => $postKey) {
        if (isset($_POST[$postKey]) && $_POST[$postKey] !== '') {
            $updateFields[] = "$dbField = ?";
            $types .= "s"; // Assuming all fields are strings
            $values[] = $_POST[$postKey];
        }
    }

    if (!empty($updateFields)) {
        // Add Client_ID to the parameter list
        $types .= "i";
        $values[] = $clientID;

        // Prepare and execute the update query
        $updateQuery = "UPDATE tbl_clients SET " . implode(", ", $updateFields) . " WHERE Client_ID = ?";
        $stmt = $conn->prepare($updateQuery);

        // Dynamic parameter binding
        $stmt->bind_param($types, ...$values);
        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            echo "No changes were made to the record.";
        } else {
            echo "Record updated successfully.";
        }
        $stmt->close();
    } else {
        echo "No fields to update.";
    }
} else {
    // Record does not exist, perform an insert
    $stmt = $conn->prepare("
        INSERT INTO tbl_clients (
            Client_ID, FirstName, LastName, MiddleName, Address, Occupation, Number,
            Birthday, Sex, Religion, Nationality, OfficeAddress, DentalInsurance,
            PreviousDentist, LastVisit, PhysicianName, Specialty, BloodType, 
            BloodPressure, HealthStatus, MedicalStatus, ConditionStatus,
            ViceStatus, Allergies, illness
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    // Bind parameters for the insert statement
    $stmt->bind_param(
        "isssssssssssssssssssssss",
        $_POST['clientID'],
        $_POST['firstName'],
        $_POST['lastName'],
        $_POST['middleName'],
        $_POST['address'],
        $_POST['occupation'],
        $_POST['phoneNumber'],
        $_POST['birthday'],
        $_POST['sex'],
        $_POST['religion'],
        $_POST['nationality'],
        $_POST['officeAddress'],
        $_POST['dentalInsurance'],
        $_POST['previousDentist'],
        $_POST['lastVisit'],
        $_POST['physicianName'],
        $_POST['specialty'],
        $_POST['bloodType'],
        $_POST['bloodPressure'],
        $_POST['healthStatus'],
        $_POST['medicalStatus'],
        $_POST['conditionStatus'],
        $_POST['viceStatus'],
        $_POST['allergies'],
        $_POST['illness']
    );

    // Execute the insert statement
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        echo "New record created successfully.";
    } else {
        echo "Error creating record.";
    }
    $stmt->close();
}


$stmt = $conn->prepare("
UPDATE tbl_transaction SET
Status = 'Done'

WHERE Transaction_Code = ?
");
$stmt->bind_param(
"s",
$_POST['transactionCode']
);

// Execute the statement
$stmt->execute();

// Clean up
$stmt->close();
$stmtCheck->close();
$conn->close();
?>