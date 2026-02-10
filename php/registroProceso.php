<?php
require '../includes/conexionDB.php';

$conn = conectar();

/* =========================
   1. RECOGER DATOS
========================= */
$nombre   = trim($_POST['nombre']);
$email    = trim($_POST['email']);
$password = $_POST['password'];
$confirm  = $_POST['password_confirm'];

$ciudad    = $_POST['ciudad'] ?? null;
$modalidad = $_POST['modalidad'] ?? null;
$tipo      = $_POST['tipo_usuario'];
$disponibilidad = $_POST['disponibilidad'] ?? null;
$tipo_inter = $_POST['tipo_intercambio'] ?? null;

/* =========================
   2. VALIDACIONES BÁSICAS
========================= */
if ($password !== $confirm) {
    header("Location: ../public/register.php?error=password");
    exit;
}

if (empty($nombre) || empty($email) || empty($password) || empty($tipo)) {
    header("Location: ../public/register.php?error=empty");
    exit;
}

/* =========================
   3. COMPROBAR EMAIL DUPLICADO
========================= */
$stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    header("Location: ../public/register.php?error=email");
    exit;
}
$stmt->close();

/* =========================
   4. INSERTAR USUARIO
========================= */
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "INSERT INTO usuarios
    (nombre, email, password, ciudad, modalidad, tipo_usuario, disponibilidad, tipo_intercambio)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "ssssssss",
    $nombre,
    $email,
    $hash,
    $ciudad,
    $modalidad,
    $tipo,
    $disponibilidad,
    $tipo_inter
);

$stmt->execute();
$idUsuario = $stmt->insert_id;
$stmt->close();

/* =========================
   5. HABILIDADES QUE OFRECE
========================= */
if ($tipo === 'ofrecer' || $tipo === 'ambos') {
    if (!empty($_POST['habilidad_ofrece']) && !empty($_POST['nivel_ofrece'])) {

        $idHabilidad = (int) $_POST['habilidad_ofrece'];
        $idNivel     = (int) $_POST['nivel_ofrece'];

        $stmt = $conn->prepare(
            "INSERT INTO usuario_ofrece
            (id_usuario, id_habilidad, id_nivel)
            VALUES (?, ?, ?)"
        );

        $stmt->bind_param("iii", $idUsuario, $idHabilidad, $idNivel);
        $stmt->execute();
        $stmt->close();
    }
}

/* =========================
   6. HABILIDADES QUE BUSCA
========================= */
if ($tipo === 'buscar' || $tipo === 'ambos') {
    if (!empty($_POST['habilidad_busca'])) {

        $idHabilidadBusca = (int) $_POST['habilidad_busca'];

        $stmt = $conn->prepare(
            "INSERT INTO usuario_busca
            (id_usuario, id_habilidad)
            VALUES (?, ?)"
        );

        $stmt->bind_param("ii", $idUsuario, $idHabilidadBusca);
        $stmt->execute();
        $stmt->close();
    }
}

/* =========================
   7. FINAL
========================= */
$conn->close();
header("Location: ../index.html");
exit;

