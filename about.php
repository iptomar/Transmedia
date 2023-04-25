<?php
require "config/connectdb.php";

/*Example of how to execute a SELECT with PDO
$stmt = $pdo->query('SELECT id, title FROM story');
$stories = $stmt->fetchAll(PDO::FETCH_ASSOC);*/

/*Generate password hash with salt
echo '<p>Joana = '.password_hash("joana123", PASSWORD_DEFAULT).'</p>'; 
echo '<p>Tiago = '.password_hash("tiago123", PASSWORD_DEFAULT).'</p>';*/


?>
<!DOCTYPE html>
<html>
  <head>
    <title>Transmedia - Share Your Stories</title>
  </head>
  <body>
    <h1>Welcome to Transmedia</h1>
    <p>Transmedia is a platform where you can share your stories and videos about anything.</p>
    <?php
        include "footer.php";
    ?>
  </body>
</html>

