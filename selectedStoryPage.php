<?php
require "config/connectdb.php";
include "./functions/useful.php";

$story = $pdo->prepare('SELECT story.name,story.description,story.author, story.id FROM story WHERE story.id = ?');
$video = $pdo->prepare('SELECT video.link,video.storyId,video.videoType,video.storyOrder,video.duration FROM video WHERE video.storyId = ? ORDER BY video.storyOrder');
$audio = $pdo->prepare('SELECT audio.id,audio.id_story,audio.audio,audio.storyOrder,duration FROM audio WHERE audio.id_story = ? ORDER BY audio.storyOrder');
$image = $pdo->prepare('SELECT id,storyID,image,duration,storyOrder FROM image WHERE storyID = ? ORDER BY storyOrder');
$text = $pdo->prepare('SELECT id,id_story,text,duration,storyOrder FROM text WHERE id_story = ? ORDER BY storyOrder');

$story->execute([$_GET['id']]);
$video->execute([$_GET['id']]);
$audio->execute([$_GET['id']]);
$image->execute([$_GET['id']]);
$text->execute([$_GET['id']]);

$storyFetch = $story->fetch(PDO::FETCH_ASSOC);
$videoFetch = $video->fetchAll(PDO::FETCH_ASSOC);
$audioFetch = $audio->fetchAll(PDO::FETCH_ASSOC);
$imagesFetch = $image->fetchAll(PDO::FETCH_ASSOC);
$textFetch = $text->fetchAll(PDO::FETCH_ASSOC);

