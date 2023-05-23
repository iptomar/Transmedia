<?php
require_once "verify_login.php";
require_once "Navbar.php";
require_once "config/connectdb.php";
include_once "./functions/useful.php";
//If id of story is not set
if (!isset($_GET['id']) || $_GET['id'] == null) {
    message_redirect("ERROR: Something went wrong", "my_stories.php");
    exit();
}

$stmt = $pdo->prepare('SELECT id,name, author FROM story where author=?');
$stmt->execute([$_SESSION['user']]);
$stories = $stmt->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_image'])) {
    //Get story id
    $storyID = $_POST['id'];

    //Get video duration from hidden input
    if (isset($_POST["duration"]) && $_POST["duration"] > 0) {
        $duration =  $_POST["duration"];
    } else {
        //If the duration is not set show error message
        alert("ERROR occurred when getting the duration");
        reload_page();
    }
    //Get image name
    $image_name = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];

    $mimeType = mime_content_type($_FILES['image']['tmp_name']);
    $fileType = explode('/', $mimeType)[0];
    if (!$fileType == "image") {
        alert("ERROR file is not a valid image");
        reload_page();
    }        
    //Generate a new name for the image
    $image = generate_file_name("image_", "image");
    if (!save_file("./files/story_$storyID/image/", $image, "image")) {
        alert("ERROR Saving the image");
        reload_page();
    }

    try {
        $sql = "INSERT into image(storyID,image,duration,storyOrder) 
            SELECT ?,?,?,coalesce(MAX(storyOrder),0)+ 1 FROM image WHERE storyID = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$storyID, $image, $duration, $storyID]);
        reload_page();
    } catch (Exception $e) {
        echo '<script>alert("ERROR occured while connecting to the database")</script>';
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./style/add_video_to_story.css">
    <title>Add text to story</title>
</head>

<body>
    <div class="container-sm mt-3">
        <div class="card">
            <div class="card-header text-center">Add Text</div>
            <div class="card-body">
                <form method="post" id="form-image" enctype="multipart/form-data">
                    <div id="previewimage"></div>

                    <div class="form-group">
                        <input type="text" class="form-control" id="text" name="text" placeholder="Insert your text..." required>
                    </div>
                    <div class="form-group">
                        <label for="initialtime">Initial Time:</label>
                        <input class="form-control" type="number" id="initialtime" name="duration" required />
                    </div>
        
                    <div class="form-group">
                        <label for="endtime">End Time:</label>
                        <input class="form-control" type="number" id="endtime" name="duration" required />
                    </div>


                    <button type="submit" name="add_text" class="w-100 btn btn-primary" style="margin-top: 10px">Add Text</button>
                </form>
            </div>
        </div>
    </div>

</body>

</html>