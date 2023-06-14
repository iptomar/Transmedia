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
                        <div class="row w-100 m-0">
                            <div class="col-6 p-0">
                                <a class="btn btn-primary w-100" href="edit_story.php?id=<?= $story['id'] ?>">Edit</a>
                            </div>
                            <div class="col-6 p-0">
                                <a onclick="confirmDelete()" class="btn btn-danger w-100" href="delete_story.php?id=<?= $story['id'] ?>">Delete</a>
                            </div>
                        </div>
                        <a class="text-reset text-decoration-none w-100" href="selectedStoryPage.php?id=<?= $story['id'] ?>">
                            <img src="<?php
                                        $stmt = $pdo->prepare('SELECT image FROM image where storyID = ? ORDER BY storyOrder LIMIT 1');
                                        $stmt->execute([$story['id']]);
                                        $stmt->rowCount() > 0 ? $img = "./files/story_" . $story['id'] . "/image/" . $stmt->fetch()['image'] : $img = "default_image.png";
                                        echo  $img; ?>" class="img-responsive img-fluid img-thumbnail w-100" style="height:250px" />
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
<script>
    // Function prompts the user to confirm the delete before submitting the form
    function confirmDelete() {
        const confirmed = confirm('Are you sure you want to delete this?');
        if (!confirmed) {
            event.preventDefault(); // prevent the form from submitting if the user doesn't confirm
        }
    }
</script>

</html>