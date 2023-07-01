<?php
require "config/connectdb.php";
require "verify_login.php";
include "NavBar.php";

$stmt = $pdo->prepare('SELECT id,name FROM story where author = ? ORDER BY name');
$stmt->execute([$_SESSION['user']]);
$stories = $stmt->fetchAll(PDO::FETCH_DEFAULT);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Stories</title>
    <style>
        .img-thumbnail {
            object-fit: contain;
            max-height: 100%;
            max-width: 100%;
        }

        .card-body {
            padding: 10px 20px !important;
        }
    </style>
</head>

<body>
    <div class="stories m-2 mr-5 ml-5 mt-3">
        <div class="row m-3">
            <?php
            foreach ($stories as $story) : ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                    <div class="card">
                        <div class="row w-100 m-0">
                            <div class="col-6 p-0">
                                <a class="btn btn-primary w-100" href="edit_story.php?id=<?= $story['id'] ?>">Edit</a>
                            </div>
                            <div class="col-6 p-0">
                                <a onclick="confirmDelete()" class="btn btn-danger w-100" href="delete_story.php?id=<?= $story['id'] ?>">Delete</a>
                            </div>
                        </div>
                        <a class="text-reset text-decoration-none" href="selectedStoryPage.php?id=<?= $story['id'] ?>">
                            <div style="height: 250px; display: flex; align-items: flex-end;">
                                <img src="<?php
                                            $stmt = $pdo->prepare('SELECT image FROM image where storyID = ? ORDER BY storyOrder LIMIT 1');
                                            $stmt->execute([$story['id']]);
                                            $stmt->rowCount() > 0 ? $img = "./files/story_" . $story['id'] . "/image/" . $stmt->fetch()['image'] : $img = "default_image.png";
                                            echo $img; ?>" class="w-100 img-thumbnail" alt="Story Image" style="max-height: 100%;">
                            </div>
                            <div class="card-body">
                            <h6 class="card-title m-0"><?php echo $story['name'] ?></h6>
                            </div>
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