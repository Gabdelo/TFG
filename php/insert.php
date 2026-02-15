<?php
$conn = new mysqli("localhost", "root", "", "nexum_db");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$usuarios = [
    ["Ana López", "ana.lopez@email.com", "pass123", "Madrid", "Online", "buscar", "Lunes a Viernes 9-17", "Clases / mentoría"],
    ["Carlos Pérez", "carlos.perez@email.com", "pass123", "Barcelona", "Presencial", "ofrecer", "Fines de semana", "Proyecto"],
    ["Lucía Gómez", "lucia.gomez@email.com", "pass123", "Valencia", "Ambas", "ambos", "Martes y Jueves", "Puntual"],
    ["David Martínez", "david.martinez@email.com", "pass123", "Sevilla", "Online", "buscar", "Flexible", "Clases / mentoría"],
    ["Marta Fernández", "marta.fernandez@email.com", "pass123", "Bilbao", "Presencial", "ofrecer", "Lunes y Miércoles", "Proyecto"],
    ["Javier Ruiz", "javier.ruiz@email.com", "pass123", "Zaragoza", "Ambas", "ambos", "Fines de semana", "Puntual"],
    ["Sofía Sánchez", "sofia.sanchez@email.com", "pass123", "Málaga", "Online", "buscar", "Flexible", "Clases / mentoría"],
    ["Miguel Torres", "miguel.torres@email.com", "pass123", "Granada", "Presencial", "ofrecer", "Martes y Jueves", "Proyecto"],
    ["Laura Ramírez", "laura.ramirez@email.com", "pass123", "Salamanca", "Ambas", "ambos", "Lunes a Viernes", "Puntual"],
    ["Diego Moreno", "diego.moreno@email.com", "pass123", "Alicante", "Online", "buscar", "Fines de semana", "Clases / mentoría"],
    ["Elena Jiménez", "elena.jimenez@email.com", "pass123", "Valladolid", "Presencial", "ofrecer", "Flexible", "Proyecto"],
    ["Andrés Castillo", "andres.castillo@email.com", "pass123", "Santander", "Ambas", "ambos", "Martes y Jueves", "Puntual"],
    ["Isabel Ortiz", "isabel.ortiz@email.com", "pass123", "Toledo", "Online", "buscar", "Lunes a Viernes", "Clases / mentoría"],
    ["Fernando Herrera", "fernando.herrera@email.com", "pass123", "Córdoba", "Presencial", "ofrecer", "Flexible", "Proyecto"],
    ["Patricia Molina", "patricia.molina@email.com", "pass123", "Murcia", "Ambas", "ambos", "Fines de semana", "Puntual"],
    ["Raúl Navarro", "raul.navarro@email.com", "pass123", "Pamplona", "Online", "buscar", "Martes y Jueves", "Clases / mentoría"],
    ["Verónica Rubio", "veronica.rubio@email.com", "pass123", "Vigo", "Presencial", "ofrecer", "Lunes a Viernes", "Proyecto"],
    ["Sergio Méndez", "sergio.mendez@email.com", "pass123", "Girona", "Ambas", "ambos", "Flexible", "Puntual"],
    ["Natalia Campos", "natalia.campos@email.com", "pass123", "Oviedo", "Online", "buscar", "Fines de semana", "Clases / mentoría"],
    ["Antonio Vega", "antonio.vega@email.com", "pass123", "Huelva", "Presencial", "ofrecer", "Martes y Jueves", "Proyecto"]
];

$stmt = $conn->prepare(
    "INSERT INTO usuarios
    (nombre, email, password, ciudad, modalidad, tipo_usuario, disponibilidad, tipo_intercambio)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);

foreach ($usuarios as $u) {
    $hash = password_hash($u[2], PASSWORD_DEFAULT);
    $stmt->bind_param("ssssssss", $u[0], $u[1], $hash, $u[3], $u[4], $u[5], $u[6], $u[7]);
    $stmt->execute();
}

$stmt->close();
$conn->close();

echo "Usuarios insertados correctamente con contraseñas hasheadas.";
?>
