<?php
  
session_start();
 
// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; text-align: center; }
    </style>
</head>
<body>
    <h1 class="my-5">Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to the COVID Portal site.</h1>
    <div id = "user details">
        <h5 class = "my-5"> User Details </h5>
        <h6 class = "my-5"> Name: <?php 
            
            $cipher = 'AES-128-CBC';
	        $key = 'thebestsecretkey';
            $iv_hex = $_SESSION["iv_hex"];
            $iv = hex2bin($iv_hex);
            $fullName = hex2bin($_SESSION["fullName"]);
            $unencrypted_fullName = openssl_decrypt($fullName, $cipher, $key, OPENSSL_RAW_DATA, $iv);
            
            echo htmlspecialchars($unencrypted_fullName); ?>
            
        </h6>
        <h6 class = "my-5"> Date Of Birth: <?php 
            
            $cipher = 'AES-128-CBC';
	        $key = 'thebestsecretkey';
            $iv_hex = $_SESSION["iv_hex"];
            $iv = hex2bin($iv_hex);
            $dateOfBirth = hex2bin($_SESSION["dateOfBirth"]);
            $unencrypted_dateOfBirth = openssl_decrypt($dateOfBirth, $cipher, $key, OPENSSL_RAW_DATA, $iv);
            
            echo htmlspecialchars($unencrypted_dateOfBirth); ?>
            
        </h6>    
        <h6 class = "my-5"> Address: <?php 
            
            $cipher = 'AES-128-CBC';
	        $key = 'thebestsecretkey';
            $iv_hex = $_SESSION["iv_hex"];
            $iv = hex2bin($iv_hex);
            $address = hex2bin($_SESSION["address"]);
            $unencrypted_address = openssl_decrypt($address, $cipher, $key, OPENSSL_RAW_DATA, $iv);
            
            echo htmlspecialchars($unencrypted_address); ?>
            
        </h6>
        <h6 class = "my-5"> Phone Number: <?php
            
            $cipher = 'AES-128-CBC';
	        $key = 'thebestsecretkey';
            $iv_hex = $_SESSION["iv_hex"];
            $iv = hex2bin($iv_hex);
            $phoneNumber = hex2bin($_SESSION["phoneNumber"]);
            $unencrypted_phoneNumber = openssl_decrypt($phoneNumber, $cipher, $key, OPENSSL_RAW_DATA, $iv);
            
            echo htmlspecialchars($unencrypted_phoneNumber); ?></h6>   
    </div>
    <br />
    <p>
        <a href="uploadContacts.php" class="btn btn-info ">Upload Contacts</a>
        <a href="uploadPage.php" class="btn btn-info ml-3">Upload Test Results</a>
        <a href="reset-password.php" class="btn btn-warning ml-3">Reset Your Password</a>
        <a href="logout.php" class="btn btn-danger ml-3">Sign Out of Your Account</a>
    </p>
</body>
</html>