$totaltimeVideo = array_sum(array_column($videoFetch, 'duration'));
$totaltimeAudio = array_sum(array_column($audioFetch, 'duration'));
$totaltimeImage = array_sum(array_column($imagesFetch, 'duration'));
$totaltimeText = array_sum(array_column($textFetch, 'duration'));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="./style/selected_story_page.css" type="text/css">
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
                        echo "<a href='user_profile.php?user=" . $storyFetch['author'] . "'>" . $storyFetch['author'] . "</a>";
                        ?>
                    </p>
                </div>

                <div class="change-media" id="changeMediaBtn">

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
                    if (isset($_POST["mediaOpt"])) {
                        $_SESSION["mediaOpt"] = $_POST["mediaOpt"];
                    } else {
                        // Determine the first available media option
                        if (count($videoFetch) > 0) {
                            $_SESSION["mediaOpt"] = "video";
                        } elseif (count($audioFetch) > 0) {
                            $_SESSION["mediaOpt"] = "audio";
                        } elseif (count($imagesFetch) > 0) {
                            $_SESSION["mediaOpt"] = "images";
                        } elseif (count($textFetch) > 0) {
                            $_SESSION["mediaOpt"] = "text";
                        }
                    }

                    $mediaOpt = $_SESSION["mediaOpt"];

                    if (count($videoFetch) + count($audioFetch) + count($imagesFetch)  + count($textFetch) == 0) {
                        echo "<p>Sem conteúdo para apresentar</p>";
                    } else {
                        echo '<div class="spinner-border mt-2" role="status" id="loadingSpinner"><span class="sr-only">Loading...</span></div>';
                        switch ($mediaOpt) {

                            case "video":
                                for ($i = 0; $i < count($videoFetch); $i++) {
                                    echo '<div style="display: none !important; max-height: 550px; height:100%;  width:auto;" id="preview' . $i . '" class="video-preview embed-responsive embed-responsive-16by9 d-inline-block rounded">';
                                    if ($videoFetch[$i]["videoType"] == "file") {
                                        echo '<video style="max-height: 550px; width:auto;  height:100%; " id="player' . $i . '" onplay="queueManager(this)" onended="playAdjacentPlayer(\'right\')" class ="video-audio player video-class embed-responsive-item" controls src="./files/story_' . $videoFetch[$i]["storyId"] . '/video/' . $videoFetch[$i]["link"] . '"></video>';
                                    } elseif ($videoFetch[$i]["videoType"] == "text") {
                                        echo '<iframe style="max-height: 550px; width:auto;" id="player' . $i . '" class ="player video-class embed-responsive-item" type="text/html" src="https://www.youtube.com/embed/' . $videoFetch[$i]["link"] . '?enablejsapi=1" allowfullscreen="true" allow="autoplay" allowscriptaccess="always"></iframe>'; //add iframe with src pointing to the video with this code
                                    }
                                    echo '</div>';
                                }
                                break;


                            case "audio":

                                for ($i = 0; $i < count($audioFetch); $i++) {
                                    echo '<div style="display: none;" id="preview' . $i . '" class="audio-preview">';
                                    echo '<audio style="width: 100%" class=" video-audio player audio-class" onplay="queueManager(this)" onended="playAdjacentPlayer(\'right\')" controls id="audio-player-' . $i . '" src="./files/story_' . $audioFetch[$i]["id_story"] . '/audio/' . $audioFetch[$i]["audio"] . '"></audio>';
                                    echo '</div>';
                                }
                                break;
                            case "images":
                                foreach ($imagesFetch as $image) {
                                    echo '<div style="display: none !important; max-height: 550px; width:auto;">';
                                    echo '<img style="max-height: 550px; width:auto;" class="mb-3 image-class player" data-duration="' . $image['duration'] . '" id="img-' . $image['id'] . '" src="./files/story_' . $image["storyID"] . '/image/' . $image["image"] . '"></img>';
                                    echo '</div>';
                                }
                                break;
                            case "text":
                                foreach ($textFetch as $text) {
                                    echo '<div style="display: none !important; max-height: 550px;  width:auto;">';
                                    echo '<p class="mb-3 text-class player" data-duration="' . $text['duration'] . '" id="text-' . $text['id'] . '">' . $text["text"] . '</p>';
                                    echo '</div>';
                                }
                                break;
                        }

                        echo <<<EOF
    
                        <div id="adjVidButDiv" class="mt-3">
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

    <?php
    include "footer.php";
    ?>

</body>

<script>
    //array with all the players
    var allPlayers;
    var prevBtn;
    var nextBtn;
    var videoBtn;
    var audioBtn;
    var imgBtn;
    var textBtn;
    //total story time
    var totalStoryElapsedTime = 0;

    //queue for player management
    var queue = [];

    //function to be called on <body> load
    function inic() {
        var loadingSpinner = document.getElementById('loadingSpinner');

        allPlayers = document.getElementsByClassName("player");
        if (sessionStorage.getItem("storyId") != <?= $storyFetch['id'] ?>) {
            sessionStorage.setItem("totalStoryElapsedTime", 0);
            sessionStorage.setItem("storyId", <?= $storyFetch['id'] ?>)
        }

        videoBtn = document.getElementById('videobtn');
        audioBtn = document.getElementById('audiobtn');
        imgBtn = document.getElementById('imagebtn');
        textBtn = document.getElementById('textbtn');
        prevBtn = document.getElementById("prevMediaButton");
        nextBtn = document.getElementById("nextMediaButton");


        //variable to count the players with tag = "IFRAME"
        let count = -1;
        let hasIframe = false;

        // Create an array of promises for each player
        const playerPromises = [];

        //iterate through all video players
        for (i = 0; i < allPlayers.length; i++) {
            // if one happens to have tag = "IFRAME"
            if (allPlayers[i].tagName == "IFRAME") {
                //increment count by 1
                count++;
                // Push the promise for this player into the array
                playerPromises.push(prepareYouTubePlayer("player" + i, count));
                hasIframe = true;
            }
        }
        // Check if there are any iframes
        if (hasIframe) {
            // Use Promise.all to wait for all promises to resolve
            Promise.all(playerPromises)
                .then(() => {
                    loadingSpinner.style.display = 'none';

                    // All players have loaded, so play the media now
                    playWithElapsedTime();
                })
                .catch((error) => {
                    // Handle any errors that may occur during loading
                    console.error("Error loading YouTube players:", error);
                });
        } else {
            loadingSpinner.style.display = 'none';
            // If there are no iframes, run the function immediately
            playWithElapsedTime();
        }

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
        console.log("Player Ready")
    }

    // Create a function that returns a promise for each player
    function prepareYouTubePlayer(playerId, count) {
        return new Promise((resolve, reject) => {
            // Call the onYouTubeIframeAPIReady function with the player ID and count
            onYouTubeIframeAPIReady(playerId, count);

            // Set a listener for the "ready" event of the player
            const player = window.player[count];
            player.addEventListener("onReady", () => {
                resolve();
            });
        });
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
        if (actualPlayer.tagName == "VIDEO" || actualPlayer.tagName == "AUDIO" || actualPlayer.tagName == "IMG" || actualPlayer.tagName == "P") {
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
            if (allPlayers[i].tagName == "VIDEO" || allPlayers[i].tagName == "AUDIO" || actualPlayer.tagName == "IMG" || actualPlayer.tagName == "P") {
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
        if (textBtn != null) {
            if (cumulativeTime >= <?= $totaltimeText ?>) {
                textBtn.style.display = "none";
            } else {
                textBtn.style.display = "inline-block";
            }
        }
        return cumulativeTime;
    }

    //function to call when the buttons form is submited
    function onSwitch() {

        //set the session variable with the elapsed story time
        sessionStorage.setItem("totalStoryElapsedTime", getTotalElapsedStoryTime());
        sessionStorage.setItem("scrollHeigth", document.documentElement.scrollTop);
        //empty the queue on switching
        queue.length = 0;
    }

    //function to play the current player
    //acording to the elapsed story time
    async function playWithElapsedTime() {


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
            if (allPlayers[i].tagName == "VIDEO" || allPlayers[i].tagName == "AUDIO" || allPlayers[i].tagName == "IMG" || allPlayers[i].tagName == "P") {
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

        if (actualPlayer.tagName == "VIDEO" || actualPlayer.tagName == "AUDIO") {
            actualPlayer.currentTime = actualPlayerTime;
            actualPlayer.parentElement.style.setProperty("display", "block", "important")
            actualPlayer.play();
        } else if (actualPlayer.tagName == "IMG" || actualPlayer.tagName == "P") {
            actualPlayer.currentTime = actualPlayerTime;
            actualPlayer.parentElement.style.setProperty("display", "block", "important")
            queueManager(actualPlayer)
        } else {
            actualPlayer.loadVideoByUrl(iframe.getAttribute("src"), actualPlayerTime, 'large');
            actualPlayer.g.style.width = "100%";
            actualPlayer.g.style.height = "100%";
            actualPlayer.g.parentElement.style.setProperty("display", "block", "important")
        }

        const scrollHeight = sessionStorage.getItem("scrollHeigth") || 0;

        console.log(scrollHeight)
        window.scrollTo({
            top: scrollHeight,
            behavior: 'smooth'
        });
    }

    function getNextPlayerIndex(Player) {

        var adjacentPlayerIndex = 0;

        adjacentPlayerIndex = getPlayerIndex(Player) + 1;

        if (Player.tagName == "VIDEO" || Player.tagName == "AUDIO") {
            //get the story order of the actual video or audio
            Player.pause();
            Player.currentTime = 0;
            Player.parentElement.style.setProperty("display", "none", "important")

        } else if (Player.tagName == "IMG" || Player.tagName == "P") {
            Player.currentTime = 0;
            Player.parentElement.style.setProperty("display", "none", "important")

        } else {
            //get the story order of the actual YouTube video
            adjacentPlayerIndex = Array.prototype.slice.call(allPlayers).indexOf(Player.g) + 1;
            Player.pauseVideo();
            Player.g.parentElement.style.setProperty("display", "none", "important")
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
            Player.parentElement.style.setProperty("display", "none", "important")
        } else if (Player.tagName == "IMG" || Player.tagName == "P") {
            Player.currentTime = 0;
            Player.parentElement.style.setProperty("display", "none", "important")
        } else {
            Player.stopVideo();
            Player.g.parentElement.style.setProperty("display", "none", "important")
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
            allPlayers[adjacentPlayerIndex].parentElement.style.setProperty("display", "block", "important")
            allPlayers[adjacentPlayerIndex].play();
        } else if (allPlayers[adjacentPlayerIndex].tagName == "AUDIO") {
            allPlayers[adjacentPlayerIndex].style.display = "inline";
            allPlayers[adjacentPlayerIndex].parentElement.style.setProperty("display", "block", "important")
            allPlayers[adjacentPlayerIndex].play();
        } else if (allPlayers[adjacentPlayerIndex].tagName == "IMG" || allPlayers[adjacentPlayerIndex].tagName == "P") {
            allPlayers[adjacentPlayerIndex].style.display = "inline";
            allPlayers[adjacentPlayerIndex].parentElement.style.setProperty("display", "block", "important")
            queueManager(allPlayers[adjacentPlayerIndex]);
        } else {
            //get YouTube player to play it
            for (j = 0; j < player.length; j++) {
                if (player[j].g == allPlayers[adjacentPlayerIndex]) {
                    allPlayers[adjacentPlayerIndex].style.setProperty("display", "block", "important")
                    allPlayers[adjacentPlayerIndex].style.width = "100%";
                    allPlayers[adjacentPlayerIndex].style.height = "100%";
                    allPlayers[adjacentPlayerIndex].parentElement.style.setProperty("display", "block", "important")
                    player[j].playVideo();
                    break;
                }
            }
        }
    }

    //return player (tag <video> or <audio>) time mark position
    function getPlayerCurrentTime(player) {
        if (player.tagName == "IMG" || player.tagName == "P") {
            var currentime = player.dataset.current
            return currentime === undefined ? 0 : Math.round(player.dataset.current);
        } else {
            return Math.round(player.currentTime);
        }
    }

    //return player (tag <video> or <audio>) duration
    function getPlayerDuration(player) {
        if (player.tagName == "IMG" || player.tagName == "P") {
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
                lastPlay.parentElement.style.setProperty("display", "none", "important")
            } else if (lastPlay.tagName == "IMG" || lastPlay.tagName == "P") {
                lastPlay.style.setProperty("display", "none", "important")
                lastPlay.parentElement.style.setProperty("display", "none", "important")
            } else {
                //console.log(lastPlay)
                lastPlay.pauseVideo();
                lastPlay.g.parentElement.style.setProperty("display", "none", "important")
            }
        }
        getTotalElapsedStoryTime();
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
        if (Player.tagName != "VIDEO" && Player.tagName != "AUDIO" && Player.tagName != "IMG" && Player.tagName != "P") {
            Player = Player.g;
        }
        return Array.prototype.slice.call(allPlayers).indexOf(Player)
    }
</script>

</html>