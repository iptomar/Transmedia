<?php
require_once "./functions/useful.php";
require_once "config/connectdb.php";
if (isset($_POST['orderdownAudio'])) {
    //Turn the order of the video selected down
    $media_id = $_POST['orderdownAudio'];
    $media_change = $pdo->prepare('SELECT id, storyOrder FROM audio WHERE id = ?');
    $media_change->execute([$media_id]);
    $media_reorder = $media_change->fetch(PDO::FETCH_ASSOC);
    $current_order = $media_reorder['storyOrder'];
    $new_order = $current_order - 1;
    if ($new_order == 0) {
        message_redirect("Something went wrong  the value is zero", "edit_story.php?id=$id");
    }
    if (!swapValues($pdo, $id, $current_order, $new_order, "audio", "storyOrder", "id_story")) {
        message_redirect("Something went wrong when changing the order", "edit_story.php?id=$id");
    }
} else if (isset($_POST['orderupAudio'])) {
    //Turn the order of the video selected up
    $media_id = $_POST['orderupAudio'];
    $media_change = $pdo->prepare('SELECT id, storyOrder FROM audio WHERE id = ?');
    $media_change->execute([$media_id]);
    $media_reorder = $media_change->fetch(PDO::FETCH_ASSOC);
    $current_order = $media_reorder['storyOrder'];
    $new_order = $current_order + 1;
    if (!swapValues($pdo, $id, $current_order, $new_order, "audio", "storyOrder", "id_story")) {
        message_redirect("Something went wrong when changing the order", "edit_story.php?id=$id");
    }
} else if (isset($_POST['deleteAudio'])) {
    //Delete the video selected
    $media_id = $_POST['deleteAudio'];
    $stmt = $pdo->prepare('SELECT id, audio, id_story, storyOrder FROM audio WHERE id = ?');
    $stmt->execute([$media_id]);
    $media_alter =  $stmt->fetch(PDO::FETCH_ASSOC);

    $sql = "DELETE FROM audio WHERE id = ?";
    //Update the storyOrder of the videos after the one deleted to reflet the change
    $sql2 = "UPDATE audio SET storyOrder = storyOrder - 1 WHERE storyOrder > ? and id_story = ?";

    $alter_audio_file = $media_alter['audio'];
    delete_file("./files/story_$id/audio/$alter_audio_file");

    // start a transaction
    $pdo->beginTransaction();
    $stmt = $pdo->prepare($sql);
    $stmt2 = $pdo->prepare($sql2);
    if ($stmt->execute([$media_id]) && $stmt2->execute([$media_alter['storyOrder'], $media_alter['id_story']])) {
        // commit the transaction
        if (!$pdo->commit()) {
            message_redirect("Something went wrong when deleting the audio", "edit_story.php?id=$id");
        }
    } else {
        //If a error occurs rollBack
        $pdo->rollBack();
        message_redirect("Something went wrong when deleting the audio", "edit_story.php?id=$id");
    }
}
