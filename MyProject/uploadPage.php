<?php

    require_once "config.php";

    $cipher = 'AES-128-CBC';
    $key = 'thebestsecretkey';

    if ($link->connect_error) {
      die('Connection failed: ' . $link->connect_error);
    }

    if (isset($_POST['delete-everything'])) {
      $sql = 'DROP TABLE images;';
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

    $sql = 'CREATE TABLE IF NOT EXISTS images (
            id int NOT NULL AUTO_INCREMENT,
            username varchar(255) NOT NULL,
            iv varchar(32) NOT NULL,
            img_file_name varchar(256),
            img_contents MEDIUMTEXT,
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
        <h1>File Uploads</h1>
        <?php
            if (isset($_POST['submit'])) {

              $image_contents = file_get_contents($_FILES['img']['tmp_name']);
              $img_name = $_FILES['img']['name'];

              $iv = random_bytes(16);

              $encrypted_img = openssl_encrypt($image_contents, $cipher, $key, OPENSSL_RAW_DATA, $iv);

              $iv_hex = bin2hex($iv);
              $img_hex = bin2hex($encrypted_img);

              $sql = "INSERT INTO images (username, iv, img_file_name, img_contents) VALUES ('$username','$iv_hex','$img_name','$img_hex')";
                
              if ($link->query($sql) === TRUE) {
                  
                echo '<p><i>New note added!</i></p>';
                  
              } else {
                  
                die('Error creating note: ' . $link->error);
                  
              }
                
            }
        ?>
        
        <h2>Create a New Note</h2>
        <div class="custom-file mb-3">
            <form method="post" enctype='multipart/form-data'>
              <div class="form-group">
                  <input type="file" class="custom-file-input" id="validatedCustomFile" name="img" accept="image/*">
                  <label class="custom-file-label" for="validatedCustomFile">Choose file...</label>
              </div>
              <button type="submit" class="btn btn-primary" name="submit">Submit</button>
            </form>
        </div>

        <h2>List Existing Notes</h2>

        <?php
        
            $sql = "SELECT id, username, iv, img_file_name, img_contents FROM images";
        
            $result = $link->query($sql);
            
        
            if ($result->num_rows > 0) {
            
                //if($username == $row['username']){
                    
                  echo '<table><tr><th>ID</th><th>Content</th><th>Image Name</th><th>Image</th></tr>';

                  while($row = $result->fetch_assoc()) {

                    $id = $row['id'];
                    $iv = hex2bin($row['iv']);

                    $image_name = $row['img_file_name'];
                    $image = ($row['img_contents']);

                    $unencrypted_image = openssl_decrypt(hex2bin($image), $cipher, $key, OPENSSL_RAW_DATA, $iv);

                    $display_unencrypted_image = '<img src="data:image/jpeg;base64,'.base64_encode( $unencrypted_image ).'"/>';

                    echo "<tr><td>$id</td><td>$image_name</td><td>$display_unencrypted_image</td></tr>";
                  }

                  echo '</table class = "table table-striped">';
                    
                //}
                
            } else {
                
              echo '<p>There are no images</p>';
                
            }
        
        ?>

        <h3>Clear Previous Results</h3>

        <form method="post">
          <button type="submit" name="delete-everything">Clear images</button>
        </form>
        <br />
        <p>
            <a href="welcome.php" class="btn btn-info">Dashboard</a>
            <a href="reset-password.php" class="btn btn-warning ml-3">Reset Your Password</a>
            <a href="logout.php" class="btn btn-danger ml-3">Sign Out of Your Account</a>
        </p>
    </body>
</html>
