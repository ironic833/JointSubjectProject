
<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = $confirm_password = $fullName = $dateOfBirth = $address = $phoneNumber = $GDPRCheck = "";
$username_err = $password_err = $confirm_password_err = $fullName_err = $dateOfBirth_err =$address_err = $phoneNumber_err = $GDPRCheck_err = "";
$cipher = 'AES-128-CBC';
$key = 'thebestsecretkey';

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        
        $username_err = "Please enter a username.";
        
    } else if(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        
        $username_err = "Username can only contain letters, numbers, and underscores.";
        
    } else {
        
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    
                    $username_err = "This username is already taken.";
                    
                } else{
                    
                    $username = trim($_POST["username"]);
                    
                }
                
            } else{
                
                echo "Oops! Something went wrong. Please try again later.";
                
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        
        $password_err = "Please enter a password.";   
        
    } elseif(strlen(trim($_POST["password"])) < 6){
        
        $password_err = "Password must have atleast 6 characters.";
        
    } else{
        
        $password = trim($_POST["password"]);
        
    }
    
    // Validate confirm password
    
    if(empty(trim($_POST["confirm_password"]))){
        
        $confirm_password_err = "Please confirm password.";  
        
    } else{
        
        $confirm_password = trim($_POST["confirm_password"]);
        
        if(empty($password_err) && ($password != $confirm_password)){
            
            $confirm_password_err = "Password did not match.";
            
        }
    }
    
    //validate fullanme entry
    if(empty(trim($_POST["fullName"]))){
        
        $fullName_err = "Please enter your full name.";
        
    } else if (ctype_alpha(str_replace(' ', '', $_POST["fullName"])) === false){
        
        $fullName_err = "Please input a name containing only A-Z characters.";
        
    } else {
        
        $fullName = trim($_POST["fullName"]);
        
    }
    
    //validate date of birth entry
    if(empty(trim($_POST["dateOfBirth"]))){
        
        $dateOfBirth_err = "Please enter a date of birth.";
        
    } else {
        
        $dateOfBirth = trim($_POST["dateOfBirth"]);
        
    }
    
    //validate Address entry
    if(empty(trim($_POST["address"]))){
        
        $address_err = "Please enter an address.";
        
    } else {
        
        $address = trim($_POST["address"]);
        
    }
    
    //validate phone number entry
    if(empty(trim($_POST["phoneNumber"]))){
        
        $phoneNumber_err = "Please enter a phone number.";
        
    } else {
        
        $phoneNumber = trim($_POST["phoneNumber"]);
        
    }
    
    if(empty($_POST["GDPRCheck"])){
        
        $GDPRCheck_err = "Please agree to GDPR terms";
        
    } 
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($dateOfBirth_err) && empty($address_err) && empty($phoneNumber_err)){
        
        $sql = "INSERT INTO users (iv_hex, username, password, fullName, dateOfBirth, address, phoneNumber) VALUES (?, ?, ?, ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            
             $iv = random_bytes(16);
            
             $escaped_fullName = $link -> real_escape_string($_POST['fullName']);
             $encrypted_fullName = openssl_encrypt($escaped_fullName, $cipher, $key, OPENSSL_RAW_DATA, $iv);
             $fullName_hex = bin2hex($encrypted_fullName);
            
             $escaped_dateOfBirth = $link -> real_escape_string($_POST['dateOfBirth']);
             $encrypted_dateOfBirth = openssl_encrypt($escaped_dateOfBirth, $cipher, $key, OPENSSL_RAW_DATA, $iv);
             $dateOfBirth_hex = bin2hex($encrypted_dateOfBirth);
            
             $escaped_address = $link -> real_escape_string($_POST['address']);
             $encrypted_address = openssl_encrypt($escaped_address, $cipher, $key, OPENSSL_RAW_DATA, $iv);
             $address_hex = bin2hex($encrypted_address);
            
             $escaped_phoneNumber = $link -> real_escape_string($_POST['phoneNumber']);
             $encrypted_phoneNumber = openssl_encrypt($escaped_phoneNumber, $cipher, $key, OPENSSL_RAW_DATA, $iv);
             $phoneNumber_hex = bin2hex($encrypted_phoneNumber);
            
             $iv_hex = bin2hex($iv);
            
            mysqli_stmt_bind_param($stmt, "sssssss", $param_iv_hex, $param_username, $param_password, $param_fullName, $param_dateOfBirth, $param_address, $param_phoneNumber);
            
            // Set parameters
            $param_iv_hex = $iv_hex;
            
            $param_username = $username;
            
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            $param_fullName = $fullName_hex;
            
            $param_dateOfBirth = $dateOfBirth_hex;
            
            $param_address = $address_hex;
            
            $param_phoneNumber = $phoneNumber_hex;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                
                // Redirect to login page
                header("location: login.php");
                
            } else{
                
                //mysql error line
                echo "Oops! Something went wrong. Please try again later.";
                
            }
            

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { 
            font: 14px sans-serif; 
        }
        .wrapper { 
            width: 360px; padding: 20px; 
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Sign Up to COVID Portal</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            
            <!--username field-->
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>    
            
            <!--password field-->
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            
            <!--confirm password field-->
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
            </div>
            
            <!--full name field-->
            <div class="form-group">
                <label>Enter Your Full Name</label>
                <input type="text" name="fullName" class="form-control <?php echo (!empty($fullName_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $fullName; ?>">
                <span class="invalid-feedback"><?php echo $fullName_err; ?></span>
            </div>
            
            
            <!--date of birth field-->
            <div class="form-group">
                <label>Enter date of birth</label>
                <input type="date" name="dateOfBirth" class="form-control <?php echo (!empty($dateOfBirth_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $dateOfBirth; ?>" placeholder="dd/mm/yyyy">
                <span class="invalid-feedback"><?php echo $dateOfBirth_err; ?></span>
            </div>
            
            <!--address field-->
            <div class="form-group">
                <label>Enter Address</label>
                <input type="text" name="address" class="form-control <?php echo (!empty($address_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $address; ?>" >
                <span class="invalid-feedback"><?php echo $address_err; ?></span>
            </div>
            
            <!--phone number field-->
            <div class="form-group">
                <label>Enter Phone Number</label>
                <input type="tel" name="phoneNumber" class="form-control <?php echo (!empty($phoneNumber_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $phoneNumber; ?>"  placeholder="0xx-xxx-xxxx" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}">
                <span class="invalid-feedback"><?php echo $phoneNumber_err; ?></span>
            </div>
            
            <div class="form-group form-check">
                <input type="checkbox" name="GDPRChceck" class="form-check-input <?php echo (!empty($GDPRCheck_err)) ? 'is-invalid' : ''; ?>" id="exampleCheck1">
                <label class="form-check-label" for="exampleCheck1">I consent to my data being used in compliance with GDPR</label>
                <span class="invalid-feedback"><?php echo $GDPRCheck_err; ?></span>
            </div>
            
            <!--submit button-->
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-secondary ml-2" value="Reset">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>    
</body>
</html>