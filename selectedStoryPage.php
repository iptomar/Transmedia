<?php
require "config/connectdb.php";
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
    <link href="./style/selected_story_page.css" type="text/css">
    <title>Story Selected</title>
</head>

<body>
    <div class="mb-3">
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
                if ($storyFetch['description'] == null) {
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

        <div class="change-media">

            <form method="post">
                <?php 

                    if(isset($videoFetch) && !empty($videoFetch)){
                        echo '<input class="bg-primary text-white media-button rounded border-0 m-2 p-2" type="submit" name="mediaOpt" value="video"/>';
                    }
                    if(isset($audioFetch) && !empty($audioFetch)){
                        echo '<input class="bg-primary text-white media-button rounded border-0 m-2 p-2" type="submit" name="mediaOpt" value="audio"/>';
                    }
                    if(isset($imagesFetch) && !empty($imagesFetch)){
                        echo '<input class="bg-primary text-white media-button rounded border-0 m-2 p-2" type="submit" name="mediaOpt" value="images"/>';
                    }
                    if(isset($textFetch) && !empty($textFetch)){
                        echo '<input class="bg-primary text-white media-button rounded border-0 m-2 p-2" type="submit" name="mediaOpt" value="text"/>';
                    }
                
                ?>

                
            </form>

        </div>

        <?php
                    if(isset($_POST["mediaOpt"])){
                        $_SESSION["mediaOpt"] = $_POST["mediaOpt"];
                    }else{
                        $_SESSION["mediaOpt"] = "video";
                    }
                    $mediaOpt = $_SESSION["mediaOpt"];
                    switch($mediaOpt){

                        case "video":
                            
                            foreach ($videoFetch as $videos) {
                                echo '<div id="preview" class="embed-responsive col-md-4 offset-md-1 d-inline-block rounded" style="width:320px; height:180px";>';
                                if ($videos["videoType"] == "file") {
                                    echo '<video class ="embed-responsive-item" id="player" controls src="./files/story_' . $videos["storyId"] . '/video/' . $videos["link"] . '"></video>';
                                } elseif ($videos["videoType"] == "text") {
                                    echo '<iframe class ="embed-responsive-item" id="player" type="text/html" src="https://www.youtube.com/embed/' . $videos["link"] . '?enablejsapi=1"></iframe>'; //add iframe with src pointing to the video with this code
                                }
                                echo '</div>';
                            }
                                break;
                        case "audio":

                            foreach ($audioFetch as $audio) {
                                echo '<audio controls src="./files/story_' . $audio["id_story"] . '/audio/' . $audio["audio"] . '"></audio>';
                            }
                            break;
                        case "images":
                            echo "Yet To Be Implemented";
                            break;
                        case "text":
                            echo "Yet To Be Implemented";
                            break;

                    }

        ?>
</body>

</html>