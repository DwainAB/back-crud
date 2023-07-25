<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
require_once '../config/Database.php';

// Vérifier si la requête est de type POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idProduct = $_GET["id"];
    // Récupérer les données du formulaire
    $reference = $_POST["updatedReference"];
    $description = $_POST["updatedDescription"];
    $priceTaxIncl = $_POST["updatedPriceTaxeIncluse"];
    $priceTaxExcl = $_POST["updatedPriceTaxeExcluse"];

    // Vérifier si la connexion a réussi
    if (!$con) {
        http_response_code(500);
        echo json_encode(array("message" => "Erreur lors de la connexion à la base de données : " . mysqli_connect_error()));
        exit();
    }

    // Construire la requête SQL de mise à jour
    $sql = "UPDATE products SET reference='$reference', description='$description', priceTaxIncl=$priceTaxIncl, priceTaxExcl=$priceTaxExcl WHERE idProduct=$idProduct";

    // Exécuter la requête SQL
    if (mysqli_query($con, $sql)) {
        // Répondre avec un statut HTTP 200 (OK) pour indiquer que la mise à jour a réussi
        http_response_code(200);
        echo json_encode(array("message" => "Le produit a été mis à jour avec succès."));
    } else {
        // Répondre avec un statut HTTP 500 (Erreur interne du serveur) et un message d'erreur
        http_response_code(500);
        echo json_encode(array("message" => "Erreur lors de la mise à jour du produit : " . mysqli_error($con)));
    }

    // Fermer la connexion à la base de données
    mysqli_close($con);
}
?>
