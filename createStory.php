<?php
require "config/connectdb.php";
echo "Transmedia";

if (isset($_POST['submitButton'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $author = $_POST['author'];
  
    //Guarda o url exato 
    $url = $_SERVER['REQUEST_URI'];
    //Separa o url pelo username e guarda o username numa variavel
    $url_components = parse_url($url);
    parse_str($url_components['query'], $params);
    $username = $params['username'];
  
  
    //Seleciona o nome da historia da base de dados
    $video = "SELECT nome FROM historia WHERE nome like '$nomeh'";
    $result = mysqli_query($conn, $video);
    $row = mysqli_fetch_row($result);
  
    //se o nome da história não existir a história vai ser criada
    if (empty($row[0])) {
      // sql query para inserir dados na base de dados
      $sql_query = "INSERT INTO historia(nome,autor) VALUES('$nomeh','$username')";
      mysqli_query($conn, $sql_query);
    }
  
  
  
    //depois de inserir o nome e duracao da historia é feito um select para identificar o id da historia em questão
    $id_historia = "SELECT * FROM historia WHERE nome like '$nomeh'";
    $result = mysqli_query($conn, $id_historia);
    $row = mysqli_fetch_row($result);
  
    //Guarda imagem na pasta /fotos
    $name = $_FILES["image"]['name'];
    $tmp_name = $_FILES["image"]['tmp_name'];
  
    if (!strpos($name, " ")) {
      $folder = "../fotos/";
      if (move_uploaded_file($tmp_name, $folder . $name)) {
      }
      //Guarda imagem na base de dados
      $sql_query = "INSERT INTO imagem(id_historia,imagem,tempo_inicial,tempo_final,autor) VALUES($row[0],'$name','$tempoi','$tempof','$username')";
      mysqli_query($conn, $sql_query);
    } else {
      echo "<script language='javascript'>";
      echo 'alert("A imagem não pode conter espaços, tente de novo");';
      echo "</script>";
    }
  }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create the story</title>
</head>
<body>
<?php
    $currPage = 'createStory';
    $createStory = 'createStory';
    ?>
<form>
    <!-- Tell the user to insert the Name !-->
    <div class="form-group">
        <label for="name">Name</label>
        <input class="form-control" id = "name" required name="name" type="text">
    </div>

    <!-- Tell the user to insert the Description !-->
    <div class="form-group">
        <label for="description">Description</label>
        <input class="form-control" id = "description" required name="description" type="text">
    </div>

    <!-- Tell the user to insert the Author !-->
    <div class="form-group">
        <label for="author">Author</label>
        <input class="form-control" id = "author" required name="author" type="text">
    </div>
  <button type="submit" name="submitButton" class="btn btn-primary">Submit</button>
</form>
</body>
</html>