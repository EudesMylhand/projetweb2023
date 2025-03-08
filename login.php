<?php require_once('./include/bdd.php'); ?>

<?php
ob_start(); // Active la mise en mémoire tampon
session_start();
if (isset($_POST['connexion'])) {
    // Vérifiez les données POST
    var_dump($_POST);

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email)) {
        die("Erreur : Le champ email est vide.");
    }
    if (empty($password)) {
        die("Erreur : Le champ mot de passe est vide.");
    }

    
    var_dump($password); // Vérifiez si $password est modifié après l'inclusion

    // Vérification de l'utilisateur
    $requete = $bdd->prepare('SELECT * FROM horizon.utlisateur WHERE email_utilisateur = :email');
    $requete->execute([':email' => $email]);
    $resultat = $requete->fetch();

    var_dump($resultat);
    var_dump($password); // Vérifiez si $password est modifié aprés l'inclusion

    if (!$resultat) {
        $message = "Merci de saisir une adresse email valide.";
    } elseif ($resultat['validation_email_utilisateur'] == 0) {

        require_once "./include/token.php";
        var_dump($resultat);

        var_dump($password); // Vérifiez si $password est modifié après l'inclusion

        // Mise à jour du token
        $update = $bdd->prepare('UPDATE horizon.utlisateur SET token_utilisateur = :token WHERE email_utilisateur = :email');
        $update->execute([
            ':token' => $token,
            ':email' => $email
        ]);

        require_once ('./include/envoimail/sendmail.php');
        var_dump($password); // Vérifiez si $password est modifié après l'inclusion
        var_dump($resultat);
    } else {
        // Débogage
        echo "Mot de passe saisi : " . $password . "<br>";
        echo "Mot de passe haché dans la BDD : " . $resultat['password_utilisateur'] . "<br>";
        echo "Résultat de password_verify : " . (password_verify($password, $resultat['password_utilisateur']) ? 'true' : 'false') . "<br>";
        var_dump($resultat);
        if (password_verify($password, $resultat['password_utilisateur'])) {
            // L'utilisateur est authentifié
            $_SESSION['id_utilisateur'] = $resultat['id_utilisateur'];
            $_SESSION['username'] = $resultat["username"];
            $_SESSION['email_utilisateur'] = $resultat["email_utilisateur"];
            $_SESSION['role_utilisateur'] = $resultat["role_utilisateur"];

            header('Location: index.php');
            exit();
        } else {
            $message = "Veuillez saisir un mot de passe valide.";
        }
    }
    // cette ligne à la fin du fichier pour vider le tampon :
    ob_end_flush(); // Envoie le contenu du tampon au navigateur
}
require_once ('./include/header_login.php');


// ============CODE PHP 2023 =========================

// if (isset($_POST['connexion'])) {
//     $email = $_POST['email'];
//     $password = $_POST['password'];

//     require_once('./include/bdd.php'); 

//     $requete = $bdd->prepare('SELECT * FROM horizon.utlisateur WHERE email_utilisateur = :email');
//     $requete->execute(array(':email' => $email));
//     $resultat = $requete->fetch();

//     if (!$resultat) {
//         $message = "Mercis de saisir une adtresse email valide.";
//     }elseif ($resultat['validation_email_utilisateur'] == 0) {

//         require_once "./include/token.php"; 

//         $update = $bdd->prepare('UPDATE horizon.utlisateur SET token_utilisateur =:token WHERE email =:email');
//         $update->bindValue(':token', $token);
//         $update->bindValue(':email', $_POST['email']);  
//         $update->execute();

//         require_once ('./include/envoimail/sendmail.php');
//     }else{
//         $passwordISOk = password_verify($password, $resultat['password_utilisateur']);
//         if($passwordISOk){
//             session_start();
//             $_SESSION['id_utilisateur'] = $resultat['id_utilisateur'];
//             $_SESSION['username'] = $resultat["username"];
//             $_SESSION['email_utilisateur'] = $resultat["email_utilisateur"];
//             $_SESSION['role_utilisateur'] = $resultat["role_utilisateur"];

//             header('Location: index.php');
//         }else{
//             $message = "Veuillez saisir un mot de passe valide.";
//         }

//     }




// =============SUGGESTION CODE CODEIUM ==================================

    // $requete = $bdd->prepare('SELECT * FROM horizon.utlisateur WHERE email_utilisateur = :email');
    // $requete->bindValue(':email', $email, PDO::PARAM_STR);
    // $requete->execute();
    // $resultat = $requete->fetch();
    // if ($resultat) {
    //     if (password_verify($password, $resultat['password_utilisateur'])) {
    //         $_SESSION['id'] = $resultat['id_utilisateur'];
    //         $_SESSION['prenom'] = $resultat['prenom_utilisateur'];
    //         $_SESSION['nom'] = $resultat['nom_utilisateur'];
    //         $_SESSION['username'] = $resultat['username'];
    //         $_SESSION['email'] = $resultat['email_utilisateur'];
    //         $_SESSION['photo'] = $resultat['photo_utilisateur'];
    //         header('Location: index.php');  
    //     } else {
    //         $message = "Mauvais mot de passe.";
    //     }
    // } else {
    //     $message = "Cet email n'existe pas.";   
    //     }


    // }

// require_once ('include/header_login.php');
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
                                        <?php if (isset($message)) : ?>
                                            <p class="text-center text-danger" >
                                                <?php echo $message ?>
                                            </p>
                                        </div>
                                        <?php endif ?>
                                        <h3 class="text-center font-weight-light my-4">connexion</h3></div>
                                    <div class="card-body">
                                        <form action="login.php" method="post">
                                            <div class="form-floating mb-3">
                                                <input class="form-control" name="email" id="inputEmail" type="email" placeholder="name@example.com" />
                                                <label for="inputEmail">Email adresse</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" name="password" id="inputPassword" type="password" required />
                                                <label for="inputPassword">Mot de passe</label>
                                            </div>
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" id="inputRememberPassword" type="checkbox" value="" />
                                                <label class="form-check-label" for="inputRememberPassword">Se souvenir de moi</label>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <a class="small" href="password.php">Mot de passe oublie?</a>
                                                <!-- <a class="btn btn-primary" href="index.html">connexion</a> -->
                                                <input type="submit" value="connecter" name="connexion" class="btn btn-primary">
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-footer text-center py-3">
                                        <div class="small"><a href="register.php">Avez-vous un compte? Enregistrer-vous!</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
 <?php
 require_once ('include/footer.php');
 ?>