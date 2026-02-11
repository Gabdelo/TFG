<?php
require 'includes/conexionDB.php'; // tu archivo de conexión a DB
$conn = conectar();

  session_start(); // Siempre al inicio, antes de usar $_SESSION

if (isset($_SESSION['id_usuario'])) { // el nombre correcto según login
    $idUsuario = $_SESSION['id_usuario'];
    $nombre    = $_SESSION['nombre'];
} else {
    // Usuario no logueado
    $nombre = "Invitado";
    // opcional: redirigir al login
    // header("Location: login.php"); exit;
} 
$id_usuario = $_SESSION['id_usuario']; // asegúrate que tienes la sesión iniciada

$sql = "SELECT * FROM proyectos 
        WHERE id_usuario = ? 
        ORDER BY fecha_creacion DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="assets/img/nexusIcon.png" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> <!--bootstrap 5 CSS-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"><!--bootstrap 5 icons-->
    <link type="text/css" rel="stylesheet" href="assets/css/perfil.css">
    <link type="text/css" rel="stylesheet" href="assets/css/global.css">
    <title>Perfil</title>
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
                <li><button><a href="index.php#Patrocinadores">MENU</a></button></li>
                <li><button id="btn-login">INICIAR SESIÓN</button></li>
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
                            <div class="perfil-foto"></div>
                        </div>

                        <div class="col-md-9">
                            <div class="d-flex justify-content-end gap-2 mb-2">
        <button class="btn btn-outline-primary btn-sm">
            <i class="bi bi-pencil-square"></i> Editar perfil
        </button>

        <button class="btn btn-primary btn-sm" id="btnSubirPublicacion" >
            <i class="bi bi-plus-circle"></i> Subir publicación
        </button>
    </div>
                            <h1 class="perfil-nombre"><?php echo "".$nombre."" ?></h1>
                            <p class="perfil-ubicacion">
                                <i class="bi bi-geo-alt"></i> Madrid · Online
                            </p>
                            <span class="badge bg-warning text-dark">
                                Ofrece y busca habilidades
                            </span>
                        </div>
                    </div>

                </div>

                <!-- HABILIDADES -->
                <div class="perfil-card p-4 mt-4">
                    <h4><i class="bi bi-lightning-charge"></i> Habilidades que ofrece</h4>

                    <div class="habilidad-item mt-3">
                        <div class="d-flex justify-content-between">
                            <span>Programación</span>
                            <span class="nivel">Avanzado</span>
                        </div>
                        <div class="barra-nivel avanzado"></div>
                    </div>

                </div>

                <!-- PREFERENCIAS -->
                <div class="perfil-card p-4 mt-4">
                    <h4><i class="bi bi-clock"></i> Preferencias</h4>
                    <p><strong>Tipo de intercambio:</strong> Proyecto</p>
                    <p><strong>Disponibilidad:</strong> Tardes entre semana</p>
                </div>

                <div class="perfil-card p-4 mt-4">
                <h4><i class="bi bi-folder"></i> Proyectos / Publicaciones</h4>

    <?php if($resultado->num_rows > 0): ?>
        
        <?php while($proyecto = $resultado->fetch_assoc()): ?>
            
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

                <?php if(!empty($proyecto['enlace'])): ?>
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

<div id="modalOverlay" class="modal-overlay d-none">
    <div class="modal-content">
        <span id="cerrarModal" class="cerrar">&times;</span>
        <h3>Subir publicación / Proyecto</h3>
        <form id="formPublicacion" action="php/subirPublicacion.php" method="post" enctype="multipart/form-data">
            
            <!-- TÍTULO -->
            <div class="mb-3">
                <label>Título</label>
                <input type="text" name="titulo" class="form-control" placeholder="Ej. App de gestión" required>
            </div>

            <!-- DESCRIPCIÓN -->
            <div class="mb-3">
                <label>Descripción</label>
                <textarea name="descripcion" class="form-control" rows="2" placeholder="Describe el proyecto y tu rol" required></textarea>
            </div>

            <!-- TIPO DE PROYECTO -->
            <div class="mb-3">
                <label>Tipo de proyecto</label>
                <select name="tipo_proyecto" class="form-select" required>
                    <option value="" selected disabled>Selecciona una opción</option>
                    <option value="Personal">Personal</option>
                    <option value="Académico">Académico</option>
                    <option value="Profesional">Profesional</option>
                    <option value="Colaborativo">Colaborativo</option>
                </select>
            </div>

            <!-- ROL -->
            <div class="mb-3">
                <label>Tu rol</label>
                <input type="text" name="rol" class="form-control" placeholder="Ej. Desarrollador / Diseñador" required>
            </div>


            <!-- MODALIDAD -->
            <div class="mb-3">
                <label>Modalidad</label>
                <select name="modalidad" class="form-select" required>
                    <option value="" selected disabled>Selecciona una opción</option>
                    <option value="Online">Online</option>
                    <option value="Presencial">Presencial</option>
                    <option value="Mixto">Mixto</option>
                </select>
            </div>

            <!-- FECHAS -->
            <div class="row g-3 mb-3">
                <div class="col">
                    <label>Fecha inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control">
                </div>
                <div class="col">
                    <label>Fecha fin</label>
                    <input type="date" name="fecha_fin" class="form-control">
                </div>
            </div>

            <!-- ENLACE -->
            <div class="mb-3">
                <label>Enlace (opcional)</label>
                <input type="url" name="enlace" class="form-control" placeholder="Ej. https://github.com/mi-proyecto">
            </div>

            <!-- BOTÓN -->
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Subir proyecto</button>
            </div>

        </form>
    </div>
</div>
<footer class="footer mt-5 pt-5 pb-4">
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
<script>
    const btnAbrir = document.getElementById('btnSubirPublicacion');
const modalOverlay = document.getElementById('modalOverlay');
const btnCerrar = document.getElementById('cerrarModal');

btnAbrir.addEventListener('click', () => {
    modalOverlay.classList.remove('d-none');
});

btnCerrar.addEventListener('click', () => {
    modalOverlay.classList.add('d-none');
});

// Cerrar al hacer clic fuera del modal
modalOverlay.addEventListener('click', (e) => {
    if (e.target === modalOverlay) {
        modalOverlay.classList.add('d-none');
    }
});
</script>
</html>