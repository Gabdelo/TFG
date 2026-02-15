<?php
require '../includes/conexionDB.php'; // ruta correcta
$conn = conectar();

session_start();
if(!isset($_SESSION['id_usuario'])){
    exit("No autorizado");
}

$id_emisor = $_SESSION['id_usuario'];
$id_receptor = isset($_POST['id_receptor']) ? (int)$_POST['id_receptor'] : 0;
$mensaje = isset($_POST['mensaje']) ? trim($_POST['mensaje']) : '';

if($id_receptor > 0 && !empty($mensaje)){
    $sql = "INSERT INTO mensajes (id_emisor, id_receptor, mensaje) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $id_emisor, $id_receptor, $mensaje);
    if($stmt->execute()){
        echo "ok";
    } else {
        echo "error";
    }
} else {
    echo "mensaje vacío o receptor no válido";
}
