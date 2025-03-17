<?php
$dsn = 'mysql:dbname=horizon;host=localhost; charset=utf8';
$user = 'root';
$password = '';

try {
    $bdd = new PDO($dsn, $user, $password);
    // Activer les erreurs PDO
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // if($bdd){
    //     echo 'Connexion réussie';
    // }

} catch (PDOException $e) {
    echo 'Connexion échouée : ' . $e->getMessage();
}