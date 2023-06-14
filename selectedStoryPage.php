<?php
require "config/connectdb.php";
include "./functions/useful.php";

$story = $pdo->prepare('SELECT story.name,story.description,story.author, story.id FROM story WHERE story.id = ?');
$video = $pdo->prepare('SELECT video.link,video.storyId,video.videoType,video.storyOrder,video.duration FROM video WHERE video.storyId = ? ORDER BY video.storyOrder');
$audio = $pdo->prepare('SELECT audio.id,audio.id_story,audio.audio,audio.storyOrder,duration FROM audio WHERE audio.id_story = ? ORDER BY audio.storyOrder');
$image = $pdo->prepare('SELECT id,storyID,image,duration,storyOrder FROM image WHERE storyID = ? ORDER BY storyOrder');

$story->execute([$_GET['id']]);
$video->execute([$_GET['id']]);
$audio->execute([$_GET['id']]);
$image->execute([$_GET['id']]);
$storyFetch = $story->fetch(PDO::FETCH_ASSOC);
$videoFetch = $video->fetchAll(PDO::FETCH_ASSOC);
$audioFetch = $audio->fetchAll(PDO::FETCH_ASSOC);
$imagesFetch = $image->fetchAll(PDO::FETCH_ASSOC);

$totaltimeVideo = array_sum(array_column($videoFetch, 'duration'));
$totaltimeAudio = array_sum(array_column($audioFetch, 'duration'));
$totaltimeImage = array_sum(array_column($imagesFetch, 'duration'));
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
        <div class="container card mt-5">
            <div class="card-body text-center">
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
                            echo '<input style="display: none; cursor: pointer;" id="videobtn" class="bg-primary text-white media-button rounded border-0 m-2 p-2" type="submit" name="mediaOpt" value="video"/>';
                        }
                        if (isset($audioFetch) && !empty($audioFetch)) {
                            echo '<input style="display: none; cursor: pointer;" id="audiobtn" class="bg-primary text-white media-button rounded border-0 m-2 p-2" type="submit" name="mediaOpt" value="audio"/>';
                        }
                        if (isset($imagesFetch) && !empty($imagesFetch)) {
                            echo '<input style="display: none; cursor: pointer;" id="imagebtn" class="bg-primary text-white media-button rounded border-0 m-2 p-2" type="submit" name="mediaOpt" value="images"/>';
                        }
                        if (isset($textFetch) && !empty($textFetch)) {
                            echo '<input style="display: none; cursor: pointer;" id="textbtn" class="bg-primary text-white media-button rounded border-0 m-2 p-2" type="submit" name="mediaOpt" value="text"/>';
                        }

                        ?>


                    </form>

                </div>

                <div id="mediaDiv" class="mt-3">
                    <?php
                    //tratamento variavel sessao de opcao de meio
                    if (isset($_POST["mediaOpt"])) {
                        $_SESSION["mediaOpt"] = $_POST["mediaOpt"];
                    } else {
                        $_SESSION["mediaOpt"] = "video";
                    }

                    $mediaOpt = $_SESSION["mediaOpt"];

                    if (count($videoFetch) + count($audioFetch) + count($imagesFetch) == 0) {
                        echo "<p>Sem conteúdo para apresentar</p>";
                    } else {
                        switch ($mediaOpt) {

                            case "video":
                                for ($i = 0; $i < count($videoFetch); $i++) {
                                    echo '<div style="width:0px; height:0px; display: none;" id="preview' . $i . '" class="video-preview embed-responsive embed-responsive-16by9 d-inline-block rounded">';
                                    if ($videoFetch[$i]["videoType"] == "file") {
                                        echo '<video style="display: none;" id="player' . $i . '" onplay="queueManager(this)" onended="playAdjacentPlayer(\'right\')" class ="video-audio player video-class embed-responsive-item" controls src="./files/story_' . $videoFetch[$i]["storyId"] . '/video/' . $videoFetch[$i]["link"] . '"></video>';
                                    } elseif ($videoFetch[$i]["videoType"] == "text") {
                                        echo '<iframe style="display: none;" id="player' . $i . '" class ="player video-class embed-responsive-item" type="text/html" src="https://www.youtube.com/embed/' . $videoFetch[$i]["link"] . '?enablejsapi=1" allowfullscreen="true" allow="autoplay" allowscriptaccess="always"></iframe>'; //add iframe with src pointing to the video with this code
                                    }
                                    echo '</div>';
                                }
                                break;

                            case "audio":

                                for ($i = 0; $i < count($audioFetch); $i++) {
                                    echo '<div style="width:0px; height:0px; display: none;" id="preview' . $i . '" class="audio-preview">';
                                    echo '<audio style="display: none;" class=" video-audio player audio-class" onplay="queueManager(this)" onended="playAdjacentPlayer(\'right\')" controls id="audio-player-' . $i . '" src="./files/story_' . $audioFetch[$i]["id_story"] . '/audio/' . $audioFetch[$i]["audio"] . '"></audio>';
                                    echo '</div>';
                                }
                                break;
                            case "images":
                                foreach ($imagesFetch as $image) {
                                    echo '<div style="width:0px; height:0px; display: none;">';
                                    echo '<img style="display: none;" class="mb-3 image-class player" data-duration="' . $image['duration'] . '" id="img-' . $image['id'] . '" src="./files/story_' . $image["storyID"] . '/image/' . $image["image"] . '"></img>';
                                    echo '</div>';
                                }
                                break;
                            case "text":
                                echo "Yet To Be Implemented";
                                break;
                        }

                        echo <<<EOF
    
                        <div id="adjVidButDiv">
                            <button id="prevMediaButton" style="visibility: hidden; color: white; background-color: #007bff; border: 0px; height:30px; width: 25%; border-radius: 4px; cursor: pointer;" class="adj-button" onclick="playAdjacentPlayer('left')"><b>|<<</b></button>
                            <button id="nextMediaButton" style="visibility: hidden;color: white; background-color: #007bff; border: 0px; height:30px; width: 25%; border-radius: 4px; cursor: pointer;" class="adj-button" onclick="playAdjacentPlayer('right')"><b>>>|</b></button>
                        </div>
    
                        EOF;
                    }

                    ?>
                </div>
            </div>

        </div>
    </div>
