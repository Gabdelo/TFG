<?php
session_start();
require '../includes/conexionDB.php'; // tu archivo de conexión a DB
$conn = conectar();

// ✅ Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.html');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// ✅ Recibir datos del formulario y sanitizar
$titulo = trim($_POST['titulo']);
$descripcion = trim($_POST['descripcion']);
$tipo_proyecto = $_POST['tipo_proyecto'] ?? null;
$rol = trim($_POST['rol']);
$modalidad = $_POST['modalidad'] ?? null;
$fecha_inicio = $_POST['fecha_inicio'] ?: null;
$fecha_fin = $_POST['fecha_fin'] ?: null;
$enlace = trim($_POST['enlace'] ?? '');

// ✅ Insertar proyecto en la tabla `proyectos`
$sql = "INSERT INTO proyectos 
    (id_usuario, titulo, descripcion, tipo_proyecto, rol, modalidad, fecha_inicio, fecha_fin, enlace)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "issssssss",
    $id_usuario,
    $titulo,
    $descripcion,
    $tipo_proyecto,
    $rol,
    $modalidad,
    $fecha_inicio,
    $fecha_fin,
    $enlace
);

if ($stmt->execute()) {
    echo "<script>
            alert('Proyecto subido correctamente');
            window.location.href='../perfil.php';
          </script>";
    exit;
} else {
    echo "Error al subir proyecto: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
