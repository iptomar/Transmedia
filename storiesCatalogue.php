
<?php 

require "config/connectdb.php";
echo "Transmedia\n";

$stmt = $pdo->prepare('SELECT * FROM story');
$stmt->execute();
$stories = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "stories_aquired"
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Stories Catalogue</title>
</head>

<body>
    <div id="storiesContainer">
        <div class="card" style="width: 18rem;">
            <img src="100x100_logo.png" class="card-img-top" alt="...">
            <div class="card-body">
                <p class="card-text">Historia teste</p>
            </div>
        </div>
        <div class="card" style="width: 18rem;">
            <img src="100x100_logo.png" class="card-img-top" alt="...">
            <div class="card-body">
                <p class="card-text">Historia teste</p>
            </div>
        </div>
        <div class="card" style="width: 18rem;">
            <img src="100x100_logo.png" class="card-img-top" alt="...">
            <div class="card-body">
                <p class="card-text">Historia teste</p>
            </div>
        </div>
        <div class="card" style="width: 18rem;">
            <img src="100x100_logo.png" class="card-img-top" alt="...">
            <div class="card-body">
                <p class="card-text">Historia teste</p>
            </div>
        </div>
    </div>
    <?php 
    

    
    ?>
</body>

</html>