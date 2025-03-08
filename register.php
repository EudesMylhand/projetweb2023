<?php
    require_once('include/header_register.php');
?>

<?php
if (isset($_POST['inscription'])) {

    require_once('./include/bdd.php');

    $nom = trim($_POST['nom']);   
    $prenom = trim($_POST['prenom']); 
    $username = trim($_POST['username']); 
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);


    // Verification des champs
    if (empty($_POST['nom']) || !preg_match("/^[\p{L}\s'-]+$/u", $_POST['nom'])) {
        $message = "Le nom ne doit contenir que des lettres.";
    } elseif (empty($_POST['prenom']) || !preg_match("/^[\p{L}\s'-]+$/u", $_POST['prenom'])) {
        $message = "Le prénom ne doit contenir que des lettres.";
    } elseif (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $message = "Veuillez entrer un email valide.";
    } elseif (empty($_POST['username']) || !preg_match("/^[a-zA-Z0-9_]+$/", $_POST['username'])) {
        $message = "Le nom d'utilisateur ne doit contenir que des lettres, chiffres ou underscores.";
    } elseif (empty($_POST['password']) || $_POST['password'] !== $_POST['confirmpassword']) {
        $message = "Les mots de passe ne correspondent pas.";
    } else {
        // Gestion du fichier uploadé

        // // Image par défaut
        // $photo_profil = 'avatar_default.png';
        // // Dossier de stockage
        // $path = "./img/photo_profil/";

        // if (!empty($_FILES['photo_profil']['name']) && $_FILES['photo_profil']['error'] === 0) {
        //     $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        //     $file_mime = mime_content_type($_FILES['photo_profil']['tmp_name']);

        //     if (in_array($file_mime, $allowed_types)) {
        //         $photo_profil = uniqid() . "_" . basename($_FILES['photo_profil']['name']);
        //         move_uploaded_file($_FILES['photo_profil']['tmp_name'], $path . $photo_profil);
        //     } else {
        //         $message = "La photo doit être en jpg, jpeg, png ou webp.";
        //     }
        // }


        // if (!empty($_FILES['photo_profil']['name'])) {
        //     if ($_FILES['photo_profil']['error'] === 0) {
        //         $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        
        //         $finfo = finfo_open(FILEINFO_MIME_TYPE);
        //         $file_mime = finfo_file($finfo, $_FILES['photo_profil']['tmp_name']);
        //         finfo_close($finfo);
        
        //         if (in_array($file_mime, $allowed_types)) {
        //             $path = "./img/photo_profil/";
        
        //             if (!is_dir($path)) {
        //                 mkdir($path, 0777, true);
        //             }
        
        //             $photo_profil = uniqid() . "_" . basename($_FILES['photo_profil']['name']);
        //             $full_path = $path . $photo_profil;
        
        //             if (move_uploaded_file($_FILES['photo_profil']['tmp_name'], $full_path)) {
        //                 echo " Fichier uploadé avec succès : " . $full_path;
        //             } else {
        //                 echo " Erreur lors du déplacement du fichier.";
        //             }
        //         } else {
        //             echo " Type de fichier non autorisé : " . $file_mime;
        //         }
        //     } else {
        //         echo " Erreur d'upload : " . $_FILES['photo_profil']['error'];
        //     }

        //     var_dump($_FILES['photo_profil']);
        // }

        // echo '<pre>';
        // var_dump($_FILES['photo_profil']);
        // var_dump($_FILES);
        // var_dump($photo_profil);
        // var_dump($full_path);
        // echo '===========Avant upload===============';
        // echo '</pre>';
   
                // Gestion du fichier uploader image
                $photo_profil = 'avatar_default.png';

                if (!empty($_FILES['photo_profil']['name'])) {
                    if ($_FILES['photo_profil']['error'] === 0) {
                        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $file_mime = finfo_file($finfo, $_FILES['photo_profil']['tmp_name']);
                        finfo_close($finfo);
        
                        if (in_array($file_mime, $allowed_types)) {
                            $path = "./img/photo_profil/";
                            if (!is_dir($path)) {
                                mkdir($path, 0777, true);
                            }
        
                            $photo_profil = uniqid() . "_" . basename($_FILES['photo_profil']['name']);
                            $full_path = $path . $photo_profil;
        
                            if (move_uploaded_file($_FILES['photo_profil']['tmp_name'], $full_path)) {
                                // echo " Fichier uploadé avec succès : " . $full_path;
                                // $success = "Fichier uploadé avec succès : " . $full_path;
                            } else {
                                // echo " Erreur lors du déplacement du fichier.";
                                $message = "Erreur lors du déplacement du fichier.";
                            }
                        } else {
                            // echo " Type de fichier non autorisé : " . $file_mime;
                            $message = "Type de fichier non autorisé : " . $file_mime;
                        }
                    } else {
                        // echo " Erreur d'upload : " . $_FILES['photo_profil']['error'];
                        $message = "Erreur d'upload : " . $_FILES['photo_profil']['error'];
                    }
                }
        

                // echo '<pre>';
                // var_dump($_FILES['photo_profil']);
                // var_dump($_FILES);
                // var_dump($photo_profil);
                // var_dump($full_path);
                // echo '===========Après upload===============';

                // echo '</pre>';

        // Hashage du mot de passe
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // $password_hash = ($_POST['password']);


        // Gestion des doubles usernames
        $requete = $bdd->prepare('SELECT * FROM horizon.utlisateur WHERE username = :username');
        $requete->bindValue(':username', $_POST['username'], PDO::PARAM_STR);
        $requete->execute();
        $resultat1 = $requete->fetch();

        // Gestion des doubles Email
        $requete = $bdd->prepare('SELECT * FROM horizon.utlisateur WHERE email_utilisateur = :email');
        $requete->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
        $requete->execute();
        $resultat2 = $requete->fetch();
        

        if($resultat1) {
            $message = "Ce nom d'utilisateur est deja utilisé.";
        } elseif ($resultat2) {
            $message = "Cet email est deja lier  à un compte.";
        }else{

            // appel de la fonction token
            require_once('./include/token.php');

                // Insertion dans la BDD
            $requete = $bdd->prepare('INSERT INTO horizon.utlisateur (
                prenom_utilisateur, nom_utilisateur, username, email_utilisateur, 
                password_utilisateur, token_utilisateur, photo_utilisateur
            ) VALUES (:prenom, :nom, :username, :email, :password, :token, :photo)');

            $requete->bindValue(':nom', $_POST['nom'], PDO::PARAM_STR);
            $requete->bindValue(':prenom', $_POST['prenom'], PDO::PARAM_STR);
            $requete->bindValue(':username', $_POST['username'], PDO::PARAM_STR);
            $requete->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
            $requete->bindValue(':password', $password_hash, PDO::PARAM_STR);
             // Génération d'un token sécurisé
            //  $requete->bindValue(':token', bin2hex(random_bytes(16)), PDO::PARAM_STR);
            $requete->bindValue(':token', $token, PDO::PARAM_STR);
            $requete->bindValue(':photo', $photo_profil, PDO::PARAM_STR);

            if ($requete->execute()) {
                $success = "Inscription réussie !";
            } else {
                $message = "Erreur lors de l'inscription.";
            }
            require_once('./include/envoimail/sendmail.php');
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
                            <div class="col-lg-8">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header">
                                        <h3 class="text-center font-weight-light my-4">Creer un compte</h3>
                                        <?php if(isset($message)) : ?>
                                            <p class="text-danger text-center"><?= $message; ?> </p>
                                        <?php elseif(isset($success)) : ?> 
                                            <p class="text-success text-center">
                                                <?= $success; ?> 
                                                <br>
                                                <?= $successMail ;?>
                                            </p>
                                         <?php endif; ?>
                                    </div>
                                    <div class="card-body">
                                        <form action="register.php" method="post" enctype="multipart/form-data">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3 mb-md-0">
                                                        <input class="form-control" id="inputFirstName" type="text" name="prenom" placeholder="Enter your first name" />
                                                        <label for="inputFirstName">Prénom</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input class="form-control" id="inputLastName" type="text" name="nom" placeholder="Enter your last name" />
                                                        <label for="inputLastName">Nom</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="inputEmail" type="email" name="email" placeholder="name@example.com" />
                                                <label for="inputEmail">Email address</label>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3 mb-md-0">
                                                        <input class="form-control" id="inputPassword" type="password" name="password" placeholder="Create a password" />
                                                        <label for="inputPassword">Password</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3 mb-md-0">
                                                        <input class="form-control" id="inputPasswordConfirm" type="password" name="confirmpassword" placeholder="Confirm password" />
                                                        <label for="inputPasswordConfirm">Confirmer Password</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3 mb-md-0">
                                                        <input class="form-control" id="inputFirstName" type="text" name="username" placeholder="Enter your first name" />
                                                        <label for="inputFirstName">Nom d'utilisateur</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-control">
                                                        <label for="photo">Photo de profil</label>
                                                        <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
                                                        <input id="photo" type="file" name="photo_profil" accept="image/*" /> 
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-4 mb-0">
                                                <div class="d-grid"><input type="submit" class="btn btn-primary btn-block"  name="inscription" value="creer un compte" href="login.html"></div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-footer text-center py-3">
                                        <div class="small"><a href="login.php">Avez vous un compte? Connectez-vous</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>





<?php
    require_once('include/footer_register.php');
?>