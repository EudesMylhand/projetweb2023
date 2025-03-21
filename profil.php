<?php
session_start();
require_once 'include/bdd.php';
require_once 'include/header_login.php';

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['id_utilisateur'])) {
    // Récupérer l'ID de l'utilisateur depuis la session
    $id_utilisateur = $_SESSION['id_utilisateur'];

    // Requête préparée pour récupérer les informations de l'utilisateur
    try {
        $requete = $bdd->prepare('SELECT * FROM horizon.utlisateur WHERE id_utilisateur = :id_utilisateur');
        $requete->bindValue(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);

        $requete->execute();
        $ligne = $requete->fetch(PDO::FETCH_ASSOC);

        // Vérifier si l'utilisateur existe dans la base de données
        if ($ligne) {
            $photo_profil = $ligne['photo_utilisateur'] ?? '';
            $nom_utilisateur = $ligne['nom_utilisateur'] ?? '';
            $prenom_utilisateur = $ligne['prenom_utilisateur'] ?? '';
            $email_utilisateur = $ligne['email_utilisateur'] ?? '';
            $username = $ligne['username'] ?? '';

            // Afficher ou utiliser les informations de l'utilisateur
        } else {
            echo "Utilisateur non trouvé dans la base de données.";
            exit;
        }
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
        exit;
    }

    // Supprimer un compte utilisateur
    if (isset($_POST['valide_supp_compte'])) {
        // $id_utilisateur = $_GET['supprimer_compte'];
        $requete = $bdd->prepare('DELETE FROM horizon.utlisateur WHERE id_utilisateur = :id_utilisateur');
        $requete->bindValue(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
        $result = $requete->execute();

        if ($result) {
            if ($_SESSION) {
                session_unset();
                session_destroy();
            }
            header('Location: index.php');
            exit;
        } else {
            $message = "Votre compte n'a pas été supprimé";
        }
    }
} else {
    // Utilisateur non connecté, afficher une alerte et rediriger

    // echo "<script type=\"text/javascript\">
    //     alert('L\\'utilisateur n\\'est pas connecté.');
    //     window.location.href = 'login.php';
    // </script>";

    // Utilisateur non connecté, afficher une alerte SweetAlert2 et rediriger
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
     document.addEventListener('DOMContentLoaded', function() {
         Swal.fire({
             title: 'Pour acceder au profil, vous devez vous connecter',
             text: 'Appuyer sur Ok pour retourner sur la page de connexion.',
             icon: 'warning',
             confirmButtonText: 'OK'
         }).then((result) => {
             if (result.isConfirmed) {
                 window.location.href = 'login.php';
             }
         });
     });
 </script>";

    exit(); // Arrêter l'exécution du script
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
                                </div>
                                <h3 class="text-center font-weight-light my-4">Profil</h3>

                                <div class="card-header">
                                    <!-- SYNTAXE 1 -->
                                    <!-- Image de profil -->
                                    <?php if (isset($photo_profil)) echo "<center>
                                     <img src='img/photo_profil/$photo_profil' class='media-object'
                                      width='150' height='200' alt='Image de profil' />
                                     </center>" ?>

                                    <!-- SYNTAXE 2 -->
                                    <!-- Image de profil -->
                                    <!-- <?php if (!empty($photo_profil)): ?>
                                            <img src="img/photo_profil/<?php echo htmlspecialchars($photo_profil); ?>" 
                                            width="150" height="200" alt="Image de profil" />
                                        <?php endif; ?> -->
                                </div>

                                <div class="card-body">

                                    <p><?php if (isset($id_utilisateur)) echo "ID :  $id_utilisateur" ?></p>
                                    <p><?php if (isset($nom_utilisateur)) echo "Nom :  $nom_utilisateur" ?></p>
                                    <p><?php if (isset($prenom_utilisateur)) echo "Prénom :  $prenom_utilisateur" ?></p>
                                    <p><?php if (isset($email_utilisateur)) echo "Email : $email_utilisateur" ?></p>
                                    <p><?php if (isset($username)) echo "Username : $username" ?></p>

                                    <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                        <?php
                                        if (isset($id_utilisateur)) {
                                            echo "<a class='small btn btn-danger'
                                                    href='profil.php?supprimer_compte=$id_utilisateur'>Supprimer mon compte</a>";

                                            echo "<a class='small btn btn-warning'
                                                    href='modifier_profil.php?modifier_compte=$id_utilisateur'>Modifiier mon compte</a>";
                                        }
                                        ?>
                                        <!-- <input type="submit" value="connecter" name="connexion" class="btn btn-primary"> -->
                                    </div>

                                </div>
                                <div class="card-footer text-center py-3">
                                    <!-- <div class="small"><a href="register.php">Avez-vous un compte? Enregistrer-vous!</a></div> -->
                                    <?php
                                    if (
                                        isset($_GET['supprimer_compte'])
                                        && isset($_SESSION['id_utilisateur'])
                                        && $_SESSION['id_utilisateur'] == $_GET['supprimer_compte']
                                    ) {
                                        echo "Voulez-vous vraiment supprimer votre compte ?";

                                        echo "<form action='profil.php' method='post'>
                                            <div class='d-group align-items-center justify-content-between mt-4 mb-0'>
                                            <input type='hidden' name='supprimer_compte' value='$_GET[supprimer_compte]'>
                                            <input type='submit' value='Oui Supprimer nom compte' name='valide_supp_compte' class='btn btn-danger btn-block'>
                                            </div>
                                            </form>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        </main>
    </div>
    <?php require_once 'include/footer.php'; ?>