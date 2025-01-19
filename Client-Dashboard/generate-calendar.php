<?php
if(isset($_SESSION["userID"])){
    $conn = require __DIR__."/connection.php";
    $query = "SELECT * FROM tbl_clients WHERE Client_ID = {$_SESSION["userID"]}";
    $result = $conn->query($query);
    $validateUser = $result->fetch_assoc(); 
    }
if(isset($validateUser)){
    function build_calendar($month, $year) {
        $conn = require __DIR__."../../connection.php";
        date_default_timezone_set('Asia/Manila');
        // Get non-working days from tbl_details
        $nonWorkingDaysQuery = "SELECT NonWorkingDays FROM tbl_details";
        $nonWorkingDaysResult = $conn->query($nonWorkingDaysQuery);
        $nonWorkingDays = array();
        if ($nonWorkingDaysResult->num_rows > 0) {
            while ($row = $nonWorkingDaysResult->fetch_assoc()) {
                $nonWorkingDays[] = $row['NonWorkingDays'];
            }
        }
        $waiting = "Waiting Approval";
        $waiting2 = "Approved";
        $stmt = $conn->prepare("SELECT * FROM tbl_transaction WHERE MONTH(AppointmentDate) = ? AND YEAR(AppointmentDate) = ? AND Client_ID=? AND (Status = ? OR Status= ?)");
        $stmt->bind_param('ssiss', $month, $year, $_SESSION["userID"], $waiting, $waiting2);
        $bookings = array();
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $bookings[] = $row['AppointmentDate'];
                }   
                $stmt->close();
            }
        }
        $query = "SELECT * FROM tbl_clients WHERE Client_ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i",$_SESSION["userID"]);
        $status;
        if($stmt->execute()){
            $result = $stmt->get_result();
            $status = $result->fetch_assoc();
        }

    
        $daysOfWeek = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
        $firstDayOfMonth = mktime(0,0,0,$month,1,$year);
        $numberDays = date('t',$firstDayOfMonth);
        $dateComponents = getdate($firstDayOfMonth);
        $monthName = $dateComponents['month'];
        $dayOfWeek = $dateComponents['wday'];
    
        $datetoday = date('Y-m-d');
    
        $calendar = "<table class='table table-bordered calendar-table'>";
    $calendar .= "<center class='mb-3' ><h2 class='mt-3' style='font-size:50px; font-weight:100'>$monthName $year</h2>";
    $calendar .= "<a class='btn btn-xs me-md-3 me-1 fs-6 fs-md-5' href='?month=" . date('m', mktime(0, 0, 0, $month - 1, 1, $year)) . "&year=" . date('Y', mktime(0, 0, 0, $month - 1, 1, $year)) . "'><i class='align-middle me-2 me-md-3' data-feather='chevron-left'></i><span class='d-none d-sm-inline'>Previous</span></a> ";

    $calendar .= "<a class='btn btn-xs btn-outline-secondary mx-1 mx-md-2 fs-6 fs-md-5' href='?month=" . date('m') . "&year=" . date('Y') . "'>Current Month</a> ";
    $calendar .= "<a class='btn btn-xs ms-md-3 ms-1 fs-6 fs-md-5' href='?month=" . date('m', mktime(0, 0, 0, $month + 1, 1, $year)) . "&year=" . date('Y', mktime(0, 0, 0, $month + 1, 1, $year)) . "'><span class='d-none d-sm-inline'>Next</span><i class='align-middle ms-2 ms-md-3' data-feather='chevron-right'></i></a><br>";

        $calendar .= "<tr>";
        foreach ($daysOfWeek as $day) {
            $calendar .= "<th class='header'>$day</th>";
        }
    
        $currentDay = 1;
        $calendar .= "</tr><tr>";
    
        if ($dayOfWeek > 0) {
            for ($k=0; $k<$dayOfWeek; $k++){
                $calendar .= "<td class='empty'></td>";
            }
        }
    
        $month = str_pad($month, 2, "0", STR_PAD_LEFT);

        $lockedDates = [];
        for($i = 0 ;$i <=2; $i++){
            $lockedDates[] = date("Y-m-d", strtotime("+$i day"));
        }
    
        while ($currentDay <= $numberDays) {
            if ($dayOfWeek == 7) {
                $dayOfWeek = 0;
                $calendar .= "</tr><tr>";
            }                                                                                                                                                                                                                                                
    
            $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
            $date = "$year-$month-$currentDayRel";
            $dayname = strtolower(date('l', strtotime($date)));
            $eventNum = 0;
            $today = $date == date('Y-m-d') ? "today" : "";
           
            

            if($status["Status"]==null){
                if (in_array($dayname, ['sunday'])) {
                    $calendar .= "<td style='background-color: #E7EAED' data-bs-toggle='tooltip' data-bs-placement='top' title='Closed'><h2 style='font-size:35px; font-weight:100'>$currentDay</h2>  <p class=' me-1 my-2 fs-9'> <i class='align-middle me-3' data-feather='x'></i></p>";
                } 
                 elseif ($date <= date('Y-m-d') || in_array($date, $lockedDates)) {
                    $calendar .= "<td style='background-color: #F7F8F9' data-bs-toggle='tooltip' data-bs-placement='top' title='Locked'><h2 style='font-size:35px; font-weight:100'>$currentDay</h2> <p  class=' me-1 my-2 fs-9' ><i class='align-middle me-3 ' data-feather='lock'></i></p>";
                } else {
                    $key = "diznuts";
                    $encryptedDate=encryptDate($date, $key);
                    $calendar .= "<td class='$today' style='background-color: #F7F8F9' data-bs-toggle='tooltip' data-bs-placement='top' title='Available'><h2 style='font-size:35px; font-weight:100'>$currentDay</h2>  <a href='book-appointment?date=".$encryptedDate."' class='badge me-1 my-2 fs-9' style='background-color:  #959CA3'> <i class='align-middle me-2' data-feather='check'></i></a>";
                }
            }else{
                if (in_array($dayname, ['sunday'])) {
                    $calendar .= "<td style='background-color: #E7EAED' data-bs-toggle='tooltip' data-bs-placement='top' title='Closed'><h2 style='font-size:35px; font-weight:100'>$currentDay</h2>  <p class=' me-1 my-2 fs-9'> <span class='align-middle me-3' data-feather='x'></span></p>";
                } elseif (in_array($date, $bookings)) {
                    $calendar .= "<td class='$today' style='background-color: #E7EAED' data-bs-toggle='tooltip' data-bs-placement='top' title='Already Booked'><h2 style='font-size:35px; font-weight:100'>$currentDay</h2>  <p class=' me-1 my-2 fs-9 text-info'  > <span class='align-middle me-2 text-info' data-feather='flag'></span></p>";
                } elseif ($date <= date('Y-m-d') || in_array($date,$lockedDates)) {
                    $calendar .= "<td style='background-color: #F7F8F9' data-bs-toggle='tooltip' data-bs-placement='top' title='Locked'><h2 style='font-size:35px; font-weight:100'>$currentDay</h2> <p  class=' me-1 my-2 fs-9' ><span class='align-middle me-3 ' data-feather='lock'></span></p>";
                } else {                   
                    $calendar .= "<td class='$today' style='background-color: #F7F8F9' data-bs-toggle='tooltip' data-bs-placement='top' title='Available (Disabled - already appointed)'><h2 style='font-size:35px; font-weight:100'>$currentDay</h2>  <p class='badge me-1 my-2 fs-9' style='background-color:  #959CA3'> <span class='align-middle me-2' data-feather='check'></span></p>";
                }
            }
    
            $calendar .="</td>";
            $currentDay++;
            $dayOfWeek++;
        }
    
        if ($dayOfWeek != 7) {
            $remainingDays = 7 - $dayOfWeek;
            for ($l=0; $l<$remainingDays; $l++){
                $calendar .= "<td class='empty'></td>";
            }
        }
        
        $calendar .= "</tr>";
        $calendar .= "</table>";
    
        echo $calendar;
    }
    function encryptDate($date, $key) {
        $iv_length = openssl_cipher_iv_length('AES-256-CBC');
    $iv = openssl_random_pseudo_bytes($iv_length);
    $encrypted = openssl_encrypt($date, 'AES-256-CBC', $key, 0, $iv);
    // Use URL-safe base64 encoding
    return str_replace(['+', '/'], ['-', '_'], base64_encode($iv . $encrypted));
    }
    
}
    