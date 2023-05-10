<?php
require_once "./functions/useful.php";
require_once "config/connectdb.php";
if (isset($_POST['orderdownAudio'])) {
    // Get the ID of the selected audio media file  to be moved down in order
    $media_id = $_POST['orderdownAudio'];
    //Retrieve the media ID and its current story order from the database
    $media_change = $pdo->prepare('SELECT id, storyOrder FROM audio WHERE id = ?');
    $media_change->execute([$media_id]);
    $media_reorder = $media_change->fetch(PDO::FETCH_ASSOC);
    $current_order = $media_reorder['storyOrder'];
    // Calculate the new order by decreasing the current order by 1
    $new_order = $current_order - 1;
    //Check if the new order is valid
    if ($new_order == 0) {
        //Redirect to the story edit page with an error message if the new order is zero
        message_redirect("Something went wrong the value is zero", "edit_story.php?id=$id");
    }
    // Swap the order value of the selected audio media file with the file bellow it
    if (!swapValues($pdo, $id, $current_order, $new_order, "audio", "storyOrder", "id_story")) {
        // If the swapping operation fails, display an error message and redirect the user to the story edit page
        message_redirect("Something went wrong when changing the order", "edit_story.php?id=$id");
    }
} else if (isset($_POST['orderupAudio'])) {
    // Get the ID of the selected audio media file
    $media_id = $_POST['orderupAudio'];
    //Retrieve the media ID and its current story order from the database
    $media_change = $pdo->prepare('SELECT id, storyOrder FROM audio WHERE id = ?');
    $media_change->execute([$media_id]);
    $media_reorder = $media_change->fetch(PDO::FETCH_ASSOC);
    $current_order = $media_reorder['storyOrder'];
    // Calculate the new order by increasing the current order by 1
    $new_order = $current_order + 1;
    // Swap the order value of the selected audio media file with the file above it
    if (!swapValues($pdo, $id, $current_order, $new_order, "audio", "storyOrder", "id_story")) {
        // If the swapping operation fails, display an error message and redirect the user to the story edit page
        message_redirect("Something went wrong when changing the order", "edit_story.php?id=$id");
    }
} else if (isset($_POST['deleteAudio'])) {
    // Get the ID of the selected audio file to be deleted
    $media_id = $_POST['deleteAudio'];
    // Retrieve information about the selected audio file
    $stmt = $pdo->prepare('SELECT id, audio, id_story, storyOrder FROM audio WHERE id = ?');
    $stmt->execute([$media_id]);
    $media_alter =  $stmt->fetch(PDO::FETCH_ASSOC);

    // Define SQL statements to delete the selected audio file and update the storyOrder of subsequent files
    $sql = "DELETE FROM audio WHERE id = ?";
    $sql2 = "UPDATE audio SET storyOrder = storyOrder - 1 WHERE storyOrder > ? and id_story = ?";

    // Delete the audio file from the server
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
