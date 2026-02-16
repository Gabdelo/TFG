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
    SELECT u.id_usuario, u.nombre, u.foto_perfil,
           p.titulo,
           p.descripcion,
           p.fecha_creacion AS fecha,
           'proyecto' AS tipo,
           NULL AS imagen
    FROM usuarios u
    LEFT JOIN proyectos p ON u.id_usuario = p.id_usuario
    LEFT JOIN usuario_ofrece uo ON u.id_usuario = uo.id_usuario
    LEFT JOIN habilidades h ON uo.id_habilidad = h.id_habilidad
    WHERE 
        u.nombre LIKE '%$busqueda%' OR
        u.ciudad LIKE '%$busqueda%' OR
        h.nombre LIKE '%$busqueda%' OR
        p.titulo LIKE '%$busqueda%'

    UNION ALL

    SELECT u.id_usuario, u.nombre, u.foto_perfil,
           NULL AS titulo,
           pub.descripcion,
           pub.fecha_creacion AS fecha,
           'publicacion' AS tipo,
           pub.imagen
    FROM usuarios u
    LEFT JOIN publicaciones pub ON u.id_usuario = pub.id_usuario
    WHERE 
        u.nombre LIKE '%$busqueda%' OR
        pub.descripcion LIKE '%$busqueda%'

    ORDER BY fecha DESC
    ";

} else {

    $sql = "
    SELECT u.id_usuario, u.nombre, u.foto_perfil,
           p.titulo,
           p.descripcion,
           p.fecha_creacion AS fecha,
           'proyecto' AS tipo,
           NULL AS imagen
    FROM proyectos p
    JOIN usuarios u ON p.id_usuario = u.id_usuario

    UNION ALL

    SELECT u.id_usuario, u.nombre, u.foto_perfil,
           NULL AS titulo,
           pub.descripcion,
           pub.fecha_creacion AS fecha,
           'publicacion' AS tipo,
           pub.imagen
    FROM publicaciones pub
    JOIN usuarios u ON pub.id_usuario = u.id_usuario

    ORDER BY fecha DESC
    ";
}

if (empty($sql)) {
    die("Error en la consulta SQL.");
}

$resultado = $conn->query($sql);

if (!$resultado) {
    die("Error en la consulta: " . $conn->error);
}

$resultado = $conn->query($sql);
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
                <a href="empleos.php">
                    <i class="bi bi-briefcase-fill"></i>
                    <span>Empleos</span>
                </a>
            </li>
            <li>
                <a href="personas.php">
                    <i class="bi bi-person-fill"></i>
                    <span>Personas</span>
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

    <h5 class="mt-3 mb-1"><?php echo "".$nombre."" ?></h5>
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

                <!-- 🔍 BUSCADOR -->
                                    <div class="search-box p-3 mb-4">
                        <form method="GET" action="menu.php">
                            <input 
                                type="text" 
                                name="busqueda" 
                                class="form-control" 
                                placeholder="Buscar perfiles, habilidades o proyectos..."
                                value="<?php echo isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : ''; ?>"
                            >
                        </form>
                    </div>

                 <?php while($fila = $resultado->fetch_assoc()) { 

    $fotoPost = !empty($fila['foto_perfil']) 
        ? "uploads/" . $fila['foto_perfil'] 
        : "assets/img/default-avatar.png";
?>

<a href="VerPerfil.php?id=<?php echo $fila['id_usuario']; ?>" 
   style="text-decoration:none; color:inherit;">

    <div class="post-card p-4 mb-4" style="cursor:pointer;">
        <div class="d-flex align-items-center mb-3">

            <div class="mini-avatar"
                 style="background-image: url('<?php echo htmlspecialchars($fotoPost); ?>');">
            </div>

            <div class="ms-3">
                <strong><?php echo htmlspecialchars($fila['nombre']); ?></strong>
                <br>
                <small><?php echo $fila['tipo'] === 'proyecto' ? 'Proyecto' : 'Publicación'; ?></small>
            </div>

        </div>

        <?php if($fila['tipo'] === 'proyecto') { ?>
            <h5><?php echo htmlspecialchars($fila['titulo']); ?></h5>
        <?php } ?>

        

        <?php if($fila['tipo'] === 'publicacion' && !empty($fila['imagen'])): ?>
            <div class="mt-2">
                <img src="uploads/<?php echo htmlspecialchars($fila['imagen']); ?>" 
                     style="max-width:100%; border-radius:10px;" 
                     alt="Imagen de publicación">
            </div>
            <p class="mt-2" ><?php echo htmlspecialchars($fila['descripcion']); ?></p>
        <?php endif; ?>

    </div>

</a>

<?php } ?>




              

                <!-- 📢 PUBLICACIÓN -->
                <div class="post-card p-4 mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="mini-avatar"></div>
                        <div class="ms-3">
                            <strong>Ana López</strong>
                            <p class="small text-muted m-0">Desarrolladora Frontend</p>
                        </div>
                    </div>
                    <p>Busco diseñador UX/UI para colaborar en una app educativa 🚀</p>
                    <button class="btn btn-sm btn-outline-dark">Ver proyecto</button>
                </div>

                <div class="post-card p-4 mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="mini-avatar"></div>
                        <div class="ms-3">
                            <strong>Carlos Ruiz</strong>
                            <p class="small text-muted m-0">Editor de video</p>
                        </div>
                    </div>
                    <p>Ofrezco edición profesional para proyectos tecnológicos.</p>
                    <button class="btn btn-sm btn-outline-dark">Contactar</button>
                </div>

            </section>


            <!-- 🔹 COLUMNA DERECHA -->
            <aside class="col-lg-3 d-none d-lg-block">
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

                <div class="side-card p-3">
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