<?php
require 'includes/conexionDB.php';
$conn = conectar();
session_start();

$foto1 = !empty($_SESSION['foto_perfil'])
    ? 'uploads/' . $_SESSION['foto_perfil']
    : 'assets/img/default-avatar.png';

// Verificamos que venga un id
if (!isset($_GET['id'])) {
    header("Location: menu.php");
    exit;
}

$id_usuario = intval($_GET['id']);

if (isset($_SESSION['id_usuario']) && $_SESSION['id_usuario'] == $id_usuario) {
    header("Location: perfil.php");
    exit;
}

// Obtener datos del usuario
$sqlUsuario = "SELECT * FROM usuarios WHERE id_usuario = ?";
$stmtUsuario = $conn->prepare($sqlUsuario);
$stmtUsuario->bind_param("i", $id_usuario);
$stmtUsuario->execute();
$usuario = $stmtUsuario->get_result()->fetch_assoc();
$stmtUsuario->close();

if (!$usuario) {
    echo "Usuario no encontrado";
    exit;
}

// Obtener seguidores y seguidos
$stmtCount = $conn->prepare(
    "SELECT 
        (SELECT COUNT(*) FROM usuario_seguidores WHERE id_usuario = ?) AS seguidores,
        (SELECT COUNT(*) FROM usuario_seguidores WHERE id_seguidor = ?) AS siguiendo"
);
$stmtCount->bind_param("ii", $id_usuario, $id_usuario);
$stmtCount->execute();
$datosFollow = $stmtCount->get_result()->fetch_assoc();
$stmtCount->close();

// Obtener proyectos del usuario
$sqlProyectos = "SELECT * FROM proyectos 
                 WHERE id_usuario = ? 
                 ORDER BY fecha_creacion DESC";
$stmtProy = $conn->prepare($sqlProyectos);
$stmtProy->bind_param("i", $id_usuario);
$stmtProy->execute();
$resultadoProyectos = $stmtProy->get_result();
$stmtProy->close();

// Verificar si el usuario logueado ya sigue a este perfil
$yaLoSigo = false;
if (isset($_SESSION['id_usuario'])) {
    $idLogueado = $_SESSION['id_usuario'];

    $stmtFollow = $conn->prepare(
        "SELECT 1 FROM usuario_seguidores 
         WHERE id_usuario = ? AND id_seguidor = ?"
    );
    $stmtFollow->bind_param("ii", $id_usuario, $idLogueado);
    $stmtFollow->execute();
    $stmtFollow->store_result();

    if ($stmtFollow->num_rows > 0) {
        $yaLoSigo = true;
    }
    $stmtFollow->close();
}
$sqlSeguidores = "SELECT COUNT(*) AS total 
                  FROM usuario_seguidores 
                  WHERE id_usuario = ?";

$stmtSeguidores = $conn->prepare($sqlSeguidores);
$stmtSeguidores->bind_param("i", $idUsuario);
$stmtSeguidores->execute();
$resSeguidores = $stmtSeguidores->get_result();
$totalSeguidores = $resSeguidores->fetch_assoc()['total'];


$sqlSeguidos = "SELECT COUNT(*) AS total 
                FROM usuario_seguidores 
                WHERE id_seguidor = ?";

$stmtSeguidos = $conn->prepare($sqlSeguidos);
$stmtSeguidos->bind_param("i", $idUsuario);
$stmtSeguidos->execute();
$resSeguidos = $stmtSeguidos->get_result();
$totalSeguidos = $resSeguidos->fetch_assoc()['total'];


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" href="assets/img/nexusIcon.png" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--bootstrap 5 CSS-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!--bootstrap 5 icons-->
    <link type="text/css" rel="stylesheet" href="assets/css/perfil.css">
    <link type="text/css" rel="stylesheet" href="assets/css/global.css">
    <title>Perfil</title>
</head>

