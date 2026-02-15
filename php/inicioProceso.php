<?php
require '../includes/conexionDB.php';
require '../includes/auth.php';

$conn = conectar();

/* =========================
   1. RECOGER DATOS
========================= */
$email    = trim($_POST['email']);
$password = $_POST['password'];

/* =========================
   2. VALIDAR
========================= */
if (empty($email) || empty($password)) {
    header("Location: ../registro.hmtl");
    exit;
}

/* =========================
   3. BUSCAR USUARIO
========================= */
$stmt = $conn->prepare(
    "SELECT id_usuario, nombre, email, password, tipo_usuario, foto_perfil
     FROM usuarios
     WHERE email = ?"
);

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Email no existe
    header("Location: ../registro.html");
    exit;
}

$user = $result->fetch_assoc();
$stmt->close();

/* =========================
   4. VERIFICAR CONTRASEÑA
========================= */
if (!password_verify($password, $user['password'])) {
    header("Location: ../registro.html");
    exit;
}

/* =========================
   5. INICIAR SESIÓN
========================= */
loginUser($user);

/* =========================
   6. RECORDARME (opcional)
========================= */

/* =========================
   7. REDIRECCIÓN
========================= */
$conn->close();
header("Location: ../menu.php");
exit;
