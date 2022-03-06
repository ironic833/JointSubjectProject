<?php

    require_once "config.php";

    $cipher = 'AES-128-CBC';
    $key = 'thebestsecretkey';

    session_start();
 
    // Check if the user is logged in, if not then redirect to login page
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: login.php");
        exit;
    }

    $username = $_SESSION["username"];

    if ($link->connect_error) {
      die('Connection failed: ' . $link->connect_error);
    }

    //needs to be adjusted with an if statement to loop and only delete entries where the username matches the username on the entry in database
    /*
    if (isset($_POST['delete-everything'])) {
        
      $username = $_POST['username'];    
        
      $sql = 'DELETE FROM images WHERE username = '$username'';
      if (!$link->query($sql) === TRUE) {
        die('Error dropping entry: ' . $link->error);
      }
    }
    */

    $sql = 'CREATE DATABASE IF NOT EXISTS demo;';
    if (!$link->query($sql) === TRUE) {
      die('Error creating database: ' . $link->error);
    }

    $sql = 'USE demo;';
    if (!$link->query($sql) === TRUE) {
      die('Error using database: ' . $link->error);
    }

    $sql = 'CREATE TABLE IF NOT EXISTS images (
            id int NOT NULL AUTO_INCREMENT,
            username varchar(255) NOT NULL,
            iv varchar(32) NOT NULL,
            img_file_name varchar(256) NOT NULL,
            img_contents MEDIUMTEXT NOT NULL,
            PRIMARY KEY (id));';

    if (!$link->query($sql) === TRUE) {
      die('Error creating table: ' . $link->error);
    }

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
        <h1 class = "my-5">File Uploads For <?php echo htmlspecialchars($_SESSION["username"]); ?></h1>
        <?php
            if (isset($_POST['submit'])) {

              $image_contents = file_get_contents($_FILES['img']['tmp_name']);
              $img_name = $_FILES['img']['name'];

              $iv = random_bytes(16);

              $encrypted_img = openssl_encrypt($image_contents, $cipher, $key, OPENSSL_RAW_DATA, $iv);

              $iv_hex = bin2hex($iv);
              $img_hex = bin2hex($encrypted_img);

              $sql = "INSERT INTO images (username, iv, img_file_name, img_contents) VALUES ('$username','$iv_hex','$img_name','$img_hex')";
                
              if ($link->query($sql) === TRUE ) {
                  
                echo '<p><i>New test result added!</i></p>';
                  
              } else {
                  
                die('Error adding image: ' . $link->error);
                  
              }
                
            }
        ?>
        
        <h6 class = "my-5">Add Image</h6>
        <div class="custom-file mb-3">
            <form method="post" enctype='multipart/form-data'>
              <div class="form-group">
                  <input type="file" class="custom-file-input" id="validatedCustomFile" name="img" accept="image/*">
                  <label class="custom-file-label" for="validatedCustomFile">Choose file...</label>
              </div>
              <button type="submit" class="btn btn-primary" name="submit">Submit</button>
            </form>
        </div>
        <div>
            <div>
                <h6 class = "my-5"  style="padding-top: 50px;">Existing Images</h6>
            </div>
            <div style="padding-left: 33%;">
                <?php

                    $sql = "SELECT id, username, iv, img_file_name, img_contents FROM images";

                    $result = $link->query($sql);


                    if ($result->num_rows > 0) {

                          echo '<table><tr><th>ID</th><th>Content</th><th>Image Name</th><th>Image</th></tr>';
                        
                          while($row = $result->fetch_assoc()) {
                              
                              if($_SESSION["username"] == $row['username']){
                                  
                                $id = $row['id'];
                                $iv = hex2bin($row['iv']);

                                $image_name = $row['img_file_name'];
                                $image = ($row['img_contents']);

                                $unencrypted_image = openssl_decrypt(hex2bin($image), $cipher, $key, OPENSSL_RAW_DATA, $iv);

                                $display_unencrypted_image = '<img src="data:image/jpeg;base64,'.base64_encode( $unencrypted_image ).'"/>';

                                echo "<tr><td>$id</td><td>$image_name</td><td>$display_unencrypted_image</td></tr>";
                                  
                              } else {
                                  
                                 echo '<p>There are no images</p>'; 
                                  
                              }
                          }

                          echo '</table>';


                    } else {

                      echo '<p>There are no images</p>';

                    }

                ?>
            </div>
        </div>

        <!--

        <h6 class = "my-5" style="padding-top: 50px;">Clear Older Tests</h6>
        
        
        <form method="post" >
          <button type="submit" name="delete-everything" class="btn btn-primary">Clear images</button>
        </form>
        -->
        
        <br />
        <p style="padding-top: 50px;">
            <a href="welcome.php" class="btn btn-info">Dashboard</a>
            <a href="uploadContacts.php" class="btn btn-info ml-3">Upload Contacts</a>
            <a href="reset-password.php" class="btn btn-warning ml-3">Reset Your Password</a>
            <a href="logout.php" class="btn btn-danger ml-3">Sign Out of Your Account</a>
        </p>
    </body>
</html>
