<?php

        if(isset($_POST['inscription'])) {

            if(empty($_POST['nom']) || !ctype_alpha($_POST['nom']) ) {
                $message = "Le nom doit contenir que des lettres alphabetiques";
            }elseif(empty($_POST['prenom']) || !ctype_alpha($_POST['prenom'])) {
                $message = "Le prenom doit contenir que des lettres alphabetiques";
            }elseif(empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $message = "Renter un Email non valide";
            }elseif(empty($_POST['username']) || !ctype_alpha($_POST['username'])) {
                $message = "username doit contenir que des lettres alphabetiques";
            }elseif(empty($_POST['password']) || empty($_POST['confirmpassword']) || $_POST['password'] != $_POST['confirmpassword']) {
                $message = "Renter un mot de passe non valide";
             }else {
              
                // if(preg_match("#jpg|jpeg|png|webp#", $_FILES['photo_profil']['type'])) {
                //     $path = "./img/photo_profil/";
                //     $photo_profil = basename($_FILES['photo_profil']['name']); // Sécurisation du nom du fichier
                //     move_uploaded_file($_FILES['photo_profil']['tmp_name'],
                //      $path.$_FILES['photo_profil']['tmp_name']);
                // }else{
                //     $message = "La photo doit être de type jpg, jpeg, png ou webp";
                // }

                if (!empty($_FILES['photo_profil']['name']) && $_FILES['photo_profil']['error'] === 0) {
                    // Vérifier que le type MIME est bien défini
                    if (isset($_FILES['photo_profil']['type']) && preg_match("#jpg|jpeg|png|webp#", $_FILES['photo_profil']['type'])) {
                        $path = "./img/photo_profil/";
                        $photo_profil = uniqid() . "_" . basename($_FILES['photo_profil']['name']); // Nom unique
                        move_uploaded_file($_FILES['photo_profil']['tmp_name'], $path . $photo_profil);
                    } else {
                        $message = "La photo doit être de type jpg, jpeg, png ou webp.";
                    }
                } else {
                    $photo_profil = 'avatar_default.png'; // Image par défaut si aucun fichier n'est envoyé
                }
                

                require_once('./include/bdd.php');
        
                $requete = $bdd->prepare('INSERT INTO horizon.utlisateur(
                    prenom_utilisateur, nom_utilisateur, username, email_utilisateur, 
                    password_utilisateur, token_utilisateur, photo_utilisateur) 
                    VALUES(:prenom, :nom, :username, :email, :password, :token, :photo)');
                
                $requete->bindvalue(':nom', $_POST['nom'], PDO::PARAM_STR);
                $requete->bindvalue(':prenom', $_POST['prenom'], PDO::PARAM_STR);
                $requete->bindvalue(':username', $_POST['username'], PDO::PARAM_STR);
                $requete->bindvalue(':email', $_POST['email'], PDO::PARAM_STR);
                $requete->bindvalue(':password', $_POST['password'], PDO::PARAM_STR);
                $requete->bindvalue(':token', 'aaa', PDO::PARAM_STR);
                $requete->bindvalue(':photo', $photo_profil, PDO::PARAM_STR);

                    if(empty($_FILES['photo_profil']['name'])) {	
                        $photo_profil = 'avatar_default.png';
                        $requete->bindvalue(':photo_profil', $photo_profil, );
                    }else{
                        $requete->bindvalue(':photo_profil', $_FILES['photo_profil']['name']);
                    }
                
                    $requete->execute();
                }
        }

        
          ?>
