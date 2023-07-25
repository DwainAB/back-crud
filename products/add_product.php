<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
require_once '../config/Database.php';

$response = array();

if (!empty($_POST['reference']) && !empty($_POST['description']) && !empty($_POST['priceTaxeIncluse']) && !empty($_POST['priceTaxeExcluse']) && !empty($_POST['quantity'])) {
    $reference = $_POST['reference'];
    $description = $_POST['description'];
    $priceTaxeIncluse = $_POST['priceTaxeIncluse'];
    $priceTaxeExcluse = $_POST['priceTaxeExcluse'];
    $quantity = $_POST['quantity'];

    // Récupérer les informations sur l'image
    $image = $_FILES['image'];
    $imageFileName = $image['name'];
    $imageTempPath = $image['tmp_name'];

    // Récupérer les informations de la langue du produit depuis le formulaire
    $langue_produit = $_POST['langue'];

    // Valider les données d'entrée
    $reference = filter_var($reference, FILTER_SANITIZE_SPECIAL_CHARS);
    $description = filter_var($description, FILTER_SANITIZE_SPECIAL_CHARS);
    $priceTaxeIncluse = filter_var($priceTaxeIncluse, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $priceTaxeExcluse = filter_var($priceTaxeExcluse, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $quantity = filter_var($quantity, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    if ($reference && $description && $priceTaxeIncluse && $priceTaxeExcluse && $quantity !== false) {
        // Échapper les données de sortie
        $reference = htmlspecialchars($reference);
        $description = htmlspecialchars($description);
        $priceTaxeIncluse = floatval($priceTaxeIncluse);
        $priceTaxeExcluse = floatval($priceTaxeExcluse);
        $quantity = floatval($quantity);

        $uploadDirectory = '../images/';
        $imageFilePath = $uploadDirectory . $imageFileName;

        if (move_uploaded_file($imageTempPath, $imageFilePath)) {
            // Commencer la transaction
            mysqli_begin_transaction($con);

            try {
                // Insérer les informations du produit dans la table "Products"
                $requete = $con->prepare('INSERT INTO products (reference, description, priceTaxIncl, priceTaxExcl, quantity, image) VALUES (?, ?, ?, ?, ?, ?)');
                $requete->bind_param('ssddss', $reference, $description, $priceTaxeIncluse, $priceTaxeExcluse, $quantity, $imageFilePath);

                if ($requete->execute()) {
                    // Récupérer l'ID du produit inséré dans la table "Products"
                    $id_produit = mysqli_insert_id($con);

                    // Insérer les informations de langue du produit dans la table "Language"
                    $requete_langue = $con->prepare('INSERT INTO language (language, idProduct) VALUES (?, ?)');
                    $requete_langue->bind_param('si', $langue_produit, $id_produit);
                    $requete_langue->execute();

                    // Valider la transaction
                    mysqli_commit($con);

                    $response['error'] = false;
                    $response['message'] = "Nouveau produit ajouté avec succès !";
                } else {
                    $response['error'] = true;
                    $response['message'] = "Le produit n'a pas pu être ajouté";
                }
            } catch (Exception $e) {
                // En cas d'erreur, annuler la transaction
                mysqli_rollback($con);

                $response['error'] = true;
                $response['message'] = "Une erreur s'est produite lors de l'ajout du produit : " . $e->getMessage();
            }
        } else {
            $response['error'] = true;
            $response['message'] = "Une erreur s'est produite lors du téléchargement de l'image";
        }
    } else {
        $response['error'] = true;
        $response['message'] = "Les données saisies sont invalides.";
    }
} else {
    $response['error'] = true;
    $response['message'] = "Veuillez renseigner les informations.";
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>
