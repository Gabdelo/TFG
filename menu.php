<?php
require 'includes/conexionDB.php'; // tu archivo de conexión a DB
$conn = conectar();

$busqueda = "";

if (isset($_GET['busqueda']) && $_GET['busqueda'] != "") {
    
    $busqueda = $_GET['busqueda'];

    $sql = "
    SELECT DISTINCT u.*
    FROM usuarios u
    LEFT JOIN usuario_ofrece uo ON u.id_usuario = uo.id_usuario
    LEFT JOIN habilidades h ON uo.id_habilidad = h.id_habilidad
    LEFT JOIN proyectos p ON u.id_usuario = p.id_usuario
    WHERE 
        u.nombre LIKE '%$busqueda%' OR
        u.ciudad LIKE '%$busqueda%' OR
        h.nombre LIKE '%$busqueda%' OR
        p.titulo LIKE '%$busqueda%'
    ";

} else {
    $sql = "
    SELECT u.id_usuario,u.nombre, p.titulo, p.descripcion
    FROM proyectos p
    JOIN usuarios u ON p.id_usuario = u.id_usuario
    ORDER BY p.fecha_creacion DESC
    ";
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
    <nav class="container-fluid navegador bg-dark">
        <div class="container-fluid nav-content">
            <a href="index.html"><img class="logo" src="./assets/img/nexusIcon.png" alt=""></a>
            <button class="btn-nav"><span class="bi bi-list"></span></button>
            <ul class="nav-list">
                <li><button><a href="index.php#PROGRAMA">PROGRAMA</a></button></li>
                <li><button><a href="./mapa/mapa.php">STANDS</a></button></li>
                <li><button><a href="./empresas/empresas.php">RANKING</a></button></li>
                <li><button><a href="menu.html">MENU</a></button></li>
                <li><button id="btn-login">INICIAR SESIÓN</button></li>
            </ul>
        </div>
    </nav>
    <main class="container-fluid mt-5 pt-5">
    <div class="container py-4">
        <div class="row">

            <!-- 🔹 COLUMNA IZQUIERDA -->
            <aside class="col-lg-3 d-none d-lg-block">
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


              <?php while($fila = $resultado->fetch_assoc()) { ?>

<a href="Verperfil.php?id=<?php echo $fila['id_usuario']; ?>" 
   style="text-decoration:none; color:inherit;">

    <div class="post-card p-4 mb-4" style="cursor:pointer;">
        <div class="d-flex align-items-center mb-3">
            <div class="mini-avatar"></div>
            <div class="ms-3">
                <strong><?php echo htmlspecialchars($fila['nombre']); ?></strong>
            </div>
        </div>

        <?php if(isset($fila['titulo'])) { ?>
            <h5><?php echo htmlspecialchars($fila['titulo']); ?></h5>
            <p><?php echo htmlspecialchars($fila['descripcion']); ?></p>
        <?php } ?>
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