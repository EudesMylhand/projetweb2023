<?php

// Connexion à la base de données (Assurez-vous que $bdd est bien défini)
require_once('include/bdd.php');
// require 'config.php';

if (isset($_GET['email'], $_GET['token']) && !empty($_GET['email']) && !empty($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];

    try {   
        // Vérifier si l'utilisateur existe avec l'email et le token fourni
        $requete = $bdd->prepare('SELECT * FROM horizon.utlisateur 
                                  WHERE email_utilisateur = :email
                                  AND token_utilisateur = :token');

        $requete->bindValue(':email', $email); //, PDO::PARAM_STR
        $requete->bindValue(':token', $token); //, PDO::PARAM_STR
        $requete->execute();
    
        $nombre = $requete->rowCount() ;
        
        if ($nombre == 1) {
            // Mettre à jour l'utilisateur
            $update = $bdd->prepare('UPDATE horizon.utlisateur 
                                     SET validation_email_utilisateur = :valide, 
                                         token_utilisateur = :new_token
                                     WHERE email_utilisateur = :email');

            $update->bindValue(':email', $email); //, PDO::PARAM_STR
            $update->bindValue(':new_token', "EmailValide"); //, PDO::PARAM_STR
            $update->bindValue(':valide', 1); //, PDO::PARAM_INT

            $resultatUpdate = $update->execute();
            
            if ($resultatUpdate) {
                echo "<script type=\"text/javascript\">
                alert('Votre adresse email est confirmée !');
                window.location.href = './login.php';
                </script>";
                // header("Location: login.php");
                exit; // Arrêter l'exécution après la redirection

            } else {
                echo "Erreur lors de la mise à jour.";
            }
        } else {
            echo "Lien de validation invalide ou expiré.";
        }
    } catch (PDOException $e) {
        echo "Erreur SQL : " . $e->getMessage();
    }
} else {
    echo "Paramètres manquants.";
}
?>


