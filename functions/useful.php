<?php
function message_redirect($msg, $redirect)
{
    echo "<script>
        alert('$msg');
        window.location.replace('$redirect');        
    </script>";
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
