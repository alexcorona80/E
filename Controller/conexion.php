<?php
$host = "localhost";
$db = "sistema";
$usuario = "root";
$password = "";

try {
    $conexion = new PDO("mysql:host=$host;dbname=$db",$usuario,$password);
    
} catch (Exception $ex) {
     echo $ex->getMessage();
}

?>

