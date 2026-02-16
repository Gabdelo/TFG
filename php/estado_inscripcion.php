<?php
require '../includes/conexionDB.php'; // Ajusta la ruta si tu archivo está en otra carpeta
$conn = conectar();

session_start();

// Verificar si el usuario está logueado
if(!isset($_SESSION['id_usuario'])) {
    echo "Debes iniciar sesión.";
    exit;
}

$idUsuarioActual = $_SESSION['id_usuario'];

// Verificar que se recibieron los parámetros necesarios
if(!isset($_POST['id_oferta'], $_POST['id_usuario'], $_POST['accion'])) {
    echo "Datos incompletos.";
    exit;
}

$idOferta = (int)$_POST['id_oferta'];
$idUsuarioInscrito = (int)$_POST['id_usuario'];
$accion = $_POST['accion'];

// Validar acción permitida
$accionesPermitidas = ['Seleccionado', 'Rechazado'];
if(!in_array($accion, $accionesPermitidas)) {
    echo "Acción no válida.";
    exit;
}

// Opcional: verificar que la oferta realmente pertenece al usuario actual
$sqlVerificar = "SELECT * FROM ofertas_trabajo WHERE id_oferta = ? AND id_usuario = ?";
$stmtVer = $conn->prepare($sqlVerificar);
$stmtVer->bind_param("ii", $idOferta, $idUsuarioActual);
$stmtVer->execute();
$resVer = $stmtVer->get_result();

if($resVer->num_rows === 0) {
    echo "No tienes permiso para modificar esta oferta.";
    exit;
}
$stmtVer->close();

// Actualizar estado del usuario inscrito
$sqlUpdate = "UPDATE inscripciones_oferta SET estado = ? WHERE id_oferta = ? AND id_usuario = ?";
$stmtUpd = $conn->prepare($sqlUpdate);
$stmtUpd->bind_param("sii", $accion, $idOferta, $idUsuarioInscrito);

if($stmtUpd->execute()) {
    // Redirigir de nuevo a la página de ver inscritos
    header("Location: ../Ver_inscritos.php?id_oferta=" . $idOferta);
    exit;
} else {
    echo "Error al actualizar el estado: " . $stmtUpd->error;
}

$stmtUpd->close();
$conn->close();
?>