<body>
    <nav class="navbar-custom">
        <div class="nav-container">

            <!-- LOGO -->
            <a href="menu.php" class="nav-logo">
                <img src="./assets/img/nexusIcon.png" alt="Logo">
            </a>

            <!-- MENÚ CENTRADO -->
            <ul class="nav-menu">

                <li>
                    <a href="menu.php">
                        <i class="bi bi-house-fill"></i>
                        <span>Inicio</span>
                    </a>
                </li>

                <li>
                    <a href="VerOfertas.php">
                        <i class="bi bi-briefcase-fill"></i>
                        <span>Empleos</span>
                    </a>
                </li>
                <li>
                    <a href="nexum.php">
                        <i class="bi bi-diagram-3-fill"></i>
                        <span>NEXUM</span>
                    </a>
                </li>

                <li>
                    <a href="mensajes.php">
                        <i class="bi bi-chat-dots-fill"></i>
                        <span>Chats</span>
                    </a>
                </li>

                <li>
                    <a href="perfil.php" class="nav-profile">
                        <div class="profile-pic">
                            <img src="<?php echo $foto1; ?>" alt="">
                        </div>
                        <span>Perfil</span>
                    </a>
                </li>

            </ul>

        </div>
    </nav>

    <main class="">
        <div class="container perfil-container">

            <div class="row g-4">

                <!-- COLUMNA PRINCIPAL -->
                <div class="col-lg-8">
                    <div class="perfil-card p-4">

                        <div class="row align-items-center">
                            <div class="col-md-3 text-center">
                                <?php
                                $foto = !empty($usuario['foto_perfil'])
                                    ? 'uploads/' . $usuario['foto_perfil']
                                    : 'assets/img/default-avatar.png';
                                ?>
                                <div class="perfil-foto" style="background-image: url('<?php echo $foto; ?>');"></div>
                            </div>
                            <div class="col-md-9">
                                <div class="d-flex justify-content-end gap-2 mb-2">
                                </div>
                                <h1 class="perfil-nombre">
                                    <?php echo htmlspecialchars($usuario['nombre']); ?>
                                </h1>
                                <p class="perfil-ubicacion">
                                    <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($usuario['ciudad']); ?> · Online
                                </p>
                                <p class="mt-2 mb-1">
                                    <strong><?php echo $datosFollow['seguidores']; ?></strong> seguidores ·
                                    <strong><?php echo $datosFollow['siguiendo']; ?></strong> siguiendo
                                </p>
                                <span class="badge bg-warning text-dark">
                                    <?php echo htmlspecialchars($usuario['tipo_usuario']); ?>
                                </span>
                            </div>
                            <?php if (isset($_SESSION['id_usuario'])): ?>
                                <form method="POST" action="php/seguir.php">
                                    <input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>">

                                    <?php if ($yaLoSigo): ?>
                                        <button type="submit" name="accion" value="dejar"
                                            class="mt-4 btn btn-outline-danger btn-sm">
                                            Dejar de seguir
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" name="accion" value="seguir" class="mt-4 btn btn-primary btn-sm">
                                            Seguir
                                        </button>
                                    <?php endif; ?>
                                </form>
                            <?php endif; ?>
                            <a href="mensajes.php?id=<?php echo $usuario['id_usuario']; ?>"
                                class="btn btn-primary mt-3">
                                Enviar mensaje
                            </a>

                        </div>

                    </div>

                    <!-- HABILIDADES -->
<!-- HABILIDADES -->
<div class="perfil-card p-4 mt-4">
    <h4><i class="bi bi-lightning-charge"></i> Habilidades que ofrece</h4>

    <?php
    // Preparar la consulta de habilidades
    $sqlHabilidades = "
        SELECT h.nombre AS habilidad, n.nombre AS nivel
        FROM usuario_ofrece uo
        JOIN habilidades h ON uo.id_habilidad = h.id_habilidad
        JOIN niveles n ON uo.id_nivel = n.id_nivel
        WHERE uo.id_usuario = ?
        ORDER BY n.id_nivel DESC
    ";

    $stmt = $conn->prepare($sqlHabilidades);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();

    // Asociar resultados
    $stmt->bind_result($habilidad, $nivel);

    $tieneHabilidades = false;

    while ($stmt->fetch()) {
        $tieneHabilidades = true;

        // Por defecto clase 'basico'
        $nivelClase = 'basico';

        if (!empty($nivel)) {
            $nivelTexto = strtolower($nivel);
            $nivelTexto = str_replace('á','a',$nivelTexto); // básico -> basico
            switch ($nivelTexto) {
                case 'avanzado':
                    $nivelClase = 'avanzado';
                    break;
                case 'intermedio':
                    $nivelClase = 'intermedio';
                    break;
                default:
                    $nivelClase = 'basico';
            }
        }
    ?>
        <div class="habilidad-item mt-3">
            <div class="d-flex justify-content-between">
                <span><?php echo htmlspecialchars($habilidad); ?></span>
                <span class="nivel"><?php echo htmlspecialchars($nivel); ?></span>
            </div>
            <div class="barra-nivel <?php echo $nivelClase; ?>"></div>
        </div>
    <?php
    }

    if (!$tieneHabilidades) {
        echo "<p>Este usuario no ha registrado habilidades aún.</p>";
    }

    $stmt->close();
    ?>
