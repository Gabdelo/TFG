<?php
require '../includes/conexionDB.php';
$conn = conectar();

session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

if(isset($_POST['subir_publicacion'])){
    $descripcion = $_POST['descripcion'];
    $imagen = null;

    // Carpeta para publicaciones
    $carpetaPublicaciones = '../uploads/publicaciones/';

    // Crear carpeta si no existe
    if(!is_dir($carpetaPublicaciones)){
        mkdir($carpetaPublicaciones, 0755, true);
    }

    // Subida de imagen
    if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0){
        $nombreArchivo = time().'_'.basename($_FILES['imagen']['name']);
        $destino = $carpetaPublicaciones . $nombreArchivo;
        if(move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)){
            $imagen = 'publicaciones/' . $nombreArchivo; // guardamos la ruta relativa dentro de uploads
        }
    }

    $stmt = $conn->prepare("INSERT INTO publicaciones (id_usuario, descripcion, imagen) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $id_usuario, $descripcion, $imagen);
    $stmt->execute();

    // Redirigir al perfil después de subir
    header("Location: ../perfil.php");
    exit;
}
?>
