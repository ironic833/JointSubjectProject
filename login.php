<?php
include_once 'conn.php'; 
    $ciphering = "AES-128-CTR";
    $iv_length = openssl_cipher_iv_length($ciphering);
    $options   = 0;
    $encryption_iv = '1234567891011121';
    $encryption_key = "crypto";
    $hashedUsername = openssl_encrypt($_POST['Username'], $ciphering, $encryption_key, $options, $encryption_iv);
    $hashedPassword = openssl_encrypt($_POST['Password'], $ciphering, $encryption_key, $options, $encryption_iv);
    
    $sql = 'SELECT username,password FROM users';
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()) 
    { 
        if($hashedUsername == $row['username'] && $hashedPassword == $row['password'])
        {
            echo "correct details entered";
        }
        else
        {
            echo "incorrect details please try again";
        }
    }
    mysqli_close($conn);
?>