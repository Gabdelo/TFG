<?php
session_start();
require '../includes/conexionDB.php';

$conn = conectar();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

$nombre = $_POST['nombre'];
$email = $_POST['email'];
$ciudad = $_POST['ciudad'];
$modalidad = $_POST['modalidad'];
$tipo_usuario = $_POST['tipo_usuario'];
$tipo_intercambio = $_POST['tipo_intercambio'];
$disponibilidad = $_POST['disponibilidad'];
// Carpeta donde se guardarán las fotos
$carpetaSubida = "../uploads/";

// Comprobar si se subió archivo
if(isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {

    $archivoTmp = $_FILES['foto_perfil']['tmp_name'];
    $nombreArchivo = basename($_FILES['foto_perfil']['name']);

    // Generar nombre único para evitar sobreescrituras
    $nombreArchivo = uniqid() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "", $nombreArchivo);

    $rutaDestino = $carpetaSubida . $nombreArchivo;

    // Mover el archivo a la carpeta uploads
    if(move_uploaded_file($archivoTmp, $rutaDestino)) {
        // Guardar el nombre de archivo en la base de datos
        $stmtFoto = $conn->prepare("UPDATE usuarios SET foto_perfil = ? WHERE id_usuario = ?");
        $stmtFoto->bind_param("si", $nombreArchivo, $id_usuario);
        $stmtFoto->execute();

        // Actualizar la sesión
        $_SESSION['foto_perfil'] = $nombreArchivo;
    }
}

/* =========================
   1️⃣ Ajustar tablas según tipo_usuario
========================= */

// Si cambia a buscar → borrar lo que ofrece
if ($tipo_usuario == "buscar") {
    $stmtDelete = $conn->prepare("DELETE FROM usuario_ofrece WHERE id_usuario = ?");
    $stmtDelete->bind_param("i", $id_usuario);
    $stmtDelete->execute();
}

// Si cambia a ofrecer → borrar lo que busca
if ($tipo_usuario == "ofrecer") {
    $stmtDelete = $conn->prepare("DELETE FROM usuario_busca WHERE id_usuario = ?");
    $stmtDelete->bind_param("i", $id_usuario);
    $stmtDelete->execute();
}

// Si es "ambos" → no se borra nada

/* =========================
   2️⃣ Actualizar datos del usuario
========================= */

$sql = "UPDATE usuarios SET 
        nombre = ?, 
        email = ?, 
        ciudad = ?, 
        modalidad = ?, 
        tipo_usuario = ?, 
        tipo_intercambio = ?, 
        disponibilidad = ?
        WHERE id_usuario = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "sssssssi",
    $nombre,
    $email,
    $ciudad,
    $modalidad,
    $tipo_usuario,
    $tipo_intercambio,
    $disponibilidad,
    $id_usuario
);

$stmt->execute();
$_SESSION['nombre'] = $nombre;
$_SESSION['email']  = $email;
$_SESSION['ciudad'] = $ciudad;
$_SESSION['modalidad'] = $modalidad;
$_SESSION['tipo_usuario'] = $tipo_usuario;
$_SESSION['tipo_intercambio'] = $tipo_intercambio;
$_SESSION['disponibilidad'] = $disponibilidad;

/* =========================
   3️⃣ Redirigir
========================= */

header("Location: ../perfil.php?actualizado=1");
exit();
?>
