<?php
session_start();
session_destroy();
header("Location: ../Patient_Login/index.php");
exit;

