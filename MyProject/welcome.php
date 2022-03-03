<?php
// Initialize the session

/*

Scenario 1

Upload an antigen test with a photo.

Our national health service has decided to create a portal that will allow citizens to report a positive antigen test for COVID-19 and to list their close contacts online. 

Citizens who have symptoms of the virus or are a close contact of a confirmed case can use store-bought test kits and upload any positive results to the portal. 

The system will require citizens to create an account with a username and password, to provide personal information including full name, address, date of birth and phone number, and to upload an image of a positive antigen test. They can also provide a list of close contacts, including their full names and phone numbers.

*/
    
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
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
        <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
        <a href="logout.php" class="btn btn-danger ml-3">Sign Out of Your Account</a>
    </p>
</body>
</html>