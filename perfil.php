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
    <main>
        <section class="container row">
            

        </section>

    </main>
</body>
</html>