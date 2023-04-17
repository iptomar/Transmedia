<?php
require "config/connectdb.php";

$storyName = $pdo->prepare('SELECT `name` FROM story WHERE `name`= ?')
$storyDescription = $pdo->prepare('SELECT `description` FROM story WHERE `description` = ?')
$storyAuthor = $pdo->prepare('SELECT `author` FROM story WHERE `author` = ?')
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Story Selected</title>
</head>
<body>

    <div class="Name">
        <label for="name">Name</label>
        <?php print($storyName)?>
    </div>

    <div class="description">
        <label for="description">Description</label>
        <?php print($storyDescription)?>
    </div>

    <div class="author">
        <label for="author">Author</label>
        <?php print($storyAuthor)?>
    </div>

</body>
</html>