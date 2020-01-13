<?php
session_start();


try
{
   $bdd = new PDO('mysql:host=localhost;dbname=ForumPHP;charset=utf8', 'root', 'root');
}
catch(Exception $e)
{
   die('Erreur : '.$e->getMessage());
}

if (isset($_SESSION['membre_pseudo'])) {
    $membres = true;
}

if(isset($_SESSION['membre_pseudo'])) {
    if(isset($_POST['submit'])) {
       if(isset($_POST['sujet'],$_POST['contenu'])) {
          $sujet = htmlspecialchars($_POST['sujet']);
          $contenu = htmlspecialchars($_POST['contenu']);
          if(!empty($sujet) AND !empty($contenu)) {
             if(strlen($sujet) <= 70) {
                $ins = $bdd->prepare('INSERT INTO post (title, content) VALUES(?,?)');
                $ins->execute(array($sujet,$contenu));
             } else {
                $error = "Votre sujet ne peut pas dépasser 70 caractères";
             }
          } else {
             $error = "Veuillez compléter tous les champs";
          }
       }
    }
 } else {
    $error = "Veuillez vous connecter pour poster un nouveau topic";
 }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title>Forum PHP Joffrey/Nicolas/Dan</title>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div id="header" class="col-lg-12">
                <h1 id="titre-forum">Forum PHP Joffrey/Nicolas/Dan</h1>

            </div>
            <div class="col-lg-1"></div>
            <div id="posts" class="col-lg-7">
                <div style=" width: 100%; height: 220px; background-color:white; margin-top: 20px; ">
                <div style="margin-left: 70px;">
            <form method="POST" >
                <table>
                    <tr>
                        <th colspan="2" style="text-align: center;">Nouveau Post</th>
                    </tr>
                    <tr>
                        <td>Sujet</td>
                        <td><input type="text" name="sujet" size="70" maxlength="70" /></td>
                    </tr>
                    <tr>
                        <td>Message</td>
                        <td><textarea name="contenu"></textarea></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit" name="submit" value="Poster le Topic" /></td>
                    </tr>
                    <?php if(isset($error)) { ?>
                    <tr>
                        <td colspan="2"><?= $error ?></td>
                    </tr>
                    <?php } ?>
                </table>
                </form>
                </div>
                </div>
            </div>
            <div class="col-lg-1"></div>
            <div id="profil" class="col-lg-2">
            <?php if ($membres) { ?>
                <img id="image-profil" src="https://fakeimg.pl/200x200/">
                <p id="nom-Profil" style="text-align: center; margin-top:15px; margin-bottom:0px;" ><?php echo strtoupper($membreInfo['membre_pseudo']);  ?></p>
                <p style=" display:inline; margin-left: 10%; color:grey;" >Age : </p>
                <p style=" display: inline; margin-left: 20%; color:grey; ">Sexe : </p>
                <a  class="btn btn-dark" style="display:block; margin-top: 15px;" href="deconnexion.php">Déconnexion</a>
                <a  class="btn btn-dark" style="display:block; margin-top: 15px;" href="post.php">Crer un post</a>
            <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>