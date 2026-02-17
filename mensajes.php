<?php
require 'includes/conexionDB.php';
$conn = conectar();

session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}
if (isset($_SESSION['id_usuario'])) { // el nombre correcto según login
    $idUsuario = $_SESSION['id_usuario'];
    $nombre    = $_SESSION['nombre'];
    $foto = !empty($_SESSION['foto_perfil']) 
    ? 'uploads/' . $_SESSION['foto_perfil'] 
    : 'assets/img/default-avatar.png';
} 

$id_usuario = $_SESSION['id_usuario'];

// Si se selecciona un chat
$id_chat = isset($_GET['id']) ? (int) $_GET['id'] : null;
$usuario_chat = null;

if ($id_chat) {
    $sqlUserChat = "SELECT nombre, foto_perfil FROM usuarios WHERE id_usuario = ?";
    $stmtUserChat = $conn->prepare($sqlUserChat);
    $stmtUserChat->bind_param("i", $id_chat);
    $stmtUserChat->execute();
    $usuario_chat = $stmtUserChat->get_result()->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mensajes</title>
    <link rel="icon" href="assets/img/nexusIcon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--bootstrap 5 CSS-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!--bootstrap 5 icons-->
    <link type="text/css" rel="stylesheet" href="assets/css/styles.css">
    <link type="text/css" rel="stylesheet" href="assets/css/global.css">
    <link type="text/css" rel="stylesheet" href="assets/css/chat.css">

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
    <div class="container mt-5">
        <h2 class="mb-3">-</h2>
        <div class="chat-container">

            <!-- IZQUIERDA: Lista de chats -->
            <div class="usuarios-list">
                <?php
                // Obtener lista de usuarios con los que tienes mensajes
                $sqlUsuarios = "
    SELECT u.id_usuario, u.nombre, u.foto_perfil, MAX(m.fecha_envio) as ultima_fecha
    FROM usuarios u
    JOIN mensajes m ON (u.id_usuario = m.id_emisor AND m.id_receptor = ?) 
                     OR (u.id_usuario = m.id_receptor AND m.id_emisor = ?)
    WHERE u.id_usuario != ?
    GROUP BY u.id_usuario, u.nombre, u.foto_perfil
    ORDER BY ultima_fecha DESC
";
                $stmtUsuarios = $conn->prepare($sqlUsuarios);
                $stmtUsuarios->bind_param("iii", $id_usuario, $id_usuario, $id_usuario);
                $stmtUsuarios->execute();
                $resUsuarios = $stmtUsuarios->get_result();

                while ($user = $resUsuarios->fetch_assoc()):
                    $active = ($id_chat == $user['id_usuario']) ? 'bg-light' : '';
                    ?>
                    <a href="mensajes.php?id=<?php echo $user['id_usuario']; ?>" class="chat-user <?php echo $active; ?>">

                        <img src="uploads/<?php echo $user['foto_perfil'] ?: '../assets/img/default-avatar.png'; ?>"
                            class="chat-user-img">

                        <span><?php echo htmlspecialchars($user['nombre']); ?></span>
                    </a>
                <?php endwhile; ?>
            </div>

            <!-- DERECHA: Chat abierto -->
            <div class="chat-ventana">
                <?php if ($id_chat && $usuario_chat): ?>
                    <div class="chat-header">
                        <?php
                        $foto_chat = !empty($usuario_chat['foto_perfil'])
                            ? 'uploads/' . $usuario_chat['foto_perfil']
                            : 'assets/img/default-avatar.png';
                        ?>
                        <img src="<?php echo $foto_chat; ?>" class="chat-header-img">
                        <div>
                            <h6 class="mb-0"><?php echo htmlspecialchars($usuario_chat['nombre']); ?></h6>
                            <small class="text-muted">En línea</small>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="chat-mensajes" id="chat-mensajes">
                    <?php
                    if ($id_chat):
                        // Cargar mensajes entre usuarios
                        $sqlMensajes = "
                        SELECT * FROM mensajes 
                        WHERE (id_emisor = ? AND id_receptor = ?) 
                           OR (id_emisor = ? AND id_receptor = ?)
                        ORDER BY fecha_envio ASC
                    ";
                        $stmtMensajes = $conn->prepare($sqlMensajes);
                        $stmtMensajes->bind_param("iiii", $id_usuario, $id_chat, $id_chat, $id_usuario);
                        $stmtMensajes->execute();
                        $resMensajes = $stmtMensajes->get_result();

                        while ($msg = $resMensajes->fetch_assoc()):
                            $clase = ($msg['id_emisor'] == $id_usuario) ? 'enviado' : 'recibido';
                            ?>
                            <div class="mensaje <?php echo $clase; ?>">
                                <p><?php echo htmlspecialchars($msg['mensaje']); ?></p>
                                <small><?php echo date("H:i", strtotime($msg['fecha_envio'])); ?></small>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center text-muted mt-5">Selecciona un chat para empezar a chatear</p>
                    <?php endif; ?>
                </div>

                <?php if ($id_chat): ?>
                    <!-- Formulario enviar mensaje -->
                    <form id="formChat" class="form-chat">
                        <input type="hidden" name="id_receptor" value="<?php echo $id_chat; ?>">
                        <input type="text" name="mensaje" placeholder="Escribe un mensaje...">
                        <button type="submit" class="btn btn-primary btn-sm">Enviar</button>
                    </form>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <script>
        <?php if ($id_chat): ?>
            const formChat = document.getElementById('formChat');
            formChat.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(formChat);

                fetch('php/enviar_mensajes.php', {  // ruta correcta desde mensajes.php
                    method: 'POST',
                    body: formData
                })
                    .then(res => res.text())
                    .then(res => {
                        if (res == 'ok') {
                            formChat.reset();  // limpiar input
                            cargarMensajes();  // recargar chat
                        } else {
                            alert(res); // mostrar error si falla
                        }
                    });
            });

            function cargarMensajes() {
                fetch('php/chat.php?id=<?php echo $id_chat; ?>')
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('chat-mensajes').innerHTML = html;
                        // Mantener scroll abajo
                        const chatDiv = document.getElementById('chat-mensajes');
                        chatDiv.scrollTop = chatDiv.scrollHeight;
                    });
            }

            setInterval(cargarMensajes, 3000);
        <?php endif; ?>
    </script>
</body>

</html>