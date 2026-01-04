<?php
// Include class autoloader
require_once __DIR__ . '/../../classes/autoload.php';

// Use Auth class to logout
Auth::logout();

header("Location: /HOTEL-MANAGEMENT-SYSTEM/index.php");
exit();
?>
