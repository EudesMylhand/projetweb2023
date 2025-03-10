<?php
session_start();

if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
    // L'utilisateur a confirmé la déconnexion
    session_unset();
    session_destroy();
    header("Location: index.php"); // Redirige vers la page d'accueil
    exit();
}

if (!empty($_SESSION)) { 
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Êtes-vous sûr de vouloir vous déconnecter ?',
            text: 'Vous serez redirigé vers la page d’accueil.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, me déconnecter',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '?confirm=yes'; // Recharge la page avec le paramètre pour détruire la session
            } else {
                window.location.href = 'index.php'; // Redirige vers la page d'accueil si l'utilisateur annule
            }
        });
    });
    </script>";
} else {
    echo "Vous n'êtes pas connecté";
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Vous êtes déconnecté',
            text: 'Appuyer sur Ok pour retourner sur la page de connexion.',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'login.php';
        });
    });
    </script>";
}


// ===============================CODE PHP 2023=================================================
// if (!empty($_SESSION)){
//     // session_unset();

//     echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
//     echo "<script>
//     Swal.fire({
//         title: 'Êtes-vous sûr de vouloir vous déconnecter ?',
//         text: 'Vous serez redirigé vers la page d’accueil.',
//         icon: 'warning',
//         showCancelButton: true,
//         confirmButtonColor: '#d33',
//         cancelButtonColor: '#3085d6',
//         confirmButtonText: 'Oui, me déconnecter',
//         cancelButtonText: 'Annuler'
//     }).then((result) => {
//         if (result.isConfirmed) {
//             window.location.href = 'logout.php';
//         }
//     });
//     </script>";
//             // header('Location: index.php');
//     // session_destroy();
//     exit();
// } 

?>
