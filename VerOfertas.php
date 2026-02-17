<?php
require 'includes/conexionDB.php'; // tu archivo de conexión a DB
$conn = conectar();

session_start(); // Siempre al inicio, antes de usar $_SESSION
if (isset($_SESSION['id_usuario'])) { // el nombre correcto según login
    $idUsuario = $_SESSION['id_usuario'];
    $nombre    = $_SESSION['nombre'];
    $foto = !empty($_SESSION['foto_perfil'])
        ? 'uploads/' . $_SESSION['foto_perfil']
        : 'assets/img/default-avatar.png';
        $ubicacion = $_SESSION['ciudad'];
} else {
    // Usuario no logueado
    $nombre = "Invitado";
    // opcional: redirigir al login
    // header("Location: login.php"); exit;
}

$busqueda = $_GET['busqueda'] ?? '';
$sql = '';

if (!empty($busqueda)) {

    $busqueda = $conn->real_escape_string($busqueda);

    $sql = "
    SELECT ot.*, u.nombre, u.foto_perfil
    FROM ofertas_trabajo ot
    JOIN usuarios u ON ot.id_usuario = u.id_usuario
    WHERE 
        ot.titulo LIKE '%$busqueda%' OR
        ot.descripcion LIKE '%$busqueda%' OR
        ot.ubicacion LIKE '%$busqueda%'
    ORDER BY ot.fecha_publicacion DESC
    ";
} else {

    $sql = "
    SELECT ot.*, u.nombre, u.foto_perfil
    FROM ofertas_trabajo ot
    JOIN usuarios u ON ot.id_usuario = u.id_usuario
    ORDER BY ot.fecha_publicacion DESC
    ";
}

$resultado = $conn->query($sql);

if (!$resultado) {
    die("Error en la consulta: " . $conn->error);
}
$sqlTopUsuarios = "
    SELECT u.id_usuario,
           u.nombre,
           u.foto_perfil,
           COUNT(us.id_seguidor) AS total_seguidores
    FROM usuarios u
    LEFT JOIN usuario_seguidores us 
        ON u.id_usuario = us.id_usuario
    GROUP BY u.id_usuario
    ORDER BY total_seguidores DESC
    LIMIT 4
";

