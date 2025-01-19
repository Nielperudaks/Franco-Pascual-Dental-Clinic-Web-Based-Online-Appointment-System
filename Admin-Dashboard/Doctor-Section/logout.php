<?php
session_start();
session_destroy();
header("Location: /Login-Registration/login-register.php");
exit;

