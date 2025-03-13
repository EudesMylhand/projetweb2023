<?php
if (isset($_POST['inscription'])) {

    require_once('./include/bdd.php');

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
        $photo_profil = 'avatar_default.png';
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
                        echo " Fichier uploadé avec succès : " . $full_path;
                    } else {
                        echo " Erreur lors du déplacement du fichier.";
                    }
                } else {
                    echo " Type de fichier non autorisé : " . $file_mime;
                }
            } else {
                echo " Erreur d'upload : " . $_FILES['photo_profil']['error'];
            }

            var_dump($_FILES['photo_profil']);
        }
        
        // Hashage du mot de passe
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);


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


        }

       
    

       
    }

}

// ================Reinitialisation du mot de passe========================
if (isset($_POST['forget_password'])) {

    if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $message = "Merci de saisir une adresse email.";
    }else{
        require_once('./include/bdd.php');
        $email = trim($_POST['email']);    
    $requete = $bdd->prepare('SELECT * FROM horizon.utlisateur WHERE email_utilisateur = :email');
    $requete->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
    $requete->execute(array(':email' => $email));
    $resultat = $requete->fetch();
    if ($resultat) {
        $token = bin2hex(random_bytes(50));
        $requete = $bdd->prepare('UPDATE horizon.utlisateur SET token = :token WHERE email_utilisateur = :email');
        $requete->execute(array(':token' => $token, ':email' => $email));
        $requete = $bdd->prepare('SELECT * FROM horizon.utlisateur WHERE email_utilisateur = :email');
        $requete->execute(array(':email' => $email));
        $resultat = $requete->fetch();
        $id = $resultat['id_utilisateur'];
        $token = $resultat['token'];
        $to = $email;
        $subject = "Reinitialisation du mot de passe";
        $message = "Merci de cliquer sur le lien suivant pour reinitialiser votre mot de passe : ";
        $message .= "<a href='http://localhost/Projet_Web/password_reset.php?id=$id&token=$token'>Reinitialiser le mot de passe</a>";
        mail($to, $subject, $message);
        header('Location: login.php');
    } else {
        $message = "Email introuvable";
    }
    }
    
}

?>
   
