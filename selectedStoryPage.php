<?php
require "config/connectdb.php";
include "./functions/useful.php";

$story = $pdo->prepare('SELECT story.name,story.description,story.author FROM story WHERE story.id = ?');
$video = $pdo->prepare('SELECT video.link,video.storyId,video.videoType,video.storyOrder,video.duration FROM video WHERE video.storyId = ?');
$audio = $pdo->prepare('SELECT audio.id,audio.id_story,audio.audio,audio.author FROM audio WHERE audio.id_story = ?');
$story->execute([$_GET['id']]);
$video->execute([$_GET['id']]);
$audio->execute([$_GET['id']]);
$storyFetch = $story->fetch(PDO::FETCH_ASSOC);
$videoFetch = $video->fetchAll(PDO::FETCH_ASSOC);
$audioFetch = $audio->fetchAll(PDO::FETCH_ASSOC);
$totalDuration = 0;

foreach ($videoFetch as $video) {
    $totalDuration += $video["duration"];
}

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

<body onload="inic()">
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

                if (isset($videoFetch) && !empty($videoFetch)) {
                    echo '<input class="bg-primary text-white media-button rounded border-0 m-2 p-2" type="submit" name="mediaOpt" value="video"/>';
                }
                if (isset($audioFetch) && !empty($audioFetch)) {
                    echo '<input class="bg-primary text-white media-button rounded border-0 m-2 p-2" type="submit" name="mediaOpt" value="audio"/>';
                }
                if (isset($imagesFetch) && !empty($imagesFetch)) {
                    echo '<input class="bg-primary text-white media-button rounded border-0 m-2 p-2" type="submit" name="mediaOpt" value="images"/>';
                }
                if (isset($textFetch) && !empty($textFetch)) {
                    echo '<input class="bg-primary text-white media-button rounded border-0 m-2 p-2" type="submit" name="mediaOpt" value="text"/>';
                }

                ?>


            </form>

        </div>

        <div id="mediaDiv">
            <?php
            //tratamento variavel sessao de opcao de meio
            if (isset($_POST["mediaOpt"])) {
                $_SESSION["mediaOpt"] = $_POST["mediaOpt"];
            } else {
                $_SESSION["mediaOpt"] = "video";
            }

            $mediaOpt = $_SESSION["mediaOpt"];
            switch ($mediaOpt) {

                case "video":

                    for ($i = 0; $i < count($videoFetch); $i++) {
                        echo '<div id="preview' . $i . '" class="video-preview embed-responsive col-md-4 offset-md-1 d-inline-block rounded" style="width:320px; height:180px";>';
                        if ($videoFetch[$i]["videoType"] == "file") {
                            echo '<video id="player' . $i . '" onplay="queueManager(this)" class ="embed-responsive-item" controls src="./files/story_' . $videoFetch[$i]["storyId"] . '/video/' . $videoFetch[$i]["link"] . '"></video>';
                        } elseif ($videoFetch[$i]["videoType"] == "text") {
                            echo '<iframe id="player' . $i . '" class ="embed-responsive-item" type="text/html" src="https://www.youtube.com/embed/' . $videoFetch[$i]["link"] . '?enablejsapi=1" allowfullscreen="true" allowscriptaccess="always"></iframe>'; //add iframe with src pointing to the video with this code
                        }
                        echo '</div>';
                    }
                    break;

                case "audio":

                    for ($i = 0; $i < count($audioFetch); $i++) {
                        echo '<audio onplay="audioQueueManager(this)" controls id="audio-player-' . $i . '" src="./files/story_' . $audioFetch[$i]["id_story"] . '/audio/' . $audioFetch[$i]["audio"] . '"></audio>';
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
        </div>
</body>

<script>
    //var 
    var videoPlayer = document.getElementsByTagName("video").length > 0 ? document.getElementsByTagName("video") : [];
    var youtubePlayer = document.getElementsByTagName("iframe").length > 0 ? document.getElementsByTagName("iframe") : [];
    //audioPlayer = document.getElementsByTagName("audio").length > 0 ? document.getElementsByTagName("audio") : [];

    var allPlayers = document.getElementsByClassName("embed-responsive-item");

    //var currentMedia = ">";
    var currentTime = 0;

    var queue = [];

    var videosBehind = [];

    function inic() {

        //console.log(queue.toString());

        let count = 0;
        for (i = 0; i < allPlayers.length; i++) {
            if (allPlayers[i].tagName == "IFRAME") {
                count++;
                onYouTubeIframeAPIReady("player" + i, count);
            }
        }

        console.log(allPlayers.length == videoPlayer.length + youtubePlayer.length);
    }

    function onSwitch() {
        let currentVideo = queue.pop();
        queue.length = 0;
        //incorporate total elapsed story time here
    }

    function getPlayerTime(player) {
        return round(player.currentTime);
    }

    function queueManager(videoPlayer) {
        if (!queue.includes(videoPlayer)) {
            queue.push(videoPlayer);
        }

        if (queue.length > 1) {
            lastVideo = queue.shift();
            if (lastVideo.tagName == "VIDEO") {
                lastVideo.pause();
                lastVideo.currentTime = 0;
            } else {
                console.log(lastVideo)
                stopYTVideo(lastVideo);
                if (!videoPlayer.tagName == "VIDEO") {
                    startYTVideo(videoPlayer);
                }
            }
        }
    }

    function audioQueueManager(audioPlayer) {
        if (!queue.includes(audioPlayer)) {
            queue.push(audioPlayer);
        }

        if (queue.length > 1) {
            lastAudio = queue.shift();
            if (lastAudio.tagName == "AUDIO") {
                lastAudio.pause();
                lastAudio.currentTime = 0;
            } else {
                //yet to be implemented
            }
        }
    }

    // 2. This code loads the IFrame Player API code asynchronously.
    var tag = document.createElement('script');

    tag.src = "https://www.youtube.com/iframe_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    // 3. This function creates an <iframe> (and YouTube player)
    //    after the API code downloads.
    var player = [];

    function onYouTubeIframeAPIReady(id, i) {
        player[i] = new YT.Player("" + id, {
            playerVars: {
                'autoplay': 0,
                'controls': 1
            },
            events: {
                'onReady': onPlayerReady,
                'onStateChange': onPlayerStateChange
            }
        });
    }

    // 4. The API will call this function when the video player is ready.
    function onPlayerReady() {
        console.log("Player ready");
    }

    // 5. The API calls this function when the player's state changes.
    //    The function indicates that when playing a video (state=1),
    //    the player should play for six seconds and then stop.

    function onPlayerStateChange(event) {
        if (event.target.getPlayerState() == YT.PlayerState.PLAYING) {
            queueManager(event);
        }
    }

    function stopYTVideo(event) {
        //console.log(event);
        event.target.stopVideo();
    }

    function startYTVideo(event) {
        event.target.startVideo();
    }

    function playYTVideo(event) {
        event.target.playVideo();
    }

    function pauseYTVideo(event) {
        event.target.pauseVideo();
    }

    function getYTPlayerTime(event){
        return Math.round(event.target.getCurrentTime());
    }
</script>

</html>