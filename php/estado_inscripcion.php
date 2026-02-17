<?php
require '../includes/conexionDB.php';
$conn = conectar();

session_start();

// Verificar si el usuario está logueado
if(!isset($_SESSION['id_usuario'])) {
    echo "Debes iniciar sesión.";
    exit;
}

$idUsuarioActual = $_SESSION['id_usuario'];

// Verificar datos recibidos
if(!isset($_POST['id_oferta'], $_POST['id_usuario'], $_POST['accion'])) {
    echo "Datos incompletos.";
    exit;
}

$idOferta = (int)$_POST['id_oferta'];
$idUsuarioInscrito = (int)$_POST['id_usuario'];
$accion = $_POST['accion'];

// Validar acción
$accionesPermitidas = ['Seleccionado', 'Rechazado'];
if(!in_array($accion, $accionesPermitidas)) {
    echo "Acción no válida.";
    exit;
}

// Verificar que la oferta pertenece al usuario actual
$sqlVerificar = "SELECT titulo FROM ofertas_trabajo WHERE id_oferta = ? AND id_usuario = ?";
$stmtVer = $conn->prepare($sqlVerificar);
$stmtVer->bind_param("ii", $idOferta, $idUsuarioActual);
$stmtVer->execute();
$resVer = $stmtVer->get_result();

if($resVer->num_rows === 0) {
    echo "No tienes permiso para modificar esta oferta.";
    exit;
}

$oferta = $resVer->fetch_assoc();
$tituloOferta = $oferta['titulo'];
$stmtVer->close();

// Actualizar estado del usuario inscrito
$sqlUpdate = "UPDATE inscripciones_oferta 
              SET estado = ? 
              WHERE id_oferta = ? AND id_usuario = ?";
$stmtUpd = $conn->prepare($sqlUpdate);
$stmtUpd->bind_param("sii", $accion, $idOferta, $idUsuarioInscrito);

if($stmtUpd->execute()) {

    // Crear mensaje automático
    if($accion === 'Seleccionado') {
        $mensaje = "¡Enhorabuena! Has sido seleccionado para la oferta \"$tituloOferta\". Pronto nos pondremos en contacto contigo.";
    } else {
        $mensaje = "Gracias por tu interés en la oferta \"$tituloOferta\". En esta ocasión no has sido seleccionado.";
    }

    $sqlMensaje = "INSERT INTO mensajes (id_emisor, id_receptor, mensaje) 
                   VALUES (?, ?, ?)";
    $stmtMsg = $conn->prepare($sqlMensaje);
    $stmtMsg->bind_param("iis", $idUsuarioActual, $idUsuarioInscrito, $mensaje);
    $stmtMsg->execute();
    $stmtMsg->close();

    // Redirigir
    header("Location: ../Ver_inscritos.php?id_oferta=" . $idOferta);
    exit;

} else {
    echo "Error al actualizar el estado: " . $stmtUpd->error;
}

$stmtUpd->close();
$conn->close();
?>
