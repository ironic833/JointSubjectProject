<?php

    require_once "config.php";

    $cipher = 'AES-128-CBC';
    $key = 'thebestsecretkey';

    if ($link->connect_error) {
      die('Connection failed: ' . $link->connect_error);
    }

    if (isset($_POST['delete-everything'])) {
      $sql = 'DROP TABLE contacts;';
      if (!$link->query($sql) === TRUE) {
        die('Error dropping database: ' . $link->error);
      }
    }

    $sql = 'CREATE DATABASE IF NOT EXISTS demo;';
    if (!$link->query($sql) === TRUE) {
      die('Error creating database: ' . $link->error);
    }

    $sql = 'USE demo;';
    if (!$link->query($sql) === TRUE) {
      die('Error using database: ' . $link->error);
    }

    $sql = 'CREATE TABLE IF NOT EXISTS contacts (
            id int NOT NULL AUTO_INCREMENT,
            username varchar(255) NOT NULL,
            iv varchar(32) NOT NULL,
            name varchar(256),
            email varchar(256),
            PRIMARY KEY (id));';

    if (!$link->query($sql) === TRUE) {
      die('Error creating table: ' . $link->error);
    }

    session_start();
 
    // Check if the user is logged in, if not then redirect him to login page
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: login.php");
        exit;
    }

    $username = $_SESSION["username"];
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>File Uploads</title> 
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <style>
            body{ font: 14px sans-serif; text-align: center; }
        </style>
    </head>
    <body>
        <h1 class = "my-5">Contact Tracing For <?php echo htmlspecialchars($_SESSION["username"]); ?></h1>
        <?php
            if (isset($_POST['submit'])) {
                
             $iv = random_bytes(16);
             $iv_hex = bin2hex($iv);
                
             $escaped_name = $link -> real_escape_string($_POST['name']);
             $encrypted_name = openssl_encrypt($escaped_name, $cipher, $key, OPENSSL_RAW_DATA, $iv);
             $name_hex = bin2hex($encrypted_name);
                
             $escaped_email = $link -> real_escape_string($_POST['email']);
             $encrypted_email = openssl_encrypt($escaped_email, $cipher, $key, OPENSSL_RAW_DATA, $iv);
             $email_hex = bin2hex($encrypted_email);

              $sql = "INSERT INTO contacts (username, iv, name, email) VALUES ('$username','$iv_hex','$name_hex','$email_hex')";
                
              if ($link->query($sql) === TRUE ) {
                  
                echo '<p><i>New contact added!</i></p>';
                  
              } else {
                  
                die('Error adding contact: ' . $link->error);
                  
              }
                
            }
        ?>
        
        <h6 class = "my-5">Add Contact</h6>
        <div class="custom-file mb-3">
            <form>
              <div class="form-group">
                <label for="exampleInputName">Name</label>
                <input type="text" name ="name" class="form-control" id="exampleInputName" aria-describedby="nameHelp">
              </div>
              <div class="form-group">
                 <label for="exampleInputEmail1">Email address</label>
                 <input type="email" name= "email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                 <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
              </div>
              <button type="submit" name="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
        <div>
            <div>
                <h6 class = "my-5"  style="padding-top: 50px;">Previously Added Contacts</h6>
            </div>
            <div style="padding-left: 33%;">
                <?php

                    $sql = "SELECT id, username, iv, name, email FROM contacts";

                    $result = $link->query($sql);


                    if ($result->num_rows > 0) {

                          echo '<table><tr><th>ID</th><th>Content</th><th>Name</th><th>Email</th></tr>';
                        
                          while($row = $result->fetch_assoc()) {
                              
                              if($_SESSION["username"] == $row['username']){
                                  
                                $id = $row['id'];
                                $iv = hex2bin($row['iv']);
                                  
                                $name = $row['name'];
                                $unencrypted_name = openssl_decrypt(hex2bin($name), $cipher, $key, OPENSSL_RAW_DATA, $iv);
                                  
                                $email = $row['email'];
                                $unencrypted_email = openssl_decrypt(hex2bin($email), $cipher, $key, OPENSSL_RAW_DATA, $iv);

                                echo "<tr><td>$id</td><td>$unencrypted_name</td><td>$unencrypted_email</td></tr>";
                                  
                              } else {
                                  
                                 echo '<p>There are no contacts</p>'; 
                                  
                              }
                          }

                          echo '</table>';


                    } else {

                      echo '<p>There are no contacts</p>';

                    }

                ?>
            </div>
        </div>

        <h6 class = "my-5" style="padding-top: 50px;">Clear Listed Contacts</h6>

        <form method="post" >
          <button type="submit" name="delete-everything" class="btn btn-primary">Clear Contacts</button>
        </form>
        <br />
        <p style="padding-top: 50px;">
            <a href="welcome.php" class="btn btn-info">Dashboard</a>
            <a href="uploadPage.php" class="btn btn-info ml-3">Upload Test Results</a>
            <a href="reset-password.php" class="btn btn-warning ml-3">Reset Your Password</a>
            <a href="logout.php" class="btn btn-danger ml-3">Sign Out of Your Account</a>
        </p>
    </body>
</html>
