<?php
require "verify_login.php";
include "./NavBar.php";
include "./functions/useful.php";

$stmt = $pdo->prepare('SELECT id,name, author FROM story where author=?');
$stmt->execute([$_SESSION['user']]);
$stories = $stmt->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //Get story id
    $storyId = $_POST['story'];

    $stmt = $pdo->prepare('SELECT author FROM story where id=?');
    $stmt->execute([$storyId]);
    $author = $stmt->fetch();
    $author = $author['author'];
    //Get audio name
    $audio_name = $_FILES['my_audio']['name'];
    $tmp_name = $_FILES['my_audio']['tmp_name'];

    //Get the audio extension
    $audio_ex = pathinfo($audio_name, PATHINFO_EXTENSION);
    //Convert the audio to loweer case to be able to compare with the allowed extensions
    $audio_ex = strtolower($audio_ex);
    //Allowed Extensions
    $allowed_extensions = array("mp3", 'wav');

    if (in_array($audio_ex, $allowed_extensions)) {
        //Save the audi o with a new name
         $audio = generate_file_name("audio_", "my_audio");
         if (!save_file("./files/story_$storyId/audio/", $audio, "my_audio")) {
            echo '<p>Error while saving the audio</p>';
         }


        try {
            $sql = "INSERT into audio(id_story,audio,author) VALUES(?, ?, ?);";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$storyId, $audio, $author]);
        } catch (Exception $e) {
            echo '<script>alert("ERROR occured while connecting to the database")</script>';
        }
    }else {
        echo '<p>Invalid extension please select another file (.mp3 or .wav)</p>'; 
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
    <title>Add Audio to story</title>
</head>

<body>
    <div class="container-sm mt-3">
        <div class="card">
            <div class="card-header text-center">Add Audio</div>
            <div class="card-body">
                <form method="post" id="form-audio"  enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="story">Choose Story:</label>
                        <select required name="story" class="form-select mb-3 w-100" aria-label=".form-select-lg example">
                            <option value="">Select the story</option>
                            <?php
                            foreach ($stories as $story) :
                                echo '<option value="' . $story['id'] . '">' . $story['name'] . '</option>';
                            endforeach;
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="audioFile">Example file input</label>
                        <input type="file" class="form-control-file" id="audioFile" name="my_audio">
                    </div>
        
                    <button type="submit" name="submit" class="btn btn-primary">Add Audio</button>
                </form>
            </div>
        </div>
    </div>
    <?php
    include "footer.php";
    ?>
</body>
</html>