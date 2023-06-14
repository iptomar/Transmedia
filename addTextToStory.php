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


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_text'])) {

    
    //Get story id, duration, text, author
    $storyID = $_POST['id'];
    $duration = $_POST['duration'];
    $text = $_POST['text'];
    $author = $_SESSION['user'];


    if($duration < 0){
        alert("The duration has to be greater than 0"); 
    }

    
    //Upload text to the database
    try {
        $sql = "INSERT into text(id_story,text,duration,author,storyOrder) 
            SELECT ?,?,?,?,coalesce(MAX(storyorder),0)+ 1 FROM text WHERE id_story = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$storyID, $text, $duration, $author, $storyID]);
        //reload_page();
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
                    <input type="hidden" id="storyID" name="id" value="<?= isset($_GET['id']) ?  $_GET['id'] : "" ?>" />
                    <div id="previewimage"></div>

                    <div class="form-group">
                        <input type="text" class="form-control" id="text" name="text" placeholder="Insert your text..." required>
                    </div>
                    <div class="form-group">
                        <label for="duration">Duration:</label>
                        <input class="form-control" type="number" id="duration" name="duration" required />
                    </div>

                    <button type="submit" name="add_text" class="w-100 btn btn-primary" style="margin-top: 10px">Add Text</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>