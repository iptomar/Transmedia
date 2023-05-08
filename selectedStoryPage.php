<?php
require "config/connectdb.php";
require "verify_login.php";
include "./functions/useful.php";

$story = $pdo->prepare('SELECT story.name,story.description,story.author FROM story WHERE story.id = ?');
$video = $pdo->prepare('SELECT video.link,video.storyId,video.videoType,video.storyOrder FROM video WHERE video.storyId = ?');
$audio = $pdo->prepare('SELECT audio.id,audio.id_story,audio.audio,audio.author FROM audio WHERE audio.id_story = ?');
$story->execute([$_GET['id']]);
$video->execute([$_GET['id']]);
$audio->execute([$_GET['id']]);
$storyFetch = $story->fetch(PDO::FETCH_ASSOC);
$videoFetch = $video->fetchAll(PDO::FETCH_ASSOC);
$audioFetch = $audio->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Story Selected</title>
</head>

<body>
    <?php
    include "NavBar.php";
    ?>
    <div class="Name">
        <label for="name" style="font-size:20px; font-weight: bold;">Name</label>
        <p>
            <?php
            print($storyFetch['name'])
            ?>
        </p>
    </div>

    <div class="description">
        <label for="description" style="font-size:20px; font-weight: bold;">Description</label>
        <p>
            <?php
            if (print($storyFetch['description'] == null)) {
                print("no description");
            } else {
                print($storyFetch['description']);
            }
            ?>
        </p>
    </div>

    <div class="author">
        <label for="author" style="font-size:20px; font-weight: bold;">Author</label>
        <p>
            <?php
            print($storyFetch['author'])
            ?>
        </p>
    </div>
        <?php
        foreach($videoFetch as $videos){
            echo '<div id="preview" class="embed-responsive col-md-4 offset-md-1 d-inline-block rounded" style="width:320px; height:180px";>';
            if ($videos["videoType"] == "file") {
                echo '<video class ="embed-responsive-item" id="player" controls src="./files/story_'. $videos["storyId"]. '/video/' . $videos["link"] . '"></video>';
            } elseif ($videos["videoType"] == "text") {
                echo '<iframe class ="embed-responsive-item" id="player" type="text/html" src="https://www.youtube.com/embed/' . $videos["link"] . '?enablejsapi=1"></iframe>'; //add iframe with src pointing to the video with this code
            }
            echo '</div>';
        }
        ?>
        <?php
        foreach($audioFetch as $audio){
                echo '<audio controls src="./files/story_'. $audio["id_story"]. '/audio/' . $audio["audio"] . '"></audio>';
        }
        ?>
</body>

</html>