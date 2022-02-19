<?php

header('Content-Type: application/json');

function error($mensaje){
$respuesta = array(
"tipo" => "error",
"msj" => $mensaje
);
return $respuesta;
}

function conectar(){
$usuario = "root";
$password = "";
$db = new PDO('mysql:host=localhost;dbname=colitas', 'root','');
return $db;
}

function listar(){
 $animales = array();
 try {
 $db = conectar();
 $sql = "SELECT * FROM fotos";
 $prepared_statement = $db->prepare($sql);
 $prepared_statement->execute();
 foreach ($prepared_statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
 $data = array(
 "id" => $row['id'],
 "nombre" => $row['nombre'],
 "especie" => $row['especie'],
 "protectora" => $row['protectora'],
 "imagen" => base64_encode($row['imagen']),
 "telefono" => $row['telefono'],
 "url" => url("/fotos.php?id=".$row['id'])
 );
 array_push($animales, $data);
 }
} catch (PDOException $e) {
 return error($e->getMessage());
 }
 return $animales;
}

function url($segmento){
 if(isset($_SERVER['HTTPS'])){
 $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ?
"https" : "http";
 }
 else{
 $protocol = 'http';
 }
 return $protocol . "://" . $_SERVER['HTTP_HOST'] . $segmento;
}

function obtener($id){
$animal = array();
try {
$db = conectar();
$sql = "SELECT * FROM fotos WHERE id=:id";
$prepared_statement = $db->prepare($sql);
$prepared_statement->bindParam(':id', $id, PDO::PARAM_INT);
$prepared_statement->execute();
foreach ($prepared_statement->fetchAll(PDO::FETCH_ASSOC) as
$row) {
    $animal = array(
        "id" => $row['id'],
        "nombre" => $row['nombre'],
        "especie" => $row['especie'],
        "protectora" => $row['protectora'],
        "imagen" => base64_encode($row['imagen']),
        "telefono" => $row['telefono'],
        "url" => url("/fotos.php?id=".$row['id'])
        );
}
} catch (PDOException $e) {
return error($e->getMessage());
}
return $animal;
}


if(!$_GET['id']){
    echo json_encode(listar());
}
else{
    echo json_encode(obtener($_GET["id"]));
}

?>
