<?php
require "config/connectdb.php";

$stmt = $pdo->prepare('SELECT id,name FROM story');
$stmt->execute();
$stories = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <?php
    $currPage = 'index';
    include "NavBar.php";
    $index = 'index';
    ?>
    <div class="d-inline-block mb-5" style="max-width: 940px">
        <?php foreach ($stories as $story) : ?>
            <div class="d-inline-block text-truncate" style="max-width: 300px; margin: 5px;">
                <a class="text-reset text-decoration-none w-100" href="selectedStoryPage.php?id=<?= $story['id'] ?>">
                    <img src="<?php
                                $stmt = $pdo->prepare('SELECT image FROM image where storyID = ? ORDER BY storyOrder LIMIT 1');
                                $stmt->execute([$story['id']]);
                                $stmt->rowCount() > 0 ? $img = "./files/story_" . $story['id'] . "/image/" . $stmt->fetch()['image'] : $img = "default_image.png";
                                echo  $img; ?>" class="img-responsive img-fluid img-thumbnail w-100" style="height:250px" />
                </a>
                <br>
                <span class="d-inline-block text-truncate" style="max-width: 300px; ">
                    <?php print_r($story['name']) ?>
                </span>
            </div>

        <?php endforeach; ?>

    </div>
    <?php
    include "footer.php";
    ?>
</body>

</html>