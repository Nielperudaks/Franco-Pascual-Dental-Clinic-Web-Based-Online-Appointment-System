<?php
session_start();

// Check if session user ID is not set
if (!isset($_SESSION["userID"])) {
    header("Location: ../Login-Registration/");
    exit();
}

$conn = require __DIR__ . "../../../connection.php";

$query = "SELECT * FROM tbl_admin WHERE Admin_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION["userID"]);
$stmt->execute();
$result = $stmt->get_result();
$validateUser = $result->fetch_assoc();

// Check if user is not found or doesn't have correct access level
if (!$validateUser || $validateUser['Access_Level'] != 1) {
    header("Location: ../Login-Registration/");
    exit();
}

    require __DIR__ . "/../../vendor/autoload.php";
    use Dompdf\Dompdf;
use Dompdf\Options;

//$treatment['treatmentCost'] = ;



// Get POST data
$patientData = json_decode($_POST['patientData'], true);
$treatmentData = json_decode($_POST['treatmentData'], true);

// Create new PDF instance
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);

$dompdf = new Dompdf($options);

// Generate HTML content
$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            max-width: 100px;
            margin-bottom: 10px;
        }
        .clinic-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .clinic-info {
            font-size: 14px;
            margin-bottom: 5px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            background-color: #f0f0f0;
            padding: 5px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        .info-item {
            margin-bottom: 5px;
        }
        .label {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="header">
        
        <div class="clinic-name">Franco Pascual: Dental Clinic</div>
        <div class="clinic-info">153 Levi Mariano St. Usuan Taguig City</div>
        <div class="clinic-info">Phone: (02) 642-0002 (02) 503-5281 | Email: FrancoPacual@fpdentalclinic.com</div>
    </div>

    <div class="section">
        <div class="section-title">Patient Information</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="label">Name:</span> 
                ' . $patientData['lastName'] . ', ' . $patientData['firstName'] . ' ' . $patientData['middleName'] . '
            </div>
            <div class="info-item">
                <span class="label">Birthday:</span> 
                ' . $patientData['birthday'] . '
            </div>
            <div class="info-item">
                <span class="label">Sex:</span> 
                ' . $patientData['sex'] . '
            </div>
            <div class="info-item">
                <span class="label">Religion:</span> 
                ' . $patientData['religion'] . '
            </div>
            <div class="info-item">
                <span class="label">Nationality:</span> 
                ' . $patientData['nationality'] . '
            </div>
            
            <div class="info-item">
                <span class="label">Office Address:</span> 
                ' . $patientData['officeAddress'] . '
            </div>
            <div class="info-item">
                <span class="label">Dental insurance:</span> 
                ' . $patientData['insurance'] . '
            </div>
            <div class="info-item">
                <span class="label">Occupation:</span> 
                ' . $patientData['occupation'] . '
            </div>
           
            
            
        </div>
    </div>

    <div class="section">
        <div class="section-title">Contact Information</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="label">Home Address:</span> 
                ' . $patientData['homeAddress'] . '
            </div>
            <div class="info-item">
                <span class="label">Phone:</span> 
                ' . $patientData['phoneNumber'] . '
            </div>
            <div class="info-item">
                <span class="label">Email:</span> 
                ' . $patientData['email'] . '
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Dental History</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="label">Previous Dentist:</span> 
                ' . $patientData['previousDentist'] . '
            </div>
           <div class="info-item">
                <span class="label">last Visit:</span> 
                ' . $patientData['lastVisit'] . '
            </div>
            
        </div>
    </div>

    <div class="section">
        <div class="section-title">Medical History</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="label">Physician:</span> 
                ' . $patientData['physicianName'] . '
            </div>
            <div class="info-item">
                <span class="label">Blood Pressure:</span> 
                ' . $patientData['bloodPressure'] . '
            </div>
            <div class="info-item">
                <span class="label">Blood Type:</span> 
                ' . $patientData['bloodType'] . '
            </div>
            
             <div class="info-item">
                <span class="label">Are you in Good Health?</span> 
                ' . $patientData['healthStatus'] . '
            </div>
            <div class="info-item">
                <span class="label">Are you in Medical Treatment right now?</span> 
                ' . $patientData['treatmentStatus'] . '
            </div>
            <div class="info-item">
                <span class="label">Ever has serious Illness or Surgical Operation?</span> 
                ' . $patientData['surgicalStatus'] . '
            </div>
            <div class="info-item">
                <span class="label">Do you smoke, drink alcohol or take any dangerous drugs?</span> 
                ' . $patientData['viceStatus'] . '
            </div>
        </div>
        
        <div style="margin-top: 10px;">
            <span class="label">Allergies:</span> 
            ' . implode(", ", $patientData['allergies']) . '
        </div>
        
        <div style="margin-top: 10px;">
            <span class="label">Medical Conditions:</span> 
            ' . implode(", ", $patientData['illnesses']) . '
        </div>
    </div>

    <div class="section">
        <div class="section-title">Treatment History</div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Treatment</th>
                    <th>Doctor</th>
                    <th>Cost</th>
                </tr>
            </thead>
            <tbody>';

// Add treatment rows
foreach ($treatmentData as $treatment) {
    $html .= '
        <tr>
            <td>' . $treatment['treatmentDate'] . '</td>
            <td>' . $treatment['treatmentName'] . '</td>
            <td>' . $treatment['doctor'] . '</td>
            <td>' .$treatment['treatmentCost'] . '</td>
        </tr>';
}

$html .= '
            </tbody>
        </table>
    </div>
</body>
</html>';

// Load HTML content
$dompdf->loadHtml($html);

// Set paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render PDF
$dompdf->render();

// Stream PDF to browser
$dompdf->stream("Medical_Record.pdf", ["Attachment" => 0]);
?>