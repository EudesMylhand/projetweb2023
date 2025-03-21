<?php
require_once('include/header_password.php');
// ======================================================================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
var_dump($_POST);



if (isset($_POST['forget_password'])) {
    
    // Vérifie si l'email est vide ou invalide
    if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $message = "Merci de saisir une adresse email valide.";
    } else {
        require_once('./include/bdd.php'); // Connexion à la base de données
        
        // Préparation de la requête pour vérifier si l'email existe
        $requete = $bdd->prepare('SELECT * FROM horizon.utlisateur WHERE email_utilisateur = :email');
        $requete->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
        $requete->execute(); // Exécution de la requête
        $resultat = $requete->fetch();

        // Vérifie si l'email existe dans la base de données
        $nombre = $requete->rowCount();
        if ($nombre == 0) {
            $message = "l'adresse email saisi ne correspond à aucun compte.";
        } else {
            
            // Si l'email n'est pas encore validé
            if ($resultat['validation_email_utilisateur'] == 0) {
                require_once "include/token.php"; // Génération d'un token sécurisé
                var_dump($resultat);
                // Mise à jour du token dans la base de données
                $update = $bdd->prepare('UPDATE horizon.utlisateur SET token_utilisateur=:token WHERE email_utilisateur=:email');
                $update->bindValue(':token', $token);
                $update->bindValue(':email', $_POST['email']);
                $update->execute();
                
                // Renvoi d'un email de validation
                require_once "include/envoimail/sendmail.php";
                // $successMail = "Un Email de verification a ete envoyé pour confirmer votre compte";
            } else {
                require_once "include/token.php"; // Génération d'un token sécurisé

                // Mise à jour du token dans la base de données
                $update = $bdd->prepare('UPDATE horizon.utlisateur SET token_utilisateur=:token WHERE email_utilisateur=:email');
                $update->bindValue(':token', $token);
                $update->bindValue(':email', $_POST['email']);
                $update->execute();
                
                // Envoi d'un email de réinitialisation de mot de passe
                require_once "include/envoimail/sendmail_reinitialisation.php";
                $successMail = "Un Email de verification a ete envoyé pour confirmer votre compte";
            }
        }
    }
}
?>
    <body class="bg-primary">
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header">
                                        <?php if (isset($message)) echo "<p class='text-center text-danger' >$message</p>" ?>
                                        <?php if (isset($successMail)) echo "<p class='text-center text-success' >$successMail</p>" ?>
                                        <h3 class="text-center font-weight-light my-4">Réinitialisation du mot de passe</h3></div>
                                    <div class="card-body">
                                        <div class="small mb-3 text-muted">Enter your email address and we will send you a link to reset your password.</div>
                                        <form action="password.php" method="post">
                                            <div class="form-floating mb-3">
                                                <input class="form-control" name="email" id="inputEmail" type="email" placeholder="name@example.com" />
                                                <label for="inputEmail">Email address</label>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <a class="small" href="login.php">connexion</a>
                                                <input type="submit" class="btn btn-primary" name="forget_password" value="Reset Password" href="login.php" />    
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-footer text-center py-3">
                                        <div class="small"><a href="register.php">Voulez vous creez un nouveau compte!</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
 
        <?php
        require_once('include/footer.php');
        ?>
           