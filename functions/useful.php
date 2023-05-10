<?php
// Function to display an alert message
function alert($msg)
{
    echo "<script>
        alert('$msg');
    </script>";
}

// Function to reload the current page
function reload_page()
{
    echo '<script>
        window.location.href = window.location.href;
    </script>';
    exit(); // End the script execution
}

// This function displays an alert message with the given message and redirects the user to the given URL.
function message_redirect($msg, $redirect)
{
    echo "<script>
        alert('$msg');
        window.location.replace('$redirect');        
    </script>";
    exit;
}

//Save a file in the directory inserted using the new name
function save_file($directory, $new_name, $file_input_name)
{
    if (!is_dir($directory) && !mkdir($directory, 0777, true)) {
        die("Error the directory does not exist $directory");
    }
    $file_path = $directory . $new_name;
    //If file already exist, return false, don't save file
    if (is_file($file_path)) {
        return false;
    }
    if (move_uploaded_file($_FILES[$file_input_name]["tmp_name"], $file_path)) {
        return true;
    }
    return false;
}

function generate_file_name($name, $file_input_name)
{
    return $name . time() . "." . pathinfo($_FILES[$file_input_name]["name"], PATHINFO_EXTENSION);
}



//Deletes a file if it exists
function delete_file($file_path)
{
    if (file_exists($file_path)) {
        return unlink($file_path);
    } else {
        return false;
    }
}


/**
 * Swaps the values of two records in a database table
 * $pdo The PDO object for the database connection
 * $storyID The ID of the story the records belong to
 * $value1 The value of the first record to be swapped
 * $value2 The value of the second record to be swapped
 * $nameTable The name of the database table
 * $nameField1 The name of the field to be updated
 * $nameFieldID The name of the ID field
 * return true if the swap was successful, false otherwise
 */
function swapValues($pdo, $storyID, $value1, $value2, $nameTable, $nameField1, $nameFieldID)
{
    $new_order1 = -$value2;
    $new_order2 = -$value1;

    // Update the storyOrder to it's new values, but in negative, to avoid a unique key constraint violation.
    $sql1 = "UPDATE $nameTable SET $nameField1 = 
            CASE
                WHEN $nameField1 = :order1 THEN :new_order1
                WHEN $nameField1 = :order2 THEN :new_order2
            END 
        WHERE $nameField1 IN (" . $value1 . ", " . $value2 . ") AND $nameFieldID = :id";

    //Set the values changed to positive
    $sql2 = "UPDATE $nameTable SET $nameField1 = -$nameField1 
    WHERE $nameField1 IN (:new_order1, :new_order2) AND $nameFieldID = :id";

    // start a transaction
    $pdo->beginTransaction();

    // prepare and execute the first statement, that commits the new values in negative
    $stmt1 = $pdo->prepare($sql1);
    $stmt1->bindParam(':new_order1', $new_order1);
    $stmt1->bindParam(':new_order2', $new_order2);
    $stmt1->bindParam(':id', $storyID);
    $stmt1->bindParam(':order1', $value1);
    $stmt1->bindParam(':order2', $value2);

    $stmt1->execute();

    // prepare and execute the second statement to return the values to positive
    $stmt2 = $pdo->prepare($sql2);
    $stmt2->execute([
        ':new_order1' => $new_order1,
        ':new_order2' => $new_order2,
        ':id' => $storyID
    ]);
    // commit the transaction
    if ($pdo->commit()) {
        return true;
    }
    return false;
}