</body>

<script>
    //array with all the players
    var allPlayers;
    var prevBtn;
    var nextBtn;
    var videoBtn;
    var audioBtn;
    var imgBtn;
    //total story time
    var totalStoryElapsedTime = 0;

    //queue for player management
    var queue = [];

    //function to be called on <body> load
    function inic() {

        allPlayers = document.getElementsByClassName("player");
        if (sessionStorage.getItem("storyId") != <?= $storyFetch['id'] ?>) {
            sessionStorage.setItem("totalStoryElapsedTime", 0);
            sessionStorage.setItem("storyId", <?= $storyFetch['id'] ?>)
        }

        videoBtn = document.getElementById('videobtn');
        audioBtn = document.getElementById('audiobtn');
        imgBtn = document.getElementById('imagebtn');
        prevBtn = document.getElementById("prevMediaButton");
        nextBtn = document.getElementById("nextMediaButton");

        //wait time in milliseconds
        var waitTimeMillis = allPlayers.length * 500;

        //variable to count the players with tag = "IFRAME"
        let count = -1;
        //iterate through all video players
        for (i = 0; i < allPlayers.length; i++) {
            // if one happens to have tag = "IFRAME"
            if (allPlayers[i].tagName == "IFRAME") {
                //increment count by 1
                count++;
                //call the method to prepare the YouTube API for this element
                onYouTubeIframeAPIReady("player" + i, count);

            }
        }

        playWithElapsedTime(waitTimeMillis);
    }

    const sleep = (ms) =>
        new Promise(resolve => setTimeout(resolve, ms));

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
        if (actualPlayer.tagName == "VIDEO" || actualPlayer.tagName == "AUDIO" || actualPlayer.tagName == "IMG") {
            //get the story order of the actual video or audio
            var actualPlayerIndex = Array.prototype.slice.call(allPlayers).indexOf(actualPlayer);
            cumulativeTime += getPlayerCurrentTime(actualPlayer);
        } else {
            //get the story order of the actual YouTube video
            var actualPlayerIndex = Array.prototype.slice.call(allPlayers).indexOf(actualPlayer.g);
            cumulativeTime += getYTPlayerCurrentTime(actualPlayer);
        }

        //the actions for the previous videos are equivalent
        //except it was used the total duration funtion for 
        //each video type
        for (i = actualPlayerIndex - 1; i >= 0; i--) {
            if (allPlayers[i].tagName == "VIDEO" || allPlayers[i].tagName == "AUDIO" || actualPlayer.tagName == "IMG") {
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

        if (videoBtn != null) {
            if (cumulativeTime >= <?= $totaltimeVideo ?>) {
                videoBtn.style.display = "none";
            } else {
                videoBtn.style.display = "inline-block";
            }
        }
        if (audioBtn != null) {
            if (cumulativeTime >= <?= $totaltimeAudio ?>) {
                audioBtn.style.display = "none";
            } else {
                audioBtn.style.display = "inline-block";
            }
        }
        if (imgBtn != null) {
            if (cumulativeTime >= <?= $totaltimeImage ?>) {
                imgBtn.style.display = "none";
            } else {
                imgBtn.style.display = "inline-block";
            }
        }
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
    async function playWithElapsedTime(waitTimeMillis) {
        await sleep(waitTimeMillis);

        //store total elapsed time in variable
        //(this variable is to later store The
        //current time of the current player)
        var actualPlayerTime = sessionStorage.getItem("totalStoryElapsedTime");

        //varible to store actual player
        var actualPlayer;

        //variable to store YouTube player
        var ytPlayer;

        //varible to store iframe
        var iframe;


        for (i = 0; i < allPlayers.length; i++) {
            if (allPlayers[i].tagName == "VIDEO" || allPlayers[i].tagName == "AUDIO" || allPlayers[i].tagName == "IMG") {
                if (getPlayerDuration(allPlayers[i]) > actualPlayerTime) {
                    actualPlayer = allPlayers[i];
                    actualPlayer.dataset.current = actualPlayerTime;
                    break;
                }
                actualPlayerTime -= getPlayerDuration(allPlayers[i]);

            } else if (allPlayers[i].tagName == "IFRAME") {
                for (j = 0; j < player.length; j++) {
                    if (player[j].g == allPlayers[i]) {
                        ytPlayer = player[j];
                        iframe = allPlayers[i];
                        break;
                    }
                }
                if (getYTPlayerDuration(ytPlayer) > actualPlayerTime) {
                    actualPlayer = ytPlayer;
                    break;
                }
                actualPlayerTime -= getYTPlayerDuration(ytPlayer);
            }
        }
        buttonsCalculate(getPlayerIndex(actualPlayer))
        queueManager(actualPlayer);

        if (actualPlayer.tagName == "VIDEO") {
            actualPlayer.currentTime = actualPlayerTime;
            actualPlayer.style.display = "inline";
            actualPlayer.style.width = "100%";
            actualPlayer.style.height = "100%";
            actualPlayer.parentElement.style.display = "inline";
            actualPlayer.parentElement.style.width = "100%";
            actualPlayer.parentElement.style.height = "100%";
            actualPlayer.play();
        } else if (actualPlayer.tagName == "AUDIO") {
            actualPlayer.currentTime = actualPlayerTime;
            actualPlayer.style.display = "inline";
            actualPlayer.style.width = "100%";
            actualPlayer.style.height = "100px";
            actualPlayer.parentElement.style.display = "inline";
            actualPlayer.parentElement.style.width = "100%";
            actualPlayer.parentElement.style.height = "100px";
            actualPlayer.play();
        } else if (actualPlayer.tagName == "IMG") {
            actualPlayer.currentTime = actualPlayerTime;
            actualPlayer.style.display = "inline";
            actualPlayer.style.width = "100%";
            actualPlayer.style.height = "100%";
            actualPlayer.parentElement.style.display = "inline";
            actualPlayer.parentElement.style.width = "100%";
            actualPlayer.parentElement.style.height = "100%";
            queueManager(actualPlayer)
        } else {
            actualPlayer.loadVideoByUrl(iframe.getAttribute("src"), actualPlayerTime, 'large');
            actualPlayer.g.style.display = "inline";
            actualPlayer.g.style.width = "100%";
            actualPlayer.g.style.height = "100%";
            actualPlayer.g.parentElement.style.display = "inline";
            actualPlayer.g.parentElement.style.width = "100%";
            actualPlayer.g.parentElement.style.height = "100%";
        }
    }

    function getNextPlayerIndex(Player) {

        var adjacentPlayerIndex = 0;


        adjacentPlayerIndex = getPlayerIndex(Player) + 1;


        if (Player.tagName == "VIDEO" || Player.tagName == "AUDIO") {
            //get the story order of the actual video or audio
            Player.pause();
            Player.currentTime = 0;
            Player.style.display = "none";
            Player.style.width = 0;
            Player.style.height = 0;
            Player.parentElement.style.display = "none";
            Player.parentElement.style.width = 0;
            Player.parentElement.style.height = 0;

        } else if (Player.tagName == "IMG") {
            Player.currentTime = 0;
            Player.style.display = "none";
            Player.style.width = 0;
            Player.style.height = 0;
            Player.parentElement.style.display = "none";
            Player.parentElement.style.width = 0;
            Player.parentElement.style.height = 0;

        } else {
            //get the story order of the actual YouTube video
            adjacentPlayerIndex = Array.prototype.slice.call(allPlayers).indexOf(Player.g) + 1;

            Player.pauseVideo();
            Player.g.style.display = "none";
            Player.g.style.width = 0;
            Player.g.style.height = 0;
            Player.g.parentElement.style.display = "none";
            Player.g.parentElement.style.width = 0;
            Player.g.parentElement.style.height = 0;
        }

        //verify if there are more players
        if (adjacentPlayerIndex == allPlayers.length) {
            adjacentPlayerIndex = allPlayers.length - 1;
        }

        return adjacentPlayerIndex;
    }

    function getPreviousPlayerIndex(Player) {

        var adjacentPlayerIndex = 0;

        adjacentPlayerIndex = getPlayerIndex(Player) - 1;

        if (Player.tagName == "VIDEO" || Player.tagName == "AUDIO") {
            Player.pause();
            Player.currentTime = 0;
            Player.style.display = "none";
            Player.style.width = 0;
            Player.style.height = 0;
            Player.parentElement.style.display = "none";
            Player.parentElement.style.width = 0;
            Player.parentElement.style.height = 0;

        } else if (Player.tagName == "IMG") {
            Player.currentTime = 0;
            Player.style.display = "none";
            Player.style.width = 0;
            Player.style.height = 0;
            Player.parentElement.style.display = "none";
            Player.parentElement.style.width = 0;
            Player.parentElement.style.height = 0;

        } else {
            Player.stopVideo();
            Player.g.style.display = "none";
            Player.g.style.width = 0;
            Player.g.style.height = 0;
            Player.g.parentElement.style.display = "none";
            Player.g.parentElement.style.width = 0;
            Player.g.parentElement.style.height = 0;
        }

        //verify if there are more players
        if (adjacentPlayerIndex == -1) {
            adjacentPlayerIndex = 0;
        }

        return adjacentPlayerIndex;
    }

    function playAdjacentPlayer(adjacency) {

        var adjacentPlayerIndex;

        var Player = queue.pop();

        var actualIndex = getPlayerIndex(Player)
        if (actualIndex == 0 || actualIndex == allPlayers.length - 1) {
            if (!queue.includes(Player) && Player !== undefined) {
                queue.push(Player);
            }
        }

        if (adjacency == "left") {
            adjacentPlayerIndex = getPreviousPlayerIndex(Player);
        } else {
            adjacentPlayerIndex = getNextPlayerIndex(Player);
        }
        buttonsCalculate(adjacentPlayerIndex)

        //queue.push(allPlayers[adjacentPlayerIndex]);
        if (allPlayers[adjacentPlayerIndex].tagName == "VIDEO") {
            allPlayers[adjacentPlayerIndex].style.display = "inline";
            allPlayers[adjacentPlayerIndex].style.width = "100%";
            allPlayers[adjacentPlayerIndex].style.height = "100%";
            allPlayers[adjacentPlayerIndex].parentElement.style.display = "inline";
            allPlayers[adjacentPlayerIndex].parentElement.style.width = "100%";
            allPlayers[adjacentPlayerIndex].parentElement.style.height = "100%";
            allPlayers[adjacentPlayerIndex].play();
        } else if (allPlayers[adjacentPlayerIndex].tagName == "AUDIO") {
            allPlayers[adjacentPlayerIndex].style.display = "inline";
            allPlayers[adjacentPlayerIndex].style.width = "100%";
            allPlayers[adjacentPlayerIndex].style.height = "100px";
            allPlayers[adjacentPlayerIndex].parentElement.style.display = "inline";
            allPlayers[adjacentPlayerIndex].parentElement.style.width = "100%";
            allPlayers[adjacentPlayerIndex].parentElement.style.height = "100px";
            allPlayers[adjacentPlayerIndex].play();
        } else if (allPlayers[adjacentPlayerIndex].tagName == "IMG") {
            allPlayers[adjacentPlayerIndex].style.display = "inline";
            allPlayers[adjacentPlayerIndex].style.width = "100%";
            allPlayers[adjacentPlayerIndex].style.height = "100%";
            allPlayers[adjacentPlayerIndex].parentElement.style.display = "inline";
            allPlayers[adjacentPlayerIndex].parentElement.style.width = "100%";
            allPlayers[adjacentPlayerIndex].parentElement.style.height = "100%";
            queueManager(allPlayers[adjacentPlayerIndex]);
        } else {
            //get YouTube player to play it
            for (j = 0; j < player.length; j++) {
                if (player[j].g == allPlayers[adjacentPlayerIndex]) {
                    allPlayers[adjacentPlayerIndex].style.display = "inline";
                    allPlayers[adjacentPlayerIndex].style.width = "100%";
                    allPlayers[adjacentPlayerIndex].style.height = "100%";
                    allPlayers[adjacentPlayerIndex].parentElement.style.display = "inline";
                    allPlayers[adjacentPlayerIndex].parentElement.style.width = "100%";
                    allPlayers[adjacentPlayerIndex].parentElement.style.height = "100%";
                    player[j].playVideo();
                    break;
                }
            }
        }
    }

    //return player (tag <video> or <audio>) time mark position
    function getPlayerCurrentTime(player) {
        if (player.tagName == "IMG") {
            var currentime = player.dataset.current
            return currentime === undefined ? 0 : Math.round(player.dataset.current);
        } else {
            return Math.round(player.currentTime);
        }
    }

    //return player (tag <video> or <audio>) duration
    function getPlayerDuration(player) {
        if (player.tagName == "IMG") {
            return Math.round(player.dataset.duration);
        } else {
            return Math.round(player.duration);
        }
    }

    //function to manage the queue for video media type
    function queueManager(Player) {

        //Check the totalElapsedTime every second to verify if buttons need to be hidden
        setInterval(getTotalElapsedStoryTime, 1000);

        //only push into queue when the video player
        //doesn't exist alerady in the queue
        if (!queue.includes(Player)) {
            queue.push(Player);
        }

        //if there is more than one video playing
        if (queue.length > 1) {
            //retrieve last video
            lastPlay = queue.shift();
            //actions to take to stop
            //if lastPlay has tag name "VIDEO"
            if (lastPlay.tagName == "VIDEO" || lastPlay.tagName == "AUDIO") {
                //console.log(lastPlay)
                lastPlay.pause();
                lastPlay.currentTime = 0;
                lastPlay.style.display = "none";
                lastPlay.parentElement.style.width = 0;
                lastPlay.parentElement.style.height = 0;
                lastPlay.parentElement.style.display = "none";
                lastPlay.parentElement.style.width = 0;
                lastPlay.parentElement.style.height = 0;
            } else if (lastPlay.tagName == "IMG") {
                lastPlay.style.display = "none";
                lastPlay.parentElement.style.width = 0;
                lastPlay.parentElement.style.height = 0;
                lastPlay.parentElement.style.display = "none";
                lastPlay.parentElement.style.width = 0;
                lastPlay.parentElement.style.height = 0;
            } else {
                //console.log(lastPlay)
                lastPlay.pauseVideo();
                lastPlay.g.style.display = "none";
                lastPlay.g.parentElement.style.width = 0;
                lastPlay.g.parentElement.style.height = 0;
                lastPlay.g.parentElement.style.display = "none";
                lastPlay.g.parentElement.style.width = 0;
                lastPlay.g.parentElement.style.height = 0;
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
        } else if (event.target.getPlayerState() == YT.PlayerState.ENDED) {
            playAdjacentPlayer("right");
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

    function buttonsCalculate(playerIndex) {
        //If it reached the end or beginning don't restart
        if (playerIndex == 0) {
            prevBtn.style.visibility = "hidden";
        } else {
            prevBtn.style.visibility = "visible";
        }

        if (playerIndex >= allPlayers.length - 1) {
            nextBtn.style.visibility = "hidden";
        } else if (playerIndex < allPlayers.length - 1) {
            nextBtn.style.visibility = "visible";
        }

    }

    function getPlayerIndex(Player) {
        if (Player.tagName != "VIDEO" && Player.tagName != "AUDIO" && Player.tagName != "IMG") {
            Player = Player.g;
        }
        return Array.prototype.slice.call(allPlayers).indexOf(Player)
    }
</script>

</html>