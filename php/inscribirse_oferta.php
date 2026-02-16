<?php
require '../includes/conexionDB.php';
$conn = conectar();
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

if(isset($_POST['accion'])) {
    $idUsuario = $_SESSION['id_usuario'];
    $idOferta = intval($_POST['id_oferta']);

    // Comprobar si ya está inscrito
    $sqlCheck = "SELECT * FROM inscripciones_oferta WHERE id_oferta = ? AND id_usuario = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("ii", $idOferta, $idUsuario);
    $stmtCheck->execute();
    $resCheck = $stmtCheck->get_result();

    if($resCheck->num_rows === 0) {
        // No está inscrito → Insertar
        $sqlInsert = "INSERT INTO inscripciones_oferta (id_oferta, id_usuario) VALUES (?, ?)";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("ii", $idOferta, $idUsuario);
        $stmtInsert->execute();
    } else {
        // Ya estaba inscrito → Eliminar (cancelar)
        $sqlDelete = "DELETE FROM inscripciones_oferta WHERE id_oferta = ? AND id_usuario = ?";
        $stmtDelete = $conn->prepare($sqlDelete);
        $stmtDelete->bind_param("ii", $idOferta, $idUsuario);
        $stmtDelete->execute();
    }

    header("Location: ../VerOfertas.php");
    exit;
}
?>