</div>

                    <!-- PREFERENCIAS -->
                    <div class="perfil-card p-4 mt-4">
                        <h4><i class="bi bi-clock"></i> Preferencias</h4>
                        <p><strong>Tipo de intercambio:</strong> <?php echo htmlspecialchars($usuario['tipo_intercambio']); ?></p>
                        <p><strong>Disponibilidad:</strong> <?php echo htmlspecialchars($usuario['disponibilidad']); ?></p>
                    </div>

                    <div class="perfil-card p-4 mt-4">
                        <h4><i class="bi bi-folder"></i> Proyectos / Publicaciones</h4>

                        <?php if ($resultadoProyectos->num_rows > 0): ?>

    <?php while ($proyecto = $resultadoProyectos->fetch_assoc()): ?>
        <div class="border rounded p-3 mt-3">
            <h5><?php echo htmlspecialchars($proyecto['titulo']); ?></h5>

            <p class="mb-1">
                <?php echo htmlspecialchars($proyecto['descripcion']); ?>
            </p>

            <small class="text-muted">
                <?php echo $proyecto['tipo_proyecto']; ?> ·
                <?php echo $proyecto['modalidad']; ?>
            </small>

            <div class="mt-2">
                <strong>Rol:</strong> <?php echo htmlspecialchars($proyecto['rol']); ?>
            </div>

            <?php if (!empty($proyecto['enlace'])): ?>
                <div class="mt-2">
                    <a href="<?php echo $proyecto['enlace']; ?>" target="_blank">
                        Ver proyecto
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>

<?php else: ?>
    <p class="text-muted mt-3">Aún no has publicado ningún proyecto.</p>
<?php endif; ?>
                    </div>

                </div>

                <!-- COLUMNA LATERAL -->
                <div class="col-lg-4">
                    <div class="perfil-card p-4">
                        <h4>Sobre mí</h4>
                        <p>
                            Usuario interesado en colaborar en proyectos tecnológicos
                            y creativos. Busco aprender mientras comparto conocimientos.
                        </p>
                    </div>

                    <div class="perfil-card p-4 mt-4">
                        <h5>Estado</h5>
                        <p><span class="estado-activo"></span> Disponible para intercambios</p>
                    </div>
                </div>

            </div>

        </div>
    </main>

    <footer class="footer mt-0 pt-5 pb-4">
        <div class="container">
            <div class="row gy-4">

                <!-- LOGO Y DESCRIPCIÓN -->
                <div class="col-lg-4 col-md-6">
                    <h4 class="footer-logo">NEXUM</h4>
                    <p class="footer-text">
                        Plataforma para intercambiar habilidades y colaborar en
                        proyectos tecnológicos y creativos.
                    </p>
                    <div class="social-icons mt-3">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-twitter-x"></i></a>
                        <a href="#"><i class="bi bi-linkedin"></i></a>
                        <a href="#"><i class="bi bi-github"></i></a>
                    </div>
                </div>

                <!-- ENLACES RÁPIDOS -->
                <div class="col-lg-2 col-md-6">
                    <h6 class="footer-title">Plataforma</h6>
                    <ul class="footer-links">
                        <li><a href="#">Inicio</a></li>
                        <li><a href="#">Explorar</a></li>
                        <li><a href="#">Proyectos</a></li>
                        <li><a href="#">Contacto</a></li>
                    </ul>
                </div>

                <!-- RECURSOS -->
                <div class="col-lg-3 col-md-6">
                    <h6 class="footer-title">Recursos</h6>
                    <ul class="footer-links">
                        <li><a href="#">Centro de ayuda</a></li>
                        <li><a href="#">Guía de uso</a></li>
                        <li><a href="#">Política de privacidad</a></li>
                        <li><a href="#">Términos y condiciones</a></li>
                    </ul>
                </div>

                <!-- CONTACTO -->
                <div class="col-lg-3 col-md-6">
                    <h6 class="footer-title">Contacto</h6>
                    
                    <p class="footer-text mb-1">
                        <i class="bi bi-geo-alt"></i> Madrid, España
                    </p>
                    <p class="footer-text">
                        <i class="bi bi-telephone"></i> +34 600 000 000
                    </p>
                </div>

            </div>

            <hr class="footer-divider">

            <div class="text-center small">
                © 2026 NEXUM. Todos los derechos reservados.
            </div>
        </div>
    </footer>


</body>


</html>