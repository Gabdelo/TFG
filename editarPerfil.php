<?php
session_start();
require 'includes/conexionDB.php'; // tu archivo de conexión a DB
$conn = conectar();

// Verificar sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Obtener datos del usuario
$sql = "SELECT * FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

if (!$usuario) {
    echo "Usuario no encontrado";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link type="text/css" rel="stylesheet" href="assets/css/perfil.css">
    <link type="text/css" rel="stylesheet" href="assets/css/global.css">
</head>
<body>

<div class="container mt-5 mb-5">
    <h3 class="mb-4">Editar perfil</h3>

    <form action="php/editarPerfilProceso.php" method="post" enctype="multipart/form-data">

        <!-- DATOS PERSONALES -->
        <h5 class="mb-3">Datos personales</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nombre o alias</label>
                <input type="text" name="nombre" class="form-control"
                       value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Correo electrónico</label>
                <input type="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($usuario['email']) ?>" required>
            </div>
        </div>

        <!-- UBICACIÓN -->
        <h5 class="mt-4 mb-3">Ubicación</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Ciudad</label>
                <input type="text" name="ciudad" class="form-control"
                       value="<?= htmlspecialchars($usuario['ciudad']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Modalidad</label>
                <select name="modalidad" class="form-select" required>
                    <option value="Presencial" <?= ($usuario['modalidad']=="Presencial")?'selected':'' ?>>Presencial</option>
                    <option value="Online" <?= ($usuario['modalidad']=="Online")?'selected':'' ?>>Online</option>
                    <option value="Ambas" <?= ($usuario['modalidad']=="Ambas")?'selected':'' ?>>Ambas</option>
                </select>
            </div>
        </div>

        <!-- TIPO USUARIO -->
        <h5 class="mt-4 mb-3">Tipo de participación</h5>
        <div class="mb-4">
            <select name="tipo_usuario" class="form-select" required>
                <option value="ofrecer" <?= ($usuario['tipo_usuario']=="ofrecer")?'selected':'' ?>>Solo ofrezco habilidades</option>
                <option value="buscar" <?= ($usuario['tipo_usuario']=="buscar")?'selected':'' ?>>Solo busco habilidades</option>
                <option value="ambos" <?= ($usuario['tipo_usuario']=="ambos")?'selected':'' ?>>Ofrezco y busco habilidades</option>
            </select>
        </div>

        <!-- PREFERENCIAS -->
        <h5 class="mt-4 mb-3">Preferencias de colaboración</h5>
        <div class="mb-3">
            <label class="form-label">Tipo de intercambio</label>
            <select name="tipo_intercambio" class="form-select" required>
                <option value="Puntual" <?= ($usuario['tipo_intercambio']=="Puntual")?'selected':'' ?>>Puntual</option>
                <option value="Proyecto" <?= ($usuario['tipo_intercambio']=="Proyecto")?'selected':'' ?>>Proyecto</option>
                <option value="Clases / mentoría" <?= ($usuario['tipo_intercambio']=="Clases / mentoría")?'selected':'' ?>>Clases / mentoría</option>
            </select>
        </div>

        <div class="mb-3">
    <label class="form-label">Foto de perfil</label>
    <input type="file" name="foto_perfil" class="form-control" accept="image/*">
    <?php if(!empty($usuario['foto_perfil'])): ?>
        <img src="uploads/<?php echo htmlspecialchars($usuario['foto_perfil']); ?>" alt="Foto de perfil" class="img-thumbnail mt-2" width="100">
    <?php endif; ?>
</div>

        <div class="mb-3">
            <label class="form-label">Disponibilidad</label>
            <textarea name="disponibilidad" class="form-control" rows="2"><?= htmlspecialchars($usuario['disponibilidad']) ?></textarea>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-success btn-lg">
                Guardar cambios
            </button>
        </div>

    </form>
</div>

</body>
</html>
