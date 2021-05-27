<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8mb4">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TP : Mini jeu de combat POO</title>
</head>
<body>
    <?php 
        require("autoload.php");

        if(isset($_GET["deconnexion"])){
            session_destroy();
            header("Location: .");
            exit;
        }

        if(isset($_SESSION["perso"])){
            $perso = $_SESSION["perso"];
        }

        $db = new PDO("mysql:host=localhost;dbname=tp_un", "root", "");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

        $personnageManager = new PersonnageManager($db);

        if(isset($_POST["creer"]) && isset($_POST["nom"]) && !empty($_POST["nom"])){
            $perso = new Personnage(["nom" => $_POST["nom"]]);
            if($personnageManager->exists($_POST["nom"])){
                $message = "Le nom du personnage est déjà pris.";
                unset($perso);
            }
            else{
                $personnageManager->add($perso);
            }
        }
        else if(isset($_POST["utiliser"]) && isset($_POST["nom"])){
            if($personnageManager->exists($_POST["nom"])){
                $perso = $personnageManager->get($_POST["nom"]);
            }
            else{
                $message = "Ce personnage n'éxiste pas.";
            }
        }
        else if(isset($_GET["frapper"])){
            if(!isset($perso)){
                $message = "Merci de créer un personnage ou de vous identifier.";
            }
            else{
                if(!$personnageManager->exists((int)$_GET["frapper"])){
                    $message = "Le personnage que vous voulez frapper n'éxiste pas.";
                }
                else{
                    $persoAFrapper = $personnageManager->get((int)$_GET["frapper"]);
                    $retour = $perso->frapper($persoAFrapper);
                    switch($retour){
                        case Personnage::PERSONNAGE_PAREIL :
                            $message = "Vous ne pouvez pas vous frapper vous-même!";
                            break;
                        case Personnage::PERSONNAGE_FRAPPE :
                            $message = "Le personnage a bien été frappé!";
                            $personnageManager->update($perso);
                            $personnageManager->update($persoAFrapper);
                            break;
                        case Personnage::PERSONNAGE_MORT :
                            $message = "Vous avez tué le personnage!";
                            $personnageManager->update($perso);
                            $personnageManager->delete($persoAFrapper);
                            break;
                    }
                }
            }
        }
    ?>
    <p>Nombre de personnages créés : <?php echo $personnageManager->count();?></p>
    <?php 
        if(isset($message)){
            echo "<p>". $message ."</p>";
        }
    ?>
    <?php 
        if(isset($perso)){
            ?>
                <p><a href="?deconnexion">Se déconnecter</a></p>
                <fieldset>
                    <legend>Mes informations</legend>
                    <p>Nom : <?php echo htmlspecialchars($perso->nom());?></p>
                    <p>Vie : <?php echo htmlspecialchars($perso->vie());?></p>
                </fieldset>
                <br>
                <fieldset>
                    <legend>Qui frapper?</legend>
                    <p>
                    <?php 
                        $persos = $personnageManager->getList($perso->nom());
                        if(empty($persos)){
                            echo "Aucun personnage à frapper..";
                        }
                        else{
                            foreach($persos as $unPerso){
                                echo "<fieldset><a href='?frapper=".$unPerso->id()."'>".htmlspecialchars($unPerso->nom())."</a> (Vie : ".$unPerso->vie().")</fieldset>";
                                echo "<br>";
                            }
                        }
                    ?>
                </fieldset>
            <?php
        }
        else{
            ?>
                <form action="" method="post">
                    <label for="nom">Nom :</label>
                    <input type="text" name="nom" maxlength="40" required>
                    <input type="submit" value="Créer ce personnage" name="creer">
                    <input type="submit" value="Utiliser ce personnage" name="utiliser">
                </form>
            <?php
        }
        if(isset($perso)){
            $_SESSION["perso"] = $perso;
        }
    ?>
</body>
</html>
