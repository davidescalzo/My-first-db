<?php

$pdo = new PDO("mysql:host=localhost;port=3306;dbname=products_crud", "root", ""); //creo una istanza della classe PDO
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //SE TROVA ERRORE FARà UNA ECCEZIONE

$id = $_POST["id"] ?? null ; //usiamo la super goblale POST, se l'id non esiste avremo null

if (!$id) { // se l'id non esiste torneremo ad index
  header("Location: index.php ");
  exit;
}

$statement = $pdo-> prepare("DELETE FROM products WHERE id = :id"); //prepare PDO con comando in mysql
$statement -> bindValue(":id" , $id); //bind paramentro con valore
$statement -> execute();
 
header("Location: index.php");

?>