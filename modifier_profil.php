<?php
session_start();
require_once 'include/bdd.php';
require_once 'include/header_login.php';

if (isset($_GET['modifier_compte']) && isset($_SESSION['id_utilisateur'])
    && $_SESSION['id_utilisateur'] == $_GET['modifier_compte']) {

    $id_utilisateur = $_SESSION['id_utilisateur'];

    // Récupérer les informations de l'utilisateur
    $requete = $bdd->prepare('SELECT * FROM horizon.utlisateur WHERE id_utilisateur = :id_utilisateur');
    $requete->bindValue(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
    $requete->execute();
    $ligne = $requete->fetch(PDO::FETCH_ASSOC);

    $photo_profil = $ligne['photo_utilisateur'] ?? '';
    $nom_utilisateur = $ligne['nom_utilisateur'] ?? '';
    $prenom_utilisateur = $ligne['prenom_utilisateur'] ?? '';
    $username = $ligne['username'] ?? '';

    // Vérification des champs du formulaire
    if (isset($_POST['modif_profil'])) {
        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $username = trim($_POST['username']);

        // Validation des champs
        if (empty($nom) || !ctype_alpha(str_replace(' ', '', $nom))) {
            $message = "Le nom ne doit contenir que des lettres.";
        } elseif (empty($prenom) || !ctype_alpha(str_replace(' ', '', $prenom))) {
            $message = "Le prénom ne doit contenir que des lettres.";
        } elseif (empty($username) || !ctype_alnum(str_replace(['_', '-'], '', $username))) {
            $message = "Le nom d'utilisateur ne doit contenir que des lettres, chiffres ou underscores.";
        } else {
            // Vérifier si le nom d'utilisateur existe déjà
            $requete = $bdd->prepare('SELECT * FROM horizon.utlisateur WHERE username = :username AND id_utilisateur != :id_utilisateur');
            $requete->bindValue(':username', $username, PDO::PARAM_STR);
            $requete->bindValue(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
            $requete->execute();
            $result = $requete->fetch();

            if ($result) {
                $message = "Le nom d'utilisateur saisi existe déjà. Merci de choisir un autre nom d'utilisateur.";
            } else {
                // Mettre à jour les informations de l'utilisateur
                $requete2 = $bdd->prepare('UPDATE horizon.utlisateur SET prenom_utilisateur = :prenom, nom_utilisateur = :nom, username = :username, photo_utilisateur = :photo WHERE id_utilisateur = :id_utilisateur');
                $requete2->bindValue(':prenom', $prenom, PDO::PARAM_STR);
                $requete2->bindValue(':nom', $nom, PDO::PARAM_STR);
                $requete2->bindValue(':username', $username, PDO::PARAM_STR);
                $requete2->bindValue(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);

                // Gestion de la photo de profil
                if (!empty($_FILES['photo_profil']['name'])) {
                    $typesAutorises = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                    $typeFichier = $_FILES['photo_profil']['type'];

                    if (in_array($typeFichier, $typesAutorises)) {
                        // Générer un token unique
                        require_once('./include/token.php');
                        // Générer un nom de fichier unique
                        $extensionFichier = pathinfo($_FILES['photo_profil']['name'], PATHINFO_EXTENSION);
                        $nouvelle_photo = $token . "." . $extensionFichier;

                        // Définir le chemin de téléchargement
                        $chemin = "./img/photo_profil/";

                        // Déplacer le fichier téléchargé
                        if (move_uploaded_file($_FILES['photo_profil']['tmp_name'], $chemin . $nouvelle_photo)) {
                            $requete2->bindValue(':photo', $nouvelle_photo, PDO::PARAM_STR);
                        } else {
                            $message = "Erreur lors du téléchargement du fichier.";
                        }
                    } else {
                        $message = "Le fichier doit être de type jpeg, jpg, png ou webp.";
                    }
                } else {
                    // Conserver l'ancienne photo
                    $requete2->bindValue(':photo', $photo_profil, PDO::PARAM_STR);
                }

                // Exécuter la requête
                if ($requete2->execute()) {
                    $success = "Modification effectuée avec succès.";
                    header('Location: profil.php');
                    exit();
                } else {
                    $message = "Erreur lors de la modification.";
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le compte</title>
</head>
<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4">Modifier votre compte</h3>
                                    <?php if (isset($message)) : ?>
                                        <p class="text-danger text-center"><?= $message; ?></p>
                                    <?php elseif (isset($success)) : ?>
                                        <p class="text-success text-center"><?= $success; ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <form action="" method="post" enctype="multipart/form-data">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3 mb-md-0">
                                                    <input class="form-control" id="inputFirstName" type="text" name="prenom" value="<?= htmlspecialchars($prenom_utilisateur) ?>" placeholder="Entrez votre prénom" />
                                                    <label for="inputFirstName">Prénom</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input class="form-control" id="inputLastName" type="text" name="nom" value="<?= htmlspecialchars($nom_utilisateur) ?>" placeholder="Entrez votre nom" />
                                                    <label for="inputLastName">Nom</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3 mb-md-0">
                                                    <input class="form-control" id="inputUsername" type="text" name="username" value="<?= htmlspecialchars($username) ?>" placeholder="Entrez votre nom d'utilisateur" />
                                                    <label for="inputUsername">Nom d'utilisateur</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-control">
                                                    <?php if (!empty($photo_profil)) : ?>
                                                        <img src="img/photo_profil/<?= htmlspecialchars($photo_profil) ?>" width="100" height="100" alt="Photo de profil">
                                                    <?php endif; ?>
                                                    <label for="photo">Photo de profil</label>
                                                    <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
                                                    <input id="photo" type="file" name="photo_profil" accept="image/*" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4 mb-0">
                                            <div class="d-grid">
                                                <input type="submit" class="btn btn-primary btn-block" name="modif_profil" value="Modifier mon profil">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>

<?php
}
require_once('include/footer_register.php');
?>