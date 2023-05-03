<?php
require "config/connectdb.php";
require "verify_login.php";
include "NavBar.php";

$stmt = $pdo->prepare('SELECT id,name FROM story where author = ?');
$stmt->execute([$_SESSION['user']]);
$stories = $stmt->fetchAll(PDO::FETCH_DEFAULT);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>My Stories</title>
    <link rel="stylesheet" href="./style/my_stories.css">

</head>

<body>
    <div class="stories">
        <div class="row m-3">
            <?php foreach ($stories as $story) : ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                    <div class="story_container text-center">
                        <a class="text-reset text-decoration-none w-100" href="selectedStoryPage.php?id=<?= $story['id'] ?>">
                            <img src="100x100_logo.png" class="img-fluid img-thumbnail w-100" />
                            <p class="w-100 p-1 pl-2 pr-2">
                                <?php echo $story['name'] ?>
                            </p>
                        </a>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    include "footer.php";
    ?>
</body>

</html>