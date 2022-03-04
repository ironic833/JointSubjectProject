<?php
$host = 'localhost';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password);

$cipher = 'AES-128-CBC';
$key = 'thebestsecretkey';

if ($conn->connect_error) {
  die('Connection failed: ' . $conn->connect_error);
}

if (isset($_POST['delete-everything'])) {
  $sql = 'DROP DATABASE securenotepad;';
  if (!$conn->query($sql) === TRUE) {
    die('Error dropping database: ' . $conn->error);
  }
}

$sql = 'CREATE DATABASE IF NOT EXISTS securenotepad;';
if (!$conn->query($sql) === TRUE) {
  die('Error creating database: ' . $conn->error);
}

$sql = 'USE securenotepad;';
if (!$conn->query($sql) === TRUE) {
  die('Error using database: ' . $conn->error);
}

$sql = 'CREATE TABLE IF NOT EXISTS notes (
id int NOT NULL AUTO_INCREMENT,
iv varchar(32) NOT NULL,
content varchar(256) NOT NULL,
img_file_name varchar(256),
img_contents MEDIUMTEXT,
PRIMARY KEY (id));';
if (!$conn->query($sql) === TRUE) {
  die('Error creating table: ' . $conn->error);
}
?>
<html>
<head>
<title>Secure Notepad</title> </head>
<body>
<h1>Secure Notepad</h1>
<?php
if (isset($_POST['new-note'])) {
  //echo '<p>filename: '.$_FILES['img']['name'].'</p>';
  //echo '<p>temp_name: '.$_FILES['img']['tmp_name'].'</p>';
  //echo '<p>image_contents: '.$image_contents.'</p>';
  //$image_contents_hex = bin2hex($image_contents);

  $image_contents = file_get_contents($_FILES['img']['tmp_name']);
  $img_name = $_FILES['img']['name'];

  $iv = random_bytes(16);

  // encrypt comment and image
  $escaped_content = $conn -> real_escape_string($_POST['content']); // only real_escape for user input

  $encrypted_content = openssl_encrypt($escaped_content, $cipher, $key, OPENSSL_RAW_DATA, $iv);
  $encrypted_img = openssl_encrypt($image_contents, $cipher, $key, OPENSSL_RAW_DATA, $iv);

  $iv_hex = bin2hex($iv);
  $content_hex = bin2hex($encrypted_content);
  $img_hex = bin2hex($encrypted_img);

  $sql = "INSERT INTO notes (iv, content,img_file_name,img_contents) VALUES ('$iv_hex', '$content_hex','$img_name','$img_hex')";
  if ($conn->query($sql) === TRUE) {
    echo '<p><i>New note added!</i></p>';
  } else {
    die('Error creating note: ' . $conn->error);
  }
}
?>
<h2>Create a New Note</h2>

<form method="post" enctype='multipart/form-data'>
  <input type="text" id="content" name="content" size="64"><br><br>
  <input type="file" id="img" name="img" accept="image/*">
  <button type="submit" name="new-note">Create Note</button>
</form>

<h2>List Existing Notes</h2>

<?php
$sql = "SELECT id, iv, content, img_file_name, img_contents FROM notes";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  echo '<table><tr><th>ID</th><th>Content</th><th>Image Name</th><th>Image</th></tr>';
  while($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $iv = hex2bin($row['iv']);
    $content = hex2bin($row['content']);
    $image_name = $row['img_file_name'];
    $image = ($row['img_contents']);

    $unencrypted_content = openssl_decrypt($content, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    $unencrypted_image = openssl_decrypt(hex2bin($image), $cipher, $key, OPENSSL_RAW_DATA, $iv);
    //$unencrypted_image_bin = hex2bin($unencrypted_image);

    //'<img src="data:image/jpeg;base64,'.base64_encode( $result['image'] ).'"/>'
    // https://stackoverflow.com/questions/23842268/how-to-display-image-from-database-using-php
    $display_unencrypted_image = '<img src="data:image/jpeg;base64,'.base64_encode( $unencrypted_image ).'"/>';

    echo "<tr><td>$id</td><td>$unencrypted_content</td><td>$image_name</td><td>$display_unencrypted_image</td></tr>";
  }
  echo '</table>';
} else {
  echo '<p>There are no notes!</p>';
}
?>

<h3>Delete Everything</h3>

<form method="post">
  <button type="submit" name="delete-everything">Delete Everything!</button>
</form>
</body>
</html>
