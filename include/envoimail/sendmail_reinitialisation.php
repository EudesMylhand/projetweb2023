<?php
// POUR UTILISER PHPMAILER, IL FAUT INSTALLER LE FICHIER composer.json DANS LE Dossier PHPMailer-master
// POUR EVITE TOUTES COMPLICATIONS DE CHEMIN INSTALLER COMPOSER PUIS TAPER LA COMMANDE : composer require phpmailer/phpmailer
// AFIN QUE L'AUTOLOADER VENDOR RESOLVE LE CHEMIN DANS LE DOSSIER PARENT
/**
 * ETAPE 1 : INSTALLER COMPOSER GLOBALEMENT
 * ETAPE 2 : DANS LE DOSSIER PARENT :PS C:\wamp64\www\envoimail>
 * ETAPE 3 : TAPER LA COMMANDE : composer require phpmailer/phpmailer
 * ETAPE 4 :  REMPLACER LES require par: require __DIR__ . '/vendor/autoload.php';
 * 
 * 
 * 
 */


 use PHPMailer\PHPMailer\PHPMailer;
 use PHPMailer\PHPMailer\Exception;
 use PHPMailer\PHPMailer\SMTP;
 
 require __DIR__ . '/vendor/autoload.php';
 
 $mail = new PHPMailer(true);
 
 try {
     // 📌 Configuration du serveur SMTP
     $mail->isSMTP();
     $mail->Host       = 'smtp.gmail.com';
     $mail->SMTPAuth   = true;
     $mail->Username   = 'eudeskenpachi@gmail.com';
     $mail->Password   = 'yisyqisxphmumvup'; // ⚠️ A remplacer par une variable d’environnement
     $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
     $mail->Port       = 587;
     $mail->CharSet    = 'UTF-8';
     $mail->Encoding   = 'base64';
 
     // 📌 Vérification des données POST
     if (empty($_POST['email'])) {
         die("Erreur : L'email du destinataire est vide.");
     }
     //Vérification de email et nom
     $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
     $nom   = $_POST['nom'] ?? 'Utilisateur';
 
     if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
         die("Erreur : Adresse email invalide.");
     }
 
     // 📌 Définition de l'expéditeur et du destinataire
     $mail->setFrom('eudeskenpachi@gmail.com', 'projetWeb');
     $mail->addAddress($_POST['email'] ?? $email, $_POST['nom'] ?? $nom);
 
     // 📌 Génération du token
    //  $token = bin2hex(random_bytes(16)); // Token sécurisé
        require_once './include/token.php';
 
     // 📌 Contenu du mail
     $mail->isHTML(true);
     $mail->Subject = 'Réinitialisation du mot de passe';
     $mail->Body = '
         Afin de réinitialiser votre mot de passe. <br>
         Veuillez cliquer sur le lien suivant pour activer votre compte : <br>
         <a href="http://localhost/projetWeb/newpassword.php?token='.$token.'&email='.urlencode($email).'">réinitialisation de mot de passe</a>
     ';
 
     // 📌 Envoi du mail
     $mail->send();
    //  echo "Email envoyé avec succès à $email";
     $successMail ="Email envoyé avec succès à $email";
 } catch (Exception $e) {
    //  echo " Erreur lors de l'envoi de l'email : {$mail->ErrorInfo}";
     $message = " Erreur lors de l'envoi de l'email : {$mail->ErrorInfo}";
 }






























 //===============CODE PHP 2023 =================================
// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;
// use PHPMailer\PHPMailer\SMTP;


// require __DIR__ . '/vendor/autoload.php';


// $mail = new PHPMailer(true);

// $mail->isSMTP();                                   // spécifier que PHPMailer utilise le protocole SMTP
// $mail->Host       = 'smtp.gmail.com';                 // specifier l'adresse du serveur SMTP
// $mail->SMTPAuth   = true;                                   // activer l'authentification
// $mail->Username   = 'eudeskenpachi@gmail.com';                     // SMTP username
// $mail->Password   = 'yisyqisxphmumvup';                               // SMTP password
// $mail->SMTPSecure = 'tls';         // Activer le cryptage TLS ou SSL ; `PHPMailer::ENCRYPTION_SMTPS` également accepté
// $mail->Port       = 587;                                    // TCP port to connect to
// $mail->CharSet = 'UTF-8';
// $mail->Encoding = 'base64';
// $mail->setFrom('eudeskenpachi@gmail.com', 'projetWeb');
// $mail->addAddress($_POST['email'] ?? null, 'projetWeb' ?? null );     // Ajouter un destinataire
// $mail->isHTML(true);                                  // specifier que le format du message est HTML

// $chemin = require './include/token.php';

// if (!isset($_POST['email']) || empty($_POST['email'])) {
//     die("Erreur : L'email du destinataire est vide.");
// }

// $email = $_POST['email'];
// $nom = $_POST['nom'] ?? '';

// if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//     die("Erreur : Adresse email invalide.");
// }

// $mail->addAddress($email, $nom);


// // echo '<pre>';
// // echo var_dump($chemin);
// // echo print_r($chemin);
// // echo '</pre>';

// // $token = uniqid();

// $mail->Subject = 'Confirmation d\'email';
// $mail->Body = 'Merci de vous être inscrit sur notre site. <br>
//     Veuillez cliquer sur le lien suivant:
//     <a href="http://localhost/projetWeb/verification.php?token='.$token.'&email='.$_POST['email'].'">Confirmation email</a>';



// // 0 = off (for production use) - 1 = commands - 2 = commands and data
// $mail->SMTPDebug = 0; //Pour désactiver le mode debug

// if (!$mail->send()) {
//     $message = "Mail non envoyé";
//     echo 'Mailer Error: ' . $mail->ErrorInfo;
// } else {
//     $successMail = "Le message a été envoyé";
//     // echo $successMail;
// }

?>