<?php
$servername = "127.0.0.1";
$username = "root"; 
$password = "mysql12345!";
$database = "nexum_db"; 

function conectar(){
    global $servername, $username, $password, $database;

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    return $conn;
}
?>
