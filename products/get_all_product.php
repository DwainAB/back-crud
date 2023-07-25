<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
require_once '../config/Database.php';

$response = array();
$query = $con->prepare("SELECT * FROM products");

if($query->execute()){

  $product = array();
  $result = $query->get_result();

  while($element = $result->fetch_array(MYSQLI_ASSOC)){
    $product[] = $element;
  };

  $response['error'] = false;
  $response['product'] = $product;
  $response['message'] = "La commande a été éxécuté avec succès";
  $result_json = json_encode($response, JSON_UNESCAPED_UNICODE);
  $query -> close();

}else{
  $response['error'] = true;
  $response['message'] = "Impossible d'executer cette requette";
}

echo ($result_json)

?>
