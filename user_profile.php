<?php
require "config/connectdb.php";
// Check if the 'user' parameter exists in the URL
if (isset($_GET['user'])) {
    $usernameRequest = $_GET['user'];
    // Retrieve user data from the database
    $query = "SELECT * FROM user WHERE username = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$usernameRequest]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    message_redirect("ERROR Username not provided", "index.php");
}
?>


<!DOCTYPE html>
<html>

<head>
    <title>User Profile</title>
    <style>
        .small-text {
            font-size: 12px;
        }

        .big-text {
            font-size: 24px;
        }

        .img-thumbnail {
            object-fit: contain;
            max-height: 100%;
        }
    </style>
</head>

<body>
    <?php
    include "NavBar.php";
    ?>
    <div class="container-sm mt-3 mb-5">
        <div class="card">
            <div class="card-header text-center">User Profile</div>
            <div class="card-body">
                <?php
                if ($user) {
                    echo '<span class="small-text">Username:</span><br>';
                    echo '<span class="big-text">' . $user['username'] . '</span><br>';
                    echo '<span class="small-text">Name:</span><br>';
                    echo '<span class="big-text">' . $user['name'] . '</span><br>';
                    echo '<span class="small-text">Email:</span><br>';
                    echo '<span class="big-text">' . $user['email'] . '</span><br>';

                    // Fetch the user's stories
                    $query = "SELECT * FROM story WHERE author = ?";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute([$user['username']]);
                    $stories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        echo '</div><div class="mt-2 mb-2 card-footer  text-center">User Stories</div>'; ?>
                        <div class="row m-3 card-body">
                            
                            <?php 
                            
                            if ($stories) {
                                
                                foreach ($stories as $story) : ?>
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                                    <div class="story_container text-center">
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
                    <?php endforeach;
                        } else {
                            echo '<p>No stories available for this user.</p> </div>  ';
                        }
                    } else {
                        echo "<p>User not found</p></div>";
                    }
                    ?>
                        </div>
            </div>
        </div>

        <?php
        include "footer.php";
        ?>
</body>

</html>