<?php

require_once('include/header_password.php');

var_dump($_GET);
echo '<br>';
echo '===============================';
echo var_dump($_POST);

if ((isset($_GET['email']) && !empty($_GET['email'])) && 
(isset($_GET['token']) && !empty($_GET['token']))) {
    $email = $_GET['email'];
    $token = $_GET['token'];
    

    require_once('./include/bdd.php');

    $requete = $bdd->prepare('SELECT * FROM horizon.utlisateur WHERE email_utilisateur = :email AND token_utilisateur = :token');
    // $requete->execute([':email' => $email, ':token' => $token]);
    // $resultat = $requete->fetch();
    $requete->bindValue(':email', $email, PDO::PARAM_STR);
    $requete->bindValue(':token', $token);
    $requete->execute();

    $nombre = $requete->rowCount();
    if ($nombre != 1) {
        $message = "Le lien de réinitialisation est invalide ou a expiré.";
        header('Location: login.php');
        exit();
    } else {
        if (isset($_POST['valide_new_password'])) {
            // Vérifiez que les champs ne sont pas vides
            if (empty($_POST['new_password']) || empty($_POST['confirm_password'])) {
                $message = "Veuillez remplir tous les champs.";
            } elseif ($_POST['new_password'] != $_POST['confirm_password']) {
                $message = "Les mots de passe ne correspondent pas.";
            } else {
                // Les champs sont valides, procédez à la mise à jour du mot de passe
                $password = $_POST['new_password'];
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                $requete = $bdd->prepare('UPDATE horizon.utlisateur SET password_utilisateur = :password WHERE email_utilisateur = :email');
                $requete->bindValue(':password', $password_hash, PDO::PARAM_STR);
                $requete->bindValue(':email', $email, PDO::PARAM_STR);
                $requete->execute();
                // Autre syntaxe
                // $resultat = $requete->execute([':password' => $password_hash, ':email' => $email]);
                
                // Vérifiez si la mise à jour a réussi
                if ($requete->rowCount() == 1) {

                    // echo "script type=\"text/javascript\">
                    // alert('Votre mot de passe a bien été réinitialisé.');
                    // window.location='login.php';
                    // </script>";

                    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
                    echo '<script>
                            Swal.fire({
                                title: "Succès !",
                                text: "Votre mot de passe a bien été réinitialisé.",
                                icon: "success"
                            }).then(() => {
                                window.location.href = "login.php"; // Redirection vers la page de connexion
                            });
                          </script>';
                } else {
                    header('Location: password.php');
                    exit();
                }
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
                            <div class="col-lg-6">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header">
                                        <?php if (isset($message)) echo "<p class='text-center text-danger' >$message</p>" ?>
                                        <h3 class="text-center font-weight-light my-4">Réinitialisation du mot de passe</h3></div>
                                    <div class="card-body">
                                        <div class="small mb-3 text-muted">Saisissez votre adresse électronique et nous vous enverrons un lien pour réinitialiser votre mot de passe.</div>
                                        <form action="newpassword.php?email=<?= isset($email) ? htmlspecialchars($email) : '' ?>&token=<?= isset($token) ? htmlspecialchars($token) : '' ?>" method="post">
                                        <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3 mb-md-0">
                                                        <input class="form-control" id="inputPassword" type="password" name="new_password" placeholder="Create a password" />
                                                        <label for="inputPassword">Password</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3 mb-md-0">
                                                        <input class="form-control" id="inputPasswordConfirm" type="password" name="confirm_password" placeholder="Confirm password" />
                                                        <label for="inputPasswordConfirm">Confirmer Password</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <a class="small" href="login.php">connexion</a>
                                                <input type="submit" class="btn btn-primary" name="valide_new_password" value="valider" href="login.php" />    
                                            </div>
                                        </form>
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
           