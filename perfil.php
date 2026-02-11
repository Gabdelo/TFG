<?php
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
    <main class="my-5">
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