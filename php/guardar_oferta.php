<?php
require '../includes/conexionDB.php';
$conn = conectar();

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['crear_oferta'])) {

    $id_usuario = $_SESSION['id_usuario'];
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $tipo_contrato = $_POST['tipo_contrato'];
    $modalidad = $_POST['modalidad'];
    $ubicacion = $_POST['ubicacion'];
    $salario = $_POST['salario'];
    $experiencia = $_POST['experiencia'];

    // Insertar oferta
    $stmt = $conn->prepare("
        INSERT INTO ofertas_trabajo 
        (id_usuario, titulo, descripcion, tipo_contrato, modalidad, ubicacion, salario, experiencia) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "isssssss",
        $id_usuario,
        $titulo,
        $descripcion,
        $tipo_contrato,
        $modalidad,
        $ubicacion,
        $salario,
        $experiencia
    );

    $stmt->execute();

    $id_oferta = $conn->insert_id;

    // Insertar habilidades relacionadas
    if (isset($_POST['habilidades'])) {
        foreach ($_POST['habilidades'] as $id_habilidad) {
            $stmtHab = $conn->prepare("
                INSERT INTO oferta_habilidad (id_oferta, id_habilidad) 
                VALUES (?, ?)
            ");
            $stmtHab->bind_param("ii", $id_oferta, $id_habilidad);
            $stmtHab->execute();
        }
    }

    header("Location: ../perfil.php");
    exit;
}
?>
