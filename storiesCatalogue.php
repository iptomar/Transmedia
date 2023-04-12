<?php

require "config/connectdb.php";

$stmt = $pdo->prepare('SELECT `name` FROM story');
$stmt->execute();
$stories = $stmt->fetchAll(PDO::FETCH_COLUMN);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="./style/stories_catalogue.css">
    <title>Stories Catalogue</title>
</head>

<body>
    <div class="d-inline-block" style="max-width: 940px">
        <?php for ($i = 0; $i < count($stories); $i++) {

        ?>
            <div class="d-inline-block text-truncate" style="max-width: 300px; margin: 5px;">
                <a href="index.php">
                    <img src="100x100_logo.png" class="img-fluid img-thumbnail" style="max-width: 300px;" />
                </a>
                <br>
                <span class="d-inline-block text-truncate" style="max-width: 300px; ">
                    <?php print_r($stories[$i]) ?>
                </span>
            </div>

        <?php

        }

        ?>
    </div>
</body>

</html>