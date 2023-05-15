<?php
require "config/connectdb.php";
include "./functions/useful.php";

$story = $pdo->prepare('SELECT story.name,story.description,story.author FROM story WHERE story.id = ?');
$video = $pdo->prepare('SELECT video.link,video.storyId,video.videoType,video.storyOrder,video.duration FROM video WHERE video.storyId = ? ORDER BY video.storyOrder');
$audio = $pdo->prepare('SELECT audio.id,audio.id_story,audio.audio,audio.storyOrder FROM audio WHERE audio.id_story = ? ORDER BY audio.storyOrder');
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

            <form method="post" onsubmit="onSwitch()">
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
                            echo '<video id="player' . $i . '" onplay="queueManager(this)" class ="player video-class embed-responsive-item" controls src="./files/story_' . $videoFetch[$i]["storyId"] . '/video/' . $videoFetch[$i]["link"] . '"></video>';
                        } elseif ($videoFetch[$i]["videoType"] == "text") {
                            echo '<iframe id="player' . $i . '" class ="player video-class embed-responsive-item" type="text/html" src="https://www.youtube.com/embed/' . $videoFetch[$i]["link"] . '?enablejsapi=1" allowfullscreen="true" allowscriptaccess="always"></iframe>'; //add iframe with src pointing to the video with this code
                        }
                        echo '</div>';
                    }
                    break;

                case "audio":

                    for ($i = 0; $i < count($audioFetch); $i++) {
                        echo '<audio class="player audio-class" onplay="audioQueueManager(this)" controls id="audio-player-' . $i . '" src="./files/story_' . $audioFetch[$i]["id_story"] . '/audio/' . $audioFetch[$i]["audio"] . '"></audio>';
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
    //all tagName="VIDEO" video players
    var videoPlayer = document.getElementsByTagName("video").length > 0 ? document.getElementsByTagName("video") : [];

    //all tagName="IFRAME" video players
    var youtubePlayer = document.getElementsByTagName("iframe").length > 0 ? document.getElementsByTagName("iframe") : [];

    //array with all the players
    var allPlayers = document.getElementsByClassName("player");

    //array with all the video players
    var allVideoPlayers = document.getElementsByClassName("video-class");

    //array with all the audio players
    var allAudioPlayers = document.getElementsByClassName("audio-class");

    //total story time
    var totalStoryElapsedTime = 0;

    //queue for player management
    var queue = [];

    //function to be called on <body> load
    function inic() {

        console.log(sessionStorage.getItem("totalStoryElapsedTime"));


        //variable to count the players with tag = "IFRAME"
        let count = -1;
        //iterate through all video players
        for (i = 0; i < allVideoPlayers.length; i++) {
            // if one happens to have tag = "IFRAME"
            if (allVideoPlayers[i].tagName == "IFRAME") {
                //increment count by 1
                count++;
                //call the method to prepare the YouTube API for this element
                onYouTubeIframeAPIReady("player" + i, count);
            }
        }

        playWithElapsedTime();
    }

    //function that retrieves the actual elapsed story time
    function getTotalElapsedStoryTime() {
        //var with time
        var cumulativeTime = 0;
        //get the actual media player
        var actualPlayer = queue.pop();
        //put the media player back in the queue
        queue.push(actualPlayer);
        //actions to take if the actual player is
        //of a tag=<video> video or tag=<audio>
        if (actualPlayer.tagName == "VIDEO" || actualPlayer.tagName == "AUDIO") {
            //get the story order of the actual video
            var actualPlayerIndex = Array.prototype.slice.call(allPlayers).indexOf(actualPlayer);
            cumulativeTime += getPlayerCurrentTime(actualPlayer);
        } else {
            //get the story order of the actual video
            var actualPlayerIndex = Array.prototype.slice.call(allPlayers).indexOf(actualPlayer.g);
            cumulativeTime += getYTPlayerCurrentTime(actualPlayer);
        }

        //the actions for the previous videos are equivalent
        //except it was used the total duration funtion for 
        //each video type
        for (i = actualPlayerIndex - 1; i >= 0; i--) {
            if (allPlayers[i].tagName == "VIDEO" || allPlayers[i].tagName == "AUDIO") {
                cumulativeTime += getPlayerDuration(allPlayers[i]);
            } else {
                for (j = 0; j < player.length; j++) {
                    if (player[j].g == allPlayers[i]) {
                        cumulativeTime += getYTPlayerDuration(player[j]);
                        break;
                    }
                }
            }
        }

        console.log(cumulativeTime);
        return cumulativeTime;
    }

    //function to call when the buttons form is submited
    function onSwitch() {

        //set the session variable with the elapsed story time
        sessionStorage.setItem("totalStoryElapsedTime", getTotalElapsedStoryTime());

        //empty the queue on switching
        queue.length = 0;
    }

    //function to play the current player
    //acording to the elapsed story time
    function playWithElapsedTime() {
        //store total elapsed time in variable
        //(this variable is to later store The
        //current time of the current player)
        var actualPlayerTime = sessionStorage.getItem("totalStoryElapsedTime");

        //varible to store actual player
        var actualPlayer;

        //variable to store YouTube player
        var ytPlayer;

        for (i = 0; i < allPlayers.length; i++) {
            if (allPlayers[i].tagName == "VIDEO" || allPlayers[i].tagName == "AUDIO") {
                actualPlayerTime -= getPlayerDuration(allPlayers[i]);
                if (getPlayerDuration(allPlayers[i]) > actualPlayerTime) {
                    actualPlayer = allPlayers[i];
                    break;
                }
            } else if (allPlayers[i].tagName == "IFRAME") {
                for (j = 0; j < player.length; j++) {
                    if (player[j].g == allPlayers[i]) {
                        ytPlayer = player[j];
                        break;
                    }
                }
                actualPlayerTime -= getYTPlayerDuration(ytPlayer);
                if (getYTPlayerDuration(ytPlayer) > actualPlayerTime) {
                    actualPlayer = ytPlayer;
                    break;
                }
            }
        }

        if (actualPlayer.tagName == "VIDEO" || actualPlayer.tagName == "AUDIO"){
            actualPlayer.currentTime = actualPlayerTime;
            actualPlayer.play();
        }else{
            actualPlayer.seekTo(actualPlayerTime, false);
            actualPlayer.playVideo();
        }
    }

    //return player (tag <video> or <audio>) time mark position
    function getPlayerCurrentTime(player) {
        return Math.round(player.currentTime);
    }

    //return player (tag <video> or <audio>) duration
    function getPlayerDuration(player) {
        return Math.round(player.duration);
    }

    //function to manage the queue for video media type
    function queueManager(videoPlayer) {
        //only push into queue when the video player
        //doesn't exist alerady in the queue
        if (!queue.includes(videoPlayer)) {
            queue.push(videoPlayer);
        }

        //if there is more than one video playing
        if (queue.length > 1) {
            //retrieve last video
            lastVideo = queue.shift();
            //actions to take to stop
            //if lastVideo has tag name "VIDEO"
            if (lastVideo.tagName == "VIDEO") {
                //console.log(lastVideo)
                lastVideo.pause();
                lastVideo.currentTime = 0;
            } else {
                //console.log(lastVideo)
                lastVideo.stopVideo();
            }
        }
        getTotalElapsedStoryTime();
    }

    //function to manage the queue for audio media type
    function audioQueueManager(audioPlayer) {
        if (!queue.includes(audioPlayer)) {
            queue.push(audioPlayer);
        }

        //if there is more than one audio playing
        if (queue.length > 1) {
            //retrieve last audio
            lastAudio = queue.shift();
            //actions to take to stop
            //if lastVideo has tag name "AUDIO"
            if (lastAudio.tagName == "AUDIO") {
                lastAudio.pause();
                lastAudio.currentTime = 0;
            } else {
                //yet to be implemented
            }
        }
        getTotalElapsedStoryTime();
    }

    //This code loads the IFrame Player API code asynchronously.
    var tag = document.createElement('script');

    tag.src = "https://www.youtube.com/iframe_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    //array to store all YouTube players
    var player = [];

    //This function creates an <iframe> (and YouTube player)
    //after the API code downloads.
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

    //The API will call this function when the video player is ready.
    function onPlayerReady() {
        console.log("Player ready");
    }

    //The API calls this function when the player's state changes.
    function onPlayerStateChange(event) {
        if (event.target.getPlayerState() == YT.PlayerState.PLAYING) {
            queueManager(event.target);
        }
    }

    //function to get time mark position of an YouTube video
    function getYTPlayerCurrentTime(YTPlayer) {
        return Math.round(YTPlayer.getCurrentTime());
    }

    //function to get duration of an YouTube video
    function getYTPlayerDuration(YTPlayer) {
        return Math.round(YTPlayer.getDuration());
    }
</script>

</html>