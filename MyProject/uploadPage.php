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

//when form is submitted we do this
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
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
    <h1 class="my-5">Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Upload Your Covid Test.</h1>
    <div id = "user details">
        <h6 class = "my-6"> Select Image File to Upload: </h6>
        <br />
        <div class="custom-file mb-3">
            <input type="file" class="custom-file-input" id="validatedCustomFile" required>
            <label class="custom-file-label" for="validatedCustomFile">Choose file...</label>
            <br />
            <br />
            <input type="submit" class="btn btn-primary" value="Submit">
            <br />
            <br />
        </div>
    </div>
    <br />
    <p>
        <a href="welcome.php" class="btn btn-info">Dashboard</a>
        <a href="reset-password.php" class="btn btn-warning ml-3">Reset Your Password</a>
        <a href="logout.php" class="btn btn-danger ml-3">Sign Out of Your Account</a>
    </p>
</body>
</html>