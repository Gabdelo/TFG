<?php
require '../includes/conexionDB.php';
$conn = conectar();
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$idLogueado = $_SESSION['id_usuario'];
$idPerfil = intval($_POST['id_usuario']);
$accion = $_POST['accion'];

// Evitar que se siga a sí mismo
if ($idLogueado == $idPerfil) {
    header("Location: verperfil.php?id=$idPerfil");
    exit;
}

if ($accion === "seguir") {

    $stmt = $conn->prepare(
        "INSERT IGNORE INTO usuario_seguidores (id_usuario, id_seguidor) 
         VALUES (?, ?)"
    );
    $stmt->bind_param("ii", $idPerfil, $idLogueado);
    $stmt->execute();
    $stmt->close();

} elseif ($accion === "dejar") {

    $stmt = $conn->prepare(
        "DELETE FROM usuario_seguidores 
         WHERE id_usuario = ? AND id_seguidor = ?"
    );
    $stmt->bind_param("ii", $idPerfil, $idLogueado);
    $stmt->execute();
    $stmt->close();
}

header("Location: ../VerPerfil.php?id=$idPerfil");
exit;