$resultTopUsuarios = $conn->query($sqlTopUsuarios);
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/img/nexusIcon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> <!--bootstrap 5 CSS-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"><!--bootstrap 5 icons-->
    <link type="text/css" rel="stylesheet" href="assets/css/menu.css">
    <link type="text/css" rel="stylesheet" href="assets/css/global.css">
    <title>OFERTAS</title>
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
                            <img src="<?php echo $foto; ?>" alt="">
                        </div>
                        <span>Perfil</span>
                    </a>
                </li>

            </ul>

        </div>
    </nav>


    <main class="container-fluid mt-5 pt-5">
        <div class="container py-4">
            <div class="row">

                <!-- 🔹 COLUMNA IZQUIERDA -->
                <aside class="col-lg-3 d-none d-lg-block">
                    <div class="side-card profile-card p-4 mb-4 text-center">

                        <div class="profile-avatar-wrapper">
                            <img src="<?php echo $foto; ?>" class="profile-avatar" alt="Foto de perfil">
                            <span class="status-dot"></span>
                        </div>

                        <h5 class="mt-3 mb-1"><?php echo "" . $nombre . "" ?></h5>
                        <p class="profile-role"><?php echo "" . $ubicacion . "" ?></p>

                        <div class="profile-stats mt-3">
                            <div>
                                <strong><?php echo $totalSeguidores; ?></strong></strong>
                                <span>Seguidores</span>
                            </div>
                            <div>
                                <strong><?php echo $totalSeguidos; ?></strong></strong>
                                <span>Seguidos</span>
                            </div>
                        </div>

                        <a href="perfil.php" class="btn btn-sm btn-nexum mt-3 w-100">
                            Ver perfil
                        </a>

                    </div>
                    

                    <div class="side-card p-3">
                        <h5>Tendencias</h5>
                        <p>#DesarrolloWeb</p>
                        <p>#UXUI</p>
                        <p>#Startups</p>
                    </div>
                </aside>


                <!-- 🔹 COLUMNA CENTRAL -->
                <section class="col-lg-6">

                    <!-- 🔍 BUSCADOR -->
                    <div class="search-box p-3 mb-4">
                        <form method="GET" action="VerOfertas.php">
                            <input
                                type="text"
                                name="busqueda"
                                class="form-control"
                                placeholder="Buscar ofertas..."
                                value="<?php echo isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : ''; ?>">
                        </form>
                    </div>

                    <?php while ($fila = $resultado->fetch_assoc()) {

                        $fotoPost = !empty($fila['foto_perfil'])
                            ? "uploads/" . $fila['foto_perfil']
                            : "assets/img/default-avatar.png";

                        $idUsuarioActual = $_SESSION['id_usuario'] ?? 0;

                        // Comprobar si el usuario ya está inscrito
                        $sqlCheck = "SELECT * FROM inscripciones_oferta WHERE id_oferta = ? AND id_usuario = ?";
                        $stmtCheck = $conn->prepare($sqlCheck);
                        $stmtCheck->bind_param("ii", $fila['id_oferta'], $idUsuarioActual);
                        $stmtCheck->execute();
                        $resCheck = $stmtCheck->get_result();
                        $yaInscrito = $resCheck->num_rows > 0;
                    ?>

                        <div class="post-card p-4 mb-4">

                            <!-- Cabecera clickeable -->
                            <a href="VerPerfil.php?id=<?php echo $fila['id_usuario']; ?>" style="text-decoration:none; color:inherit;">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="mini-avatar" style="background-image: url('<?php echo htmlspecialchars($fotoPost); ?>');"></div>
                                    <div class="ms-3">
                                        <strong><?php echo htmlspecialchars($fila['nombre']); ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo date("d M Y", strtotime($fila['fecha_publicacion'])); ?></small>
                                    </div>
                                </div>
                            </a>

                            <h5><?php echo htmlspecialchars($fila['titulo']); ?></h5>
                            <p><?php echo htmlspecialchars($fila['descripcion']); ?></p>

                            <div class="mt-2">
                                <span class="badge bg-warning text-dark"><?php echo htmlspecialchars($fila['tipo_contrato']); ?></span>
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($fila['modalidad']); ?></span>
                                <?php if (!empty($fila['ubicacion'])): ?>
                                    <span class="badge bg-info text-dark"><?php echo htmlspecialchars($fila['ubicacion']); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($fila['experiencia'])): ?>
                                    <span class="badge bg-dark"><?php echo htmlspecialchars($fila['experiencia']); ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- Botón Inscribirse / Cancelar: solo si no es mi propia oferta -->
                            <?php
                            $tipoUsuarioActual = $_SESSION['tipo_usuario'] ?? '';

                            if ($fila['id_usuario'] != $idUsuarioActual && $tipoUsuarioActual != 'buscar'):
                            ?>
                                <div class="mt-3">
                                    <form method="post" action="php/inscribirse_oferta.php">
                                        <input type="hidden" name="id_oferta" value="<?php echo $fila['id_oferta']; ?>">
                                        <button type="submit" name="accion"
                                            class="btn <?php echo $yaInscrito ? 'btn-secondary' : 'btn-success'; ?> btn-sm">
                                            <?php echo $yaInscrito ? 'Inscrito' : 'Inscribirse'; ?>
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>

                        </div>

                    <?php } ?>
                  

                </section>


                                <!-- 🔹 COLUMNA DERECHA -->
<aside class="col-lg-3 d-none d-lg-block">

    <div class="side-card p-3 mb-4">
        <h5>Usuarios destacados</h5>

        <?php while ($user = $resultTopUsuarios->fetch_assoc()): 
            
            $fotoDestacado = !empty($user['foto_perfil'])
                ? 'uploads/' . $user['foto_perfil']
                : 'assets/img/default-avatar.png';
        ?>

            <div class="d-flex align-items-center mb-3">
                <img src="<?php echo $fotoDestacado; ?>" 
                     class="rounded-circle" 
                     width="40" height="40" 
                     style="object-fit: cover;">
                
                <div class="ms-2">
                    <strong><?php echo $user['nombre']; ?></strong><br>
                    <small class="text-muted">
                        <?php echo $user['total_seguidores']; ?> seguidores
                    </small>
                </div>
            </div>

        <?php endwhile; ?>
    </div>

    <div class="side-card p-3 mb-4">
        <h5>Consejo del día</h5>
        <p>Completa tu perfil al 100% para tener más visibilidad.</p>
    </div>

</aside>

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