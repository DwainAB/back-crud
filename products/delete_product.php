<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
require_once '../config/Database.php';

// Vérifier si l'ID du produit à supprimer est passé en paramètre
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Récupérer l'ID de la langue associée au produit
    $getLangQuery = "SELECT idProduct FROM language WHERE idProduct = $id";
    $langResult = $con->query($getLangQuery);
    if ($langResult->num_rows > 0) {
        // Supprimer le produit de la table language
        $deleteLangQuery = "DELETE FROM language WHERE idProduct = $id";
        if ($con->query($deleteLangQuery) !== TRUE) {
            echo "Erreur lors de la suppression de la langue : " . $con->error;
            $con->close();
            exit;
        }
    }

    // Supprimer le produit de la base de données
    $deleteProductQuery = "DELETE FROM products WHERE idProduct = $id";
    if ($con->query($deleteProductQuery) === TRUE) {
        echo "Le produit avec l'ID $id a été supprimé avec succès, ainsi que la langue associée.";
    } else {
        echo "Erreur lors de la suppression du produit : " . $con->error;
    }
} else {
    echo "ID de produit non spécifié ou invalide.";
}

$con->close();
?>
