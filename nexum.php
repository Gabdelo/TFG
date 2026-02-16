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
} else {
    // Usuario no logueado
    $nombre = "Invitado";
    // opcional: redirigir al login
    // header("Location: login.php"); exit;
}


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
    <title>Document</title>
</head>

<body>
    <nav class="navbar-custom">
        <div class="nav-container">

            <!-- LOGO -->
            <a href="index.html" class="nav-logo">
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
                        <p class="profile-role">Desarrollador Full Stack</p>

                        <div class="profile-stats mt-3">
                            <div>
                                <strong>12</strong>
                                <span>Proyectos</span>
                            </div>
                            <div>
                                <strong>34</strong>
                                <span>Conexiones</span>
                            </div>
                        </div>

                        <a href="perfil.php" class="btn btn-sm btn-nexum mt-3 w-100">
                            Ver perfil
                        </a>

                    </div>
                    <div class="side-card p-3 mb-4">
                        <h5>Filtrar por</h5>
                        <ul class="list-unstyled mt-3">
                            <li><a href="#">Programación</a></li>
                            <li><a href="#">Diseño</a></li>
                            <li><a href="#">Marketing</a></li>
                            <li><a href="#">Edición de video</a></li>
                        </ul>
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

                    <?php
                    $idUsuario = $_SESSION['id_usuario'] ?? 0;
                    $tipoUsuario = $_SESSION['tipo_usuario'] ?? '';

                    // Conectar a DB si aún no lo has hecho
                    $conn = conectar();

                    // =====================
                    // 1️⃣ Sección: Mis ofertas (ofertas creadas por mí)
                    // =====================
                    if ($tipoUsuario === 'buscar' || $tipoUsuario === 'ambos') {
                        echo '<h4 class="mb-3">Mis ofertas publicadas</h4>';

                        $sqlOfertasPropias = "SELECT o.*, 
                                 (SELECT COUNT(*) FROM inscripciones_oferta i WHERE i.id_oferta = o.id_oferta) AS num_inscritos
                          FROM ofertas_trabajo o
                          WHERE o.id_usuario = ?
                          ORDER BY o.fecha_publicacion DESC";
                        $stmt = $conn->prepare($sqlOfertasPropias);
                        $stmt->bind_param("i", $idUsuario);
                        $stmt->execute();
                        $resultado = $stmt->get_result();

                        if ($resultado->num_rows > 0) {
                            while ($fila = $resultado->fetch_assoc()) {
                    ?>
                                <div class="post-card p-4 mb-4">
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
                                        <span class="badge bg-primary">Inscritos: <?php echo $fila['num_inscritos']; ?></span>
                                    </div>

                                    <!-- Botón para ver inscritos -->
                                    <?php if ($fila['num_inscritos'] > 0): ?>
                                        <form method="get" action="Ver_inscritos.php" class="mt-2">
                                            <input type="hidden" name="id_oferta" value="<?php echo $fila['id_oferta']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Ver inscritos</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php
                            }
                        } else {
                            echo "<p>No tienes ofertas publicadas.</p>";
                        }
                    }

                    // =====================
                    // 2️⃣ Sección: Mis inscripciones (ofertas a las que me he inscrito)
                    // =====================
                    if ($tipoUsuario === 'ofrecer' || $tipoUsuario === 'ambos') {
                        echo '<h4 class="mb-3">Ofertas a las que estoy inscrito</h4>';

                        $sqlInscritas = "SELECT o.*, u.nombre, u.foto_perfil
                 FROM inscripciones_oferta i
                 JOIN ofertas_trabajo o ON i.id_oferta = o.id_oferta
                 JOIN usuarios u ON o.id_usuario = u.id_usuario
                 WHERE i.id_usuario = ?
                 ORDER BY o.fecha_publicacion DESC";
                        $stmt2 = $conn->prepare($sqlInscritas);
                        $stmt2->bind_param("i", $idUsuario);
                        $stmt2->execute();
                        $resultadoInscritas = $stmt2->get_result();

                        if ($resultadoInscritas->num_rows > 0) {
                            while ($fila = $resultadoInscritas->fetch_assoc()) {

                                $fotoOfertante = !empty($fila['foto_perfil'])
                                    ? "uploads/" . $fila['foto_perfil']
                                    : "assets/img/default-avatar.png";
                            ?>
                                <div class="post-card p-4 mb-4">

                                    <!-- Cabecera clickeable (foto + nombre) -->
                                    <a href="VerPerfil.php?id=<?php echo $fila['id_usuario']; ?>"
                                        class="d-flex align-items-center mb-3 text-decoration-none text-dark">

                                        <div style="width:50px; height:50px; border-radius:50%; overflow:hidden;">
                                            <img src="<?php echo htmlspecialchars($fotoOfertante); ?>"
                                                style="width:100%; height:100%; object-fit:cover;">
                                        </div>

                                        <div class="ms-3">
                                            <strong><?php echo htmlspecialchars($fila['nombre']); ?></strong><br>
                                            <small class="text-muted">
                                                <?php echo date("d M Y", strtotime($fila['fecha_publicacion'])); ?>
                                            </small>
                                        </div>
                                    </a>

                                    <!-- Info oferta -->
                                    <h5><?php echo htmlspecialchars($fila['titulo']); ?></h5>
                                    <p><?php echo htmlspecialchars($fila['descripcion']); ?></p>

                                    <div class="mt-2">
                                        <span class="badge bg-warning text-dark">
                                            <?php echo htmlspecialchars($fila['tipo_contrato']); ?>
                                        </span>
                                        <span class="badge bg-secondary">
                                            <?php echo htmlspecialchars($fila['modalidad']); ?>
                                        </span>
                                    </div>

                                    <!-- Botón Inscrito (cancelar inscripción) -->
                                    <div class="mt-3">
                                        <form method="post" action="php/inscribirse_oferta.php">
                                            <input type="hidden" name="id_oferta" value="<?php echo $fila['id_oferta']; ?>">
                                            <button type="submit" name="accion" class="btn btn-secondary btn-sm">
                                                Inscrito
                                            </button>
                                        </form>
                                    </div>

                                </div>
                    <?php
                            }
                        } else {
                            echo "<p>No te has inscrito a ninguna oferta.</p>";
                        }
                    }
                    ?>
                </section>

                <!-- 🔹 COLUMNA DERECHA -->
                <aside class="col-lg-3 d-none d-lg-block">

                    <!-- Tarjeta usuarios destacados / consejos -->
                    <div class="side-card p-3 mb-4">
                        <h5>Usuarios destacados</h5>
                        <div class="d-flex align-items-center mb-3">
                            <div class="mini-avatar"></div>
                            <span class="ms-2">Laura Dev</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="mini-avatar"></div>
                            <span class="ms-2">Mario UX</span>
                        </div>
                    </div>

                    <div class="side-card p-3 mb-4">
                        <h5>Consejo del día</h5>
                        <p>Completa tu perfil al 100% para tener más visibilidad.</p>
                    </div>

                    <!-- 🔹 OFERTAS DE EMPLEO / INSCRIPCIONES -->


                </aside>

            </div>
        </div>
    </main>
    <footer class="footer mt-0 pt-5 pb-4">
        <div class="container">
            <div class="row gy-4">

                <!-- LOGO Y DESCRIPCIÓN -->
                <div class="col-lg-4 col-md-6">
                    <h4 class="footer-logo">SkillSwap</h4>
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
                © 2026 SkillSwap. Todos los derechos reservados.
            </div>
        </div>
    </footer>


</body>

</html>