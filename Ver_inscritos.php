
<?php
require 'includes/conexionDB.php';

 // tu archivo de conexión a DB
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
$idUsuarioActual = $_SESSION['id_usuario'];

// Comprobar que recibimos id_oferta
if(!isset($_GET['id_oferta'])) {
    echo "Oferta no especificada.";
    exit;
}

$idOferta = (int)$_GET['id_oferta'];

// =======================
// Obtener info de la oferta
// =======================
$sqlOferta = "SELECT o.*, u.nombre AS nombre_empresa, u.foto_perfil AS foto_empresa
              FROM ofertas_trabajo o
              JOIN usuarios u ON o.id_usuario = u.id_usuario
              WHERE o.id_oferta = ? AND o.id_usuario = ?";
$stmt = $conn->prepare($sqlOferta);
$stmt->bind_param("ii", $idOferta, $idUsuarioActual);
$stmt->execute();
$resOferta = $stmt->get_result();

if($resOferta->num_rows === 0) {
    echo "No tienes permiso para ver esta oferta o no existe.";
    exit;
}

$oferta = $resOferta->fetch_assoc();
$stmt->close();

// =======================
// Obtener usuarios inscritos
// =======================
$sqlInscritos = "SELECT i.id_usuario, i.estado, i.fecha_inscripcion,
                        u.nombre, u.foto_perfil
                 FROM inscripciones_oferta i
                 JOIN usuarios u ON i.id_usuario = u.id_usuario
                 WHERE i.id_oferta = ?
                 ORDER BY i.fecha_inscripcion ASC";
$stmt2 = $conn->prepare($sqlInscritos);
$stmt2->bind_param("i", $idOferta);
$stmt2->execute();
$resInscritos = $stmt2->get_result();
$inscritos = [];
while($fila = $resInscritos->fetch_assoc()) {
    $inscritos[] = $fila;
}
$stmt2->close();
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
    <title>Inscripciones</title>
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
                    <!-- 🔹 COLUMNA CENTRAL -->


    <!-- Contenedor general de la sección -->
    <div class="bg-white rounded shadow-sm p-4 w-100">

        <!-- Info de la oferta -->
        <div class="mb-4">
            <h3>Oferta: <?php echo htmlspecialchars($oferta['titulo']); ?></h3>
            <p><?php echo htmlspecialchars($oferta['descripcion']); ?></p>
            <p>
                <strong>Tipo de contrato:</strong> <?php echo $oferta['tipo_contrato']; ?> | 
                <strong>Modalidad:</strong> <?php echo $oferta['modalidad']; ?> |
                <strong>Ubicación:</strong> <?php echo $oferta['ubicacion']; ?>
            </p>
        </div>

        

        <!-- Lista de usuarios inscritos -->
        <div>
            <h4>Usuarios inscritos (<?php echo count($inscritos); ?>)</h4>

            <?php if(!empty($inscritos)): ?>
                <?php foreach($inscritos as $user): ?>
                    <div class="d-flex align-items-center mb-3 p-2 rounded bg-light">
                        <div style="width:50px; height:50px; border-radius:50%; overflow:hidden;">
                            <img src="<?php echo !empty($user['foto_perfil']) ? 'uploads/'.$user['foto_perfil'] : 'assets/img/default-avatar.png'; ?>" 
                                 alt="Foto" style="width:100%; height:100%; object-fit:cover;">
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <a href="VerPerfil.php?id=<?php echo $user['id_usuario']; ?>" class="fw-bold text-decoration-none" style="color: black;">
                                <?php echo htmlspecialchars($user['nombre']); ?>
                            </a>
                            <br>
                            <small class="text-muted">Inscrito el: <?php echo date("d M Y H:i", strtotime($user['fecha_inscripcion'])); ?></small>
                        </div>
                        <div class="ms-3">
                            <?php if($user['estado'] === 'Inscrito'): ?>
                                <form method="post" action="php/estado_inscripcion.php" class="d-inline">
                                    <input type="hidden" name="id_oferta" value="<?php echo $idOferta; ?>">
                                    <input type="hidden" name="id_usuario" value="<?php echo $user['id_usuario']; ?>">
                                    <input type="hidden" name="accion" value="Seleccionado">
                                    <button type="submit" class="btn btn-sm btn-success">Aceptar</button>
                                </form>
                                <form method="post" action="php/estado_inscripcion.php" class="d-inline">
                                    <input type="hidden" name="id_oferta" value="<?php echo $idOferta; ?>">
                                    <input type="hidden" name="id_usuario" value="<?php echo $user['id_usuario']; ?>">
                                    <input type="hidden" name="accion" value="Rechazado">
                                    <button type="submit" class="btn btn-sm btn-danger">Rechazar</button>
                                </form>
                            <?php else: ?>
                                <span class="badge <?php echo $user['estado']==='Seleccionado' ? 'bg-success' : 'bg-danger'; ?>">
                                    <?php echo $user['estado']; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay usuarios inscritos aún.</p>
            <?php endif; ?>
        </div>

    </div>





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
                        <i class="bi bi-envelope"></i> soporte@skillswap.com
                    </p>
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
