<?php

//echo "auth.php cargado"; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function loginUser($user) {
    $_SESSION['id_usuario']   = $user['id_usuario'];
    $_SESSION['nombre']       = $user['nombre'];
    $_SESSION['email']        = $user['email'];
    $_SESSION['tipo_usuario'] = $user['tipo_usuario'];
    $_SESSION['foto_perfil'] = $user['foto_perfil'];
    $_SESSION['ciudad'] = $user['ciudad'];
    $_SESSION['disponibilidad'] = $user['disponibilidad'];
    $_SESSION['tipo_intercambio'] = $user['tipo_intercambio'];
}

function isLoggedIn() {
    return isset($_SESSION['id_usuario']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

function logoutUser() {
    session_unset();
    session_destroy();
    header("Location: ../index.html");
    exit;
}
