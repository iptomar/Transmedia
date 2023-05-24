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
    //Get story id
    $storyID = $_POST['id'];

    //Verify if the end time is greater than the initial time 
    $initialTime = $_POST['initialtime'];
    $endTime = $_POST['endtime'];

    if($initialTime > $endTime){
        alert("The initial time has to be greather than the end time"); 
    }else if($endTime > $initialTime){
        alert("The end time has to be shorter than the initial"); 
    }

    //Verify if it exists a text in the story that already starts at that initial_time
    try{
        $stmt = $pdo->prepare('SELECT initial_time, end_time FROM text where id_story=?');
        $stmt->execute([$storyID]);
        $times = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($times as $time){
            echo "<p>".$time['initial_time']."</p>";
        }
    }catch(e){  
        alert("Something went wrong");  
    }
    //Upload text to the database 
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
                        <label for="initialtime">Initial Time:</label>
                        <input class="form-control" type="number" id="initialtime" name="initialtime" required />
                    </div>
        
                    <div class="form-group">
                        <label for="endtime">End Time:</label>
                        <input class="form-control" type="number" id="endtime" name="endtime" required />
                    </div>


                    <button type="submit" name="add_text" class="w-100 btn btn-primary" style="margin-top: 10px">Add Text</button>
                </form>
            </div>
        </div>
    </div>

</body>

</html>