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

// Connexion
if (isset($_POST['valider'])) {
	$identifiant = htmlspecialchars($_POST['identifiant']);
	$motDePasse = sha1($_POST['motDePasse']);

	if (!empty($_POST['identifiant']) AND !empty($_POST['motDePasse'])) {

        $reqMembre = $bdd->prepare("SELECT * FROM forum_membres WHERE membre_pseudo = ? AND membre_mdp = ?");
        
        $reqMembre->execute(array($identifiant, $motDePasse));
        $membreExist = $reqMembre->rowCount();

        if ($membreExist == 1) {
            $membreInfo = $reqMembre->fetch();
            $_SESSION['membre_pseudo'] = $membreInfo['membre_pseudo'];
            
        } else {
            $erreur = "Identifiant ou mot de passe incorrect !";
        }
	} else {
		$erreur = "Tous les champs doivent être complétés !";
	}
}

if (isset($_SESSION['membre_pseudo'])) {
    $membres = true;
    

}
if(!empty($_POST)){
    extract($_POST);
    $valid = true;

    // On se place sur le bon formulaire grâce au "name" de la balise "input"
    if (isset($_POST['inscription'])){
        $pseudo  = htmlentities(trim($pseudo)); // On récupère le nom
        $mail = htmlentities(strtolower(trim($mail))); // On récupère le mail
        $mdp = trim($mdp); // On récupère le mot de passe 
        $confmdp = trim($confmdp); //  On récupère la confirmation du mot de passe

        //  Vérification du nom
        if(empty($pseudo)){
            $valid = false;
            $er_pseudo = ("Le nom d' utilisateur ne peut pas être vide");
        }       

        // Vérification du mail
        if(empty($mail)){
            $valid = false;
            $er_mail = "Le mail ne peut pas être vide";

            // On vérifit que le mail est dans le bon format
        }elseif(!preg_match("/^[a-z0-9\-_.]+@[a-z]+\.[a-z]{2,3}$/i", $mail)){
            $valid = false;
            $er_mail = "Le mail n'est pas valide";

        }else{
            // On vérifit que le mail est disponible
            $req_mail = $DB->query("SELECT membre_email FROM forum_membres WHERE membre_email = ?",
                array($mail));

            $req_mail = $req_mail->fetch();

            if ($req_mail['mail'] <> ""){
                $valid = false;
                $er_mail = "Ce mail existe déjà";
            }
        }

        // Vérification du mot de passe
        if(empty($mdp)) {
            $valid = false;
            $er_mdp = "Le mot de passe ne peut pas être vide";

        }elseif($mdp != $confmdp){
            $valid = false;
            $er_mdp = "La confirmation du mot de passe ne correspond pas";
        }

        // Si toutes les conditions sont remplies alors on fait le traitement
        if($valid){

            $mdp = crypt($mdp, "$6$rounds=5000$macleapersonnaliseretagardersecret$");

            // On insert nos données dans la table utilisateur
            $DB->insert("INSERT INTO forum_membres (membre_pseudo, membre_email, membre_mdp) VALUES 
                (?, ?, ?)", 
                array($pseudo, $mail, $mdp));

                $erreur = "Compte crer !";
            exit;
        }
    }
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
<script>


function AfficherPost(id)
{
  if (document.getElementById(id).style.display = 'block')
  {
    document.getElementById(id).style.display = 'none';
    document.getElementById('posterPost').style.display = 'block';
  }
}
</script>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title>Forum PHP Joffrey/Nicolas/Dan </title>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div id="header" class="col-lg-12 text-center">
                <img class="img-fluid" src="header.jpg" style="height:100px; ">
            </div>
            <div class="col-lg-1"></div>
            <div id="posts" class="col-lg-7">
            </div>
            <div id="posterPost" class="col-lg-7" style="display:none;">
            <div class="row" >
                <div class="container">
            <form method="POST" class="col-lg-12" style="background-color: white; margin-top:20px;" >
                <table class="table">
                    <tr>
                        <th colspan="2" style="text-align: center;">Nouveau Post</th>
                    </tr>
                    <tr>
                        <td>Sujet</td>
                        <td><input type="text" name="sujet" size="70" maxlength="70" /></td>
                    </tr>
                    <tr >
                        <td>Message</td>
                        <td><textarea name="contenu"></textarea></td>
                    </tr>
                    <tr >
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
            <div id="profil" class="col-lg-2" style="margin-top:1%; background-color: lightgrey;">
            <?php if ($membres) { ?>
                <!--<img id="image-profil" src="https://fakeimg.pl/200x200/">-->
                <p id="nom-Profil" style="text-align: center; margin-top:15px; margin-bottom:0px;" ><?php echo strtoupper($_SESSION['membre_pseudo']);?></p>
                <p style="margin-left: 10%; color:grey; margin-top: 10px;" >Nom : </p>
                <p style="margin-left: 10%; color:grey; margin-top: 10px;" >Prénom : </p>
                <p style="margin-left: 10%; color:grey; margin-top: 10px;">Sexe : </p>
                <p style="margin-left: 10%; color:grey; margin-top: 10px;">Date de naissance : </p>
                <p style="color:grey; margin-left: 10%; margin-top: 10px;">Mail :</p>
                <a  class="btn btn-dark" style="display:block; margin-top: 15px;" href="deconnexion.php">Déconnexion</a>
                <a  class="btn btn-dark" style="display:block; margin-top: 15px; color:white;"  onclick="AfficherPost('posts');">Crer un post</a>
            <?php } else { ?>
                <div>
                    <form id="formconnexion" method="POST">
                    <h2 style="background-color:grey; margin-left:-14px; margin-right:-14px; margin-bottom:20px; text-align:center; margin-top:-10px;">Connexion</h2>
                        <input type="text" name="identifiant" placeholder="Identifiant"><br><br>
                        <input type="password" name="motDePasse" placeholder="Mot de passe"><br><br>
                        <input type="submit" name="valider" value="Se Connecter"><br><br>
                        <?php if (isset($erreur)) {
                            echo "<font color='red'>".$erreur."</font><br/><br/>";
                        } ?>
                    </form>
                    </div>
                    <div style="margin-top:5%;"> 
                    <h2 style="background-color:grey; margin-left:-14px; margin-right:-14px; margin-bottom:20px; text-align:center;">Inscription</h2>
                    <form method="post">
                        <?php
                            // S'il y a une erreur sur le nom alors on affiche
                            if (isset($er_pseudo)){
                            ?>
                                <div><?= $er_pseudo ?></div>
                            <?php   
                            }
                        ?>
                        <input  style="margin-bottom: 20px; width:40%; display:inline;" type="text" placeholder="nom" name="nom" alue="<?php if(isset($nom)){ echo $nom; }?>" require>
                        <input  style="margin-bottom: 20px; width:40%; margin-left:18%; display:inline;" type="text" placeholder="prenom" name="prenom"  value="<?php if(isset($prenom)){ echo $prenom; }?>" require>
                        <input type="text" placeholder="Votre pseudo" name="pseudo" value="<?php if(isset($pseudo)){ echo $pseudo; }?>" required><br><br>
                        <input type="date" placeholder="Date de naissance" name="naissance" value="<?php if(isset($naissance)){ echo $naissance; }?>" required><br><br>
                        <select name="sexe" id="sexe-select">
                            <option value="" disabled selected>-- Sexe --</option>
                            <option value="homme">Homme</option>
                            <option value="femme">Femme</option>
                            <option value="nonbinaire">Non Binaire</option>
                        </select>
                        <?php
                            if (isset($er_mail)){
                            ?>
                                <div><?= $er_mail ?></div>
                            <?php   
                            }
                        ?>
                        <input type="email" placeholder="Adresse mail" name="mail" value="<?php if(isset($mail)){ echo $mail; }?>" required><br><br>
                        <?php
                            if (isset($er_mdp)){
                            ?>
                                <div><?= $er_mdp ?></div>
                            <?php   
                            }
                        ?>
                        <input type="password" placeholder="Mot de passe" name="mdp" value="<?php if(isset($mdp)){ echo $mdp; }?>" required><br><br>
                        <input type="password" placeholder="Confirmer le mot de passe" name="confmdp" required><br><br>
                        <button class="btn btn-dark" type="submit" name="inscription" style="width:100%">S'inscrire</button>
                    </form>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>