<?php
require "config/connectdb.php";
require "NavBar.php";
?>


<div class="container d-flex align-items-center justify-content-center h-75">
    <div class="text-center">

        <?php
        if (!isset($_GET["key"])) {
            echo  setMessage("Invalid link", "The link given is not valid, verify if it was introduced correctly");
        } else {

            $verkey = $_GET["key"];
            //select the id of the user that has the verification key passed via the parameter "key"
            $qry = $pdo->prepare('SELECT id, verificationKey FROM user WHERE verificationKey = ?');
            $qry->execute([$verkey]);
            $results = $qry->fetch();
            //if the verification key is found
            if ($qry->rowCount() == 1) {
                //update the user the key belongs to as verified and remove the verification key from the db
                $qry = $pdo->prepare('UPDATE user SET verified = ?, verificationKey = ? WHERE id = ?');
                $result = $qry->execute([true, '', $results["id"]]);
                if ($qry->rowCount() > 0) {
                    echo setMessage("Email was successfully verified", "Please login");
                } else {
                    echo setMessage("Something went wrong while verifying the account, please try again", "");
                }
            } else {
                echo setMessage("Invalid key", "It might already been used, please check if your account is verified if it's not please request a new key");
            }
        }

        function setMessage($title, $message)
        {
            return "<h3 class='w-100 text-center'>$title</h2><p class='text-center'>$message</p>";
        }

        ?>

        <a href="index.php"><img class="mt-2" style="width:80px;" src="assets/icon_home.svg"></a>
    </div>
</div>
<?php
include "footer.php";
?>