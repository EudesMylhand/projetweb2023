<?php
session_start();
require_once 'include/bdd.php';
require_once 'include/header_login.php';

if ($_SESSION['id_utilisateur']) {

//Requete preparee
    // $id_utilisateur = $_SESSION['id_utilisateur'];
    // $requete = $bdd->prepare('SELECT * FROM horizon.utlisateur WHERE id_utilisateur = :id_utilisateur');
    // $requete->bindValue(':id_utilisateur', $id_utilisateur);
    // $requete->execute();
    // $resultat = $requete->fetch();

// Requete simple Autre syntaxe
    $id_utilisateur = $_SESSION['id_utilisateur'];
    $requete = "SELECT * FROM horizon.utlisateur WHERE id_utilisateur = $id_utilisateur";
    $result = $bdd->query($requete);

    $ligne = $result->fetch(PDO::FETCH_ASSOC);

    $photo_profil = $ligne['photo_utilisateur'];
    $nom = $ligne['nom_utilisateur'];
    $prenom = $ligne['prenom_utilisateur'];
    $email = $ligne['email_utilisateur'];
    $username = $ligne['username'];
    $password = $ligne['password_utilisateur'];
    $role = $ligne['role_utilisateur'];



}else{

echo "script type=\"text/javascript\">
    alert('l'utilisateur n'est pas connecté.');
    document.location='login.php';
    </script>";

    // echo "Vous n'êtes pas connecté";
    // echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    // echo "<script>
    // document.addEventListener('DOMContentLoaded', function() {
    //     Swal.fire({
    //         title: 'Vous êtes déconnecté',
    //         text: 'Appuyer sur Ok pour retourner sur la page de connexion.',
    //         icon: 'warning',
    //         confirmButtonText: 'OK'
    //     }).then(() => {
    //         window.location.href = 'login.php';
    //     });
    // });
    // </script>";
}

?